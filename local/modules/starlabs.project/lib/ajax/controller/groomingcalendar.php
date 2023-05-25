<?php

namespace Starlabs\Project\Ajax\Controller;

use Bitrix\Crm\ContactTable;
use Bitrix\Crm\DealTable;
use Bitrix\Crm\PhaseSemantics;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;
use Starlabs\Project\Calendar\Model\CalendarNotesTable;
use Starlabs\Project\Calendar\Notes;
use Starlabs\Project\Grooming;
use Starlabs\Project\Iblock;
use Starlabs\Project\SmartProcess;
use Starlabs\Project\Personal\Master;
use Starlabs\Project\WorkSchedule\Model\WorkScheduleTable;
use Starlabs\Tools\Ajax\Controller\Prototype;
use Starlabs\Project\Helpers\Utils;
use Starlabs\Tools\Helpers\Log;
use Starlabs\Tools\Helpers\p;

class GroomingCalendar extends Prototype
{
    public function fetchInitDataAction()
    {
        $this->returnAsIs = true;
        $arSalons = [];
        $arMasters = [];
        $Salon = new Iblock\Salon();
        $SalonsQuery = $Salon->createQuery()
            ->setSelect(["ID", "NAME"])
            ->setFilter(["ACTIVE" => "Y"])
            ->exec();
        while ($arSalon = $SalonsQuery->fetch()) {
            $arSalons[] = [
                "name" => $arSalon["NAME"],
                "id"   => $arSalon["ID"]
            ];
        }

        $Master = new Master();
        $mastersData = $Master->getAllMasters();
        foreach ($mastersData as $arMaster) {
            $arMasters[] = [
                "name" => $arMaster["NAME"] . ' ' . $arMaster["LAST_NAME"],
                "id"   => $arMaster["ID"]
            ];
        }
        Context::getCurrent()->getResponse()->addHeader('Access-Control-Allow-Origin', "*");
        return [
            "salons"  => $arSalons,
            "masters" => $arMasters
        ];
    }

    public function fetchHeaderDataAction()
    {
        $this->returnAsIs = true;
        $arRequest = Utils::getRequestBodyContent();
        $salon = $arRequest["salon"];
        if (is_array($salon)) {
            $salonId = $salon["id"];
        } else {
            $salonId = $salon;
        }
        $arMasters = $arRequest["master"];
        $Query = WorkScheduleTable::query()
            ->setSelect(
                [
                    "MASTER_LAST_NAME"    => "MASTER.LAST_NAME",
                    "MASTER_NAME"         => "MASTER.NAME",
                    "ASSISTANT_LAST_NAME" => "ASSISTANT.LAST_NAME",
                    "ASSISTANT_NAME"      => "ASSISTANT.NAME",
                    "COLOR"               => "MASTER." . Master::COLOR_FIELD_CODE,
                    "MASTER_ID",
                    "WORK_DATE"
                ]
            )
            ->addFilter("SALON_ID", $salonId);
        if ($arMasters) {
            $Query->addFilter('MASTER_ID', $arMasters);
        }
        $Result = $Query->exec();
        $arResult = [];
        while ($arWorkday = $Result->fetch()) {
            $arResult[] = [
                "name"      => $arWorkday["MASTER_NAME"] . ' ' . $arWorkday["MASTER_LAST_NAME"],
                "id"        => $arWorkday["MASTER_ID"],
                "color"     => $arWorkday["COLOR"],
                "assistant" => ($arWorkday["ASSISTANT_NAME"]) ? $arWorkday["ASSISTANT_NAME"] . ' ' . $arWorkday["ASSISTANT_LAST_NAME"] : '',
                "date"      => Utils::formatDateToJs($arWorkday["WORK_DATE"])
            ];
        }
        Context::getCurrent()->getResponse()->addHeader('Access-Control-Allow-Origin', "*");
        Context::getCurrent()->getResponse()->addHeader('Access-Control-Allow-Methods', ["POST", "GET", "OPTIONS"]);
        return $arResult;
    }

    public function getCalendarEventsAction()
    {
        $this->returnAsIs = true;
        try {
            $arRequest = Utils::getRequestBodyContent();
            if (is_array($arRequest["salon"])) {
                $salonId = $arRequest["salon"]["id"];
            } else {
                $salonId = $arRequest["salon"];
            }
            $arMasters = $arRequest["master"];
            Loader::includeModule('crm');
            Loader::includeModule('tasks');
            $BreedsQuery = (new Iblock\Breed())->createQuery();
            $BreedRes = $BreedsQuery
                ->setSelect(["ID", "NAME"])
                ->setFilter(["ACTIVE" => "Y"])
                ->exec();
            while ($arBreed = $BreedRes->fetch()) {
                $arBreeds[$arBreed["ID"]] = $arBreed["NAME"];
            }
            $Pets = new SmartProcess\Pets();
            $DateStart = new DateTime();
            $DateStart->add('-1 month');
            $Query = \Bitrix\Crm\DealTable::query()
                ->setSelect([
                                "ID",
                                "PRODUCT.OWNER_ID",
                                "CONTACT_NAME"     => "CONTACT.NAME",
                                "CONTACT_PHONE"    => "CONTACT.PHONE",
                                Grooming\Deal::FIELD_PET_CODE,
                                Grooming\Deal::FIELD_MASTER_CODE,
                                Grooming\Deal::FIELD_SALON_CODE,
                                Grooming\Deal::FIELD_MASTER_TIME_FINISH_CODE,
                                Grooming\Deal::FIELD_MASTER_TIME_START_CODE,
                                Grooming\Deal::FIELD_TIME_FINISH_CODE,
                                Grooming\Deal::FIELD_TIME_START_CODE,
                                Grooming\Deal::FIELD_USE_CASHBACK_CODE,
                                Grooming\Deal::FIELD_CASHBACK_CODE,
                                "SERVICE_NAME"     => "PRODUCT.PRODUCT_NAME",
                                "SERVICE_ID"       => "PRODUCT.PRODUCT_ID",
                                "MASTER_NAME"      => "MASTER.NAME",
                                "MASTER_LAST_NAME" => "MASTER.LAST_NAME",
                                "MASTER_COLOR"     => "MASTER." . Master::COLOR_FIELD_CODE,
                                "PET_NAME"         => "PET.TITLE",
                                "PET_ID"           => "PET.ID",
                                "PET_BREED_ID"     => "PET." . $Pets->getUfBreedCode(),
                                "OPPORTUNITY",
                                "CONTACT_ID",
                                "COMMENTS",
                                "STAGE_ID"
                            ])
                ->registerRuntimeField(
                    'PRODUCT',
                    [
                        'data_type' => \Bitrix\Crm\ProductRowTable::getEntity(),
                        'reference' => ['=this.ID' => 'ref.OWNER_ID']
                    ]
                )
                ->registerRuntimeField(
                    'MASTER',
                    [
                        'data_type' => \Bitrix\Main\UserTable::getEntity(),
                        'reference' => ['=this.' . Grooming\Deal::FIELD_MASTER_CODE => 'ref.ID']
                    ]
                )
                ->registerRuntimeField(
                    'PET',
                    [
                        'data_type' => $Pets->getFactory()->getDataClass(),
                        'reference' => ['=this.' . Grooming\Deal::FIELD_PET_CODE => 'ref.ID']
                    ]
                )
                ->setFilter([
                                "CATEGORY_ID"                   => Grooming\Deal::GROOMING_CATEGORY_ID,
                                Grooming\Deal::FIELD_SALON_CODE => $salonId,
                                "PRODUCT.OWNER_TYPE"            => "D",
//                                "!STAGE_SEMANTIC_ID" => "F", TODO: Поправить фильтр по стадиям
                                "STAGE_ID" => [Grooming\Deal::getNewStatusId(), Grooming\Deal::getWonStatusId()],
                                ">" . Grooming\Deal::FIELD_TIME_START_CODE => $DateStart
                            ]);
            if ($arMasters) {
                $Query->addFilter(Grooming\Deal::FIELD_MASTER_CODE, $arMasters);
            }

            $QueryRes = $Query->exec();
            $arResult = [];
            while ($arDeal = $QueryRes->fetch()) {
                if (!$arResult[$arDeal["ID"]]) {
                    $arResult[$arDeal["ID"]] = [
                        "id"            => $arDeal["ID"],
                        "stage"         => $arDeal["STAGE_ID"],
                        "type"          => "event",
                        "price"         => (int)$arDeal["OPPORTUNITY"],
                        "start"         => $arDeal[Grooming\Deal::FIELD_TIME_START_CODE]->format('Y-m-d\TH:i:s.000Z'),
                        "finish"        => $arDeal[Grooming\Deal::FIELD_TIME_FINISH_CODE]->format('Y-m-d\TH:i:s.000Z'),
                        "master_start"  => $arDeal[Grooming\Deal::FIELD_MASTER_TIME_START_CODE]->format(
                            'Y-m-d\TH:i:s.000Z'
                        ),
                        "master_finish" => $arDeal[Grooming\Deal::FIELD_MASTER_TIME_FINISH_CODE]->format(
                            'Y-m-d\TH:i:s.000Z'
                        ),
                        "service"       => [
                            [
                                "name" => $arDeal["SERVICE_NAME"],
                                "id"   => $arDeal["SERVICE_ID"],
                            ]
                        ],
                        "master"        => [
                            "name"  => $arDeal["MASTER_NAME"] . ' ' . $arDeal["MASTER_LAST_NAME"],
                            "color" => $arDeal["MASTER_COLOR"],
                            "id"    => $arDeal[Grooming\Deal::FIELD_MASTER_CODE]
                        ],
                        "client"        => [
                            "name"  => $arDeal["CONTACT_NAME"],
                            "phone" => $arDeal["CONTACT_PHONE"],
                            "id"    => $arDeal["CONTACT_ID"],
                        ],
                        "pet"           => [
                            "name"  => $arDeal["PET_NAME"],
                            "breed" => $arBreeds[$arDeal["PET_BREED_ID"]],
                            "id"    => $arDeal["PET_ID"],
                        ],
                        "comment"       => $arDeal["COMMENTS"],
                        "salesList" => [],
                    ];
                    $Cashback = new Grooming\Cashback($arDeal["ID"], $arDeal);
                    //TODO: DRY

                    if ($arDeal["STAGE_ID"] == Grooming\Deal::getWonStatusId()) {
                        $arResult[$arDeal["ID"]]["disabled"] = true;
                        if ($arDeal[Grooming\Deal::FIELD_USE_CASHBACK_CODE] > 0 && $arDeal[Grooming\Deal::FIELD_CASHBACK_CODE] > 0) {
                            $arResult[$arDeal["ID"]]["salesList"] = [
                                [
                                    "id"         => $Cashback::SALE_ID,
                                    "name"       => $Cashback::SALE_NAME,
                                    "changeable" => false,
                                    "measure"    => "number",
                                    "min"        => 0,
                                    "max"        => $arDeal[Grooming\Deal::FIELD_CASHBACK_CODE],
                                    "limit"      => $Cashback::CASHBACK_DISCOUNT_PERCENT,
                                    "value"      => $arDeal[Grooming\Deal::FIELD_CASHBACK_CODE],
                                ],
                            ];
                        }
                    } elseif (
                        ($cashbackBalance = $Cashback->getAvailableCashback()) &&
                        $arDeal[Grooming\Deal::FIELD_USE_CASHBACK_CODE]
                    ) {
                        $arResult[$arDeal["ID"]]["salesList"] = [
                            [
                                "id"         => $Cashback::SALE_ID,
                                "name"       => $Cashback::SALE_NAME,
                                "changeable" => false,
                                "measure"    => "number",
                                "min"        => 0,
                                "max"        => $cashbackBalance,
                                "limit"      => $Cashback::CASHBACK_DISCOUNT_PERCENT,
                                "value"      => $Cashback->getAvailableCashback(),
                            ],
                        ];
                    }

                } else {
                    $arResult[$arDeal["ID"]]["service"][] = [
                        "name" => $arDeal["SERVICE_NAME"],
                        "id"   => $arDeal["SERVICE_ID"],
                    ];
                }
            }
            //region ищем задачи для сделок

            foreach ($arResult as $dealId => $dealData) {
                if ($dealData["stage"] == Grooming\Deal::getWonStatusId()) { //TODO: Сделать более универсальную отработку окраса карточек календаря
                    $arCardTasksFilter["UF_CRM_TASK"][] = 'D_' . $dealId;
                }
            }

            if ($arCardTasksFilter) {
                $arCardTasksFilter["!STATUS"] = 5;
                $arCardTasksFilter["TAG"] = [Grooming\Tasks::TAG_SHOW_TASK_IN_CARD];
                $arCardTasksFilter["GROUP_ID"] = [Grooming\Tasks::GROUP_CUSTOMERS_COMMUNICATION_ID];
                $arCardTasksOrder = [];
                $arCardTasksSelect = [
                    "ID",
                    "TITLE",
                    "UF_CRM_TASK"
                ];
                $CardTasksRes = \CTasks::GetList($arCardTasksOrder, $arCardTasksFilter, $arCardTasksSelect);
                while ($arCardTask = $CardTasksRes->Fetch()) {
                    foreach ($arCardTask["UF_CRM_TASK"] as $string) {
                        $arString = explode('_', $string);
                        if ($arString[0] == \CCrmOwnerTypeAbbr::Deal) {
                            $cardTaskDealId = $arString[1];
                            $arResult[$cardTaskDealId]["color"] = "#ff0000";
                            break;
                        }
                    }
                }
            }
            //endregion
            $arResult = array_merge($arResult, Notes::getNotesBySalonId($salonId));
        } catch (\Throwable $e) {
            return [$e->getMessage(), $e->getTrace(), $arDeal];
        }

        return array_values($arResult);
    }

    public function getServicesByPetAction()
    {
        $this->returnAsIs = true;
        $arRequest = Utils::getRequestBodyContent();
        $arResult = [];
        try {
            if ($petId = $arRequest["id"]) {
                $QueryProducts = (new Iblock\Products())->createQuery();
                $Pets = new SmartProcess\Pets();
                $Filter = new ConditionTree();
                $Filter->logic('or');
                $Filter->where(
                    [
                        ['PET_ID', '=', $petId],
                        ['SECTION_DATA.CODE', '=', Iblock\Products::GENERAL_SECTION_CODE]
                    ]
                );
                $QueryProductsRes = $QueryProducts
                    ->setSelect(["ID", "NAME", "BREED_" => "BREED", "PET_ID" => "PETS.ID"])
                    ->registerRuntimeField(
                        'PETS',
                        [
                            'data_type' => $Pets->getFactory()->getDataClass(),
                            'reference' => ['=this.BREED_VALUE' => 'ref.' . $Pets->getUfBreedCode()]
                        ]
                    )
                    ->registerRuntimeField(
                        'SECTION_DATA',
                        [
                            'data_type' => SectionTable::getEntity(),
                            'reference' => ['=this.IBLOCK_SECTION_ID' => 'ref.ID']
                        ]
                    )
                    ->where($Filter)
                    ->exec();
                while ($arProduct = $QueryProductsRes->fetch()) {
                    $arResult[] = [
                        "id"   => $arProduct["ID"],
                        "name" => $arProduct["NAME"]
                    ];
                }
                if (!$arResult) {
                    $arResult = ["error" => "Услуги не найдены"];
                }
            }
        } catch (\Exception $e) {
            $arResult = $e->getMessage();
        }
        return $arResult;
    }

    /**
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * TODO: ПРОВЕРКА ЗАНЯТОГО ВРЕМЕНИ
     */
    public function getMastersAction()
    {
        try {
            $this->returnAsIs = true;
            $arRequest = Utils::getRequestBodyContent();
            Loader::includeModule('crm');
            if (is_array($arRequest["salon"])) {
                $salonId = $arRequest["salon"]["id"];
            } else {
                $salonId = $arRequest["salon"];
            }
            $arServices = $arRequest["service"];
            $Products = new Iblock\Products();
            $arNeedSkillServices = [];
            foreach ($arServices as $serviceId) {
                if (!$Products->isGeneralProduct($serviceId)) {
                    $arNeedSkillServices[] = $serviceId;
                }
            }
            $date = new \Bitrix\Main\Type\DateTime($arRequest["date"], 'Y-m-d\Th:i:s');
            $workDay = new \Bitrix\Main\Type\Date($date->format('Y-m-d'), 'Y-m-d');

            $Query = WorkScheduleTable::query()
                ->setSelect(
                    [
                        "MASTER_ID",
                        "MASTER_LAST_NAME" => "MASTER.LAST_NAME",
                        "MASTER_NAME"      => "MASTER.NAME",
                        "MASTER_COLOR"     => "MASTER." . Master::COLOR_FIELD_CODE,
                    ]
                )
                ->setFilter(
                    [
                        "SALON_ID"  => $salonId,
                        "WORK_DATE" => $workDay,
                    ]
                )
                ->exec();
            $arMasters = [];
            while ($arSchedule = $Query->fetch()) {
                $arMasters[$arSchedule["MASTER_ID"]] = $arSchedule;
            }

            $MasterSkills = new SmartProcess\MasterSkills();

            $SkillsQuery = $MasterSkills->createQuery()
                ->setSelect(["ID", $MasterSkills->getUfServiceCode(), $MasterSkills->getUfMasterCode()])
                ->setFilter(
                    [
                        $MasterSkills->getUfMasterCode()  => array_keys($arMasters),
                        $MasterSkills->getUfServiceCode() => $arNeedSkillServices
                    ]
                )
                ->exec();
            $arResult = [];

            while ($arSkill = $SkillsQuery->fetch()) {
                $masterData = $arMasters[$arSkill[$MasterSkills->getUfMasterCode()]];
                $arResult[] = [
                    "name"  => $masterData["MASTER_NAME"] . ' ' . $masterData["MASTER_LAST_NAME"],
                    "color" => $masterData["MASTER_COLOR"],
                    "id"    => $masterData["MASTER_ID"]
                ];
            }
            if (!$arNeedSkillServices) {
                foreach ($arMasters as $arMaster) {
                    $arResult[] = [
                        "name"  => $arMaster["MASTER_NAME"] . ' ' . $arMaster["MASTER_LAST_NAME"],
                        "color" => $arMaster["MASTER_COLOR"],
                        "id"    => $arMaster["MASTER_ID"]
                    ];
                }
            }
            return $arResult;
        } catch (\Throwable $exception) {
            return [$exception->getMessage(), $exception->getTrace()];
        }
    }

    public function getPriceAction()
    {
        $this->returnAsIs = true;
        try {
            Loader::includeModule('crm');
            Loader::includeModule('tasks');
            $arRequest = Utils::getRequestBodyContent();
            $salonId = $arRequest["salon"];//TODO: кажется не работает вся логика, т.к. приходит объект а не id
            $arServicesId = $arRequest["service"];
            $masterId = $arRequest["master"]; //TODO: masterid не приходит, разобраться что надо поправить и зачем он задумывался
            $petId = $arRequest["pet"];
            $dealId = $arRequest["dealId"];
            $isNew = $arRequest["isNew"];
            $disabled = $arRequest["disabled"];
            $activeSales = $arRequest["activeSales"];
            $arContact = $arRequest["client"];

            $date = new \Bitrix\Main\Type\DateTime($arRequest["date"], 'Y-m-d\Th:i:s');

            $MasterSkills = new SmartProcess\MasterSkills();
            $Master = new Master();
            $Products = new Iblock\Products();
            $GeneralProductsQuery = $Products->createQuery()
                ->setSelect(
                    [
                        "ID",
                        "BASE_DURATION_" => $Products::PROP_BASE_DURATION_CODE,
                        "SECTION_CODE"   => "SECTION_DATA.CODE"
                    ]
                )
                ->setFilter(["ID" => $arServicesId, "SECTION_CODE" => $Products::GENERAL_SECTION_CODE])
                ->registerRuntimeField(
                    'SECTION_DATA',
                    [
                        'data_type' => SectionTable::getEntity(),
                        'reference' => ['=this.IBLOCK_SECTION_ID' => 'ref.ID']
                    ]
                )
                ->exec();
            $duration = 0;

            while ($arProduct = $GeneralProductsQuery->fetch()) {
                $duration += $arProduct["BASE_DURATION_VALUE"];
            }
            $Salon = new Iblock\Salon();
            $isPremiumSalon = $Salon->isPremium($salonId);
            $isTopMaster = $Master->isTop($masterId);
            $price = 0;
            $arSkillMessages = [];
            $MasterSkills = new SmartProcess\MasterSkills();
            foreach ($arServicesId as $serviceId) {
                if (!$Products->isGeneralProduct($serviceId)) {
                    $SkillsQuery = $MasterSkills->createQuery()
                        ->setSelect(
                            [
                                "ID",
                                $MasterSkills->getUfServiceCode(),
                                $MasterSkills->getUfMasterCode(),
                                $MasterSkills->getUfCommentCode()
                            ]
                        )
                        ->setFilter(
                            [
                                $MasterSkills->getUfMasterCode()  => $masterId,
                                $MasterSkills->getUfServiceCode() => $serviceId
                            ]
                        )
                        ->exec();
                    if ($skillMessage = $SkillsQuery->fetch()[$MasterSkills->getUfCommentCode()]) {
                        $arSkillMessages[] = $skillMessage;
                    }
                }
                $price += $Products->getPrice($serviceId, $isTopMaster, $isPremiumSalon);
            }
            $SkillQuery = $MasterSkills->createQuery()
                ->setSelect([$MasterSkills->getUfDurationCode()])
                ->setFilter(
                    [$MasterSkills->getUfMasterCode() => $masterId, $MasterSkills->getUfServiceCode() => $arServicesId]
                )
                ->exec();
            if ($arSkill = $SkillQuery->fetch()) {
                $duration += $MasterSkills->getDurationFromMaskString($arSkill[$MasterSkills->getUfDurationCode()]);
            } else {
//                throw new \Exception("Мастер с id = $masterId не может выполнять услугу с id = $serviceId");
            }

            $messageHtml = '<div>';

            foreach ($arSkillMessages as $message) {
                $messageHtml .= '<p>' . $message . '</p>';
            }
            $Pet = new SmartProcess\Pets();
            if ($petId) {
                $PetItem = $Pet->getFactory()->getItem($petId);
            }
            if ($PetItem) {
                $petCashback = $PetItem->get($Pet->getUfCashbackCode());
            }
            $salesList = [];
            global $USER;
            if ($dealId) {
                $arCardTasks = Grooming\Tasks::getCardTasksByDealId($dealId);
                if ($arCardTasks) {
                    $messageHtml .= "<div style='margin-bottom: 5px'>Задачи по данному клиенту:<ol>";
                    foreach ($arCardTasks as $cardTask) {
                        $messageHtml .= "<li><a href='/company/personal/user/" . $USER->GetID() . "/tasks/task/view/" . $cardTask["ID"] . "/'>" . $cardTask["TITLE"] . "</a></li>";
                    }
                    $messageHtml .= "</ol></div>";
                }
            }
            if ($petCashback) {
                if (!$activeSales && !$disabled) {
                    $salesList[] = [
                        "id" => Grooming\Cashback::SALE_ID,
                        "name" => Grooming\Cashback::SALE_NAME,
                        "changeable" => false,
                        "measure" => "number",
                        "min" => 0,
                        "max" => $petCashback,
                        "limit" => Grooming\Cashback::CASHBACK_DISCOUNT_PERCENT,
                        "value" => $petCashback
                    ];
                }
                $messageHtml .= "<p><span style='font-weight: bold'>Баланс кэшбек-баллов</span> у клиента: $petCashback</p>";
            }

            $contactData = ContactTable::query()
                ->setSelect(["ID", "NAME", "COMMENTS"])
                ->setFilter(["ID" => $arContact["id"]])
                ->exec()->fetch();

            if ($contactData["COMMENTS"]) {
                $messageHtml .= "<p>Комментарий о клиенте - " . $contactData["COMMENTS"] . " </p>";
            }

            $messageHtml .= '</div>';

            $arResult = [
                "price"    => $price,
                "duration" => $duration,
                "message"  => $messageHtml,
                "salesList" => $salesList,
            ];
            return $arResult;
        } catch (\Throwable $exception) {
            return [$exception->getMessage(), $exception->getTrace()];
        }
    }

    public function addEventAction()
    {
        try {
            Loader::includeModule('crm');
            $this->returnAsIs = true;
            $arRequest = Utils::getRequestBodyContent();
            if (is_array($arRequest["salon"])) {
                $salonId = $arRequest["salon"]["id"];
            } else {
                $salonId = $arRequest["salon"];
            }
            $arMaster = $arRequest["master"];
            $arPet = $arRequest["pet"];
            $arServices = $arRequest["service"];
            $arClient = $arRequest["client"];
            $startTime = Utils::getDateTimeFromJs($arRequest["start"]);
            $finishTime = Utils::getDateTimeFromJs($arRequest["finish"]);
            $masterStart = Utils::getDateTimeFromJs($arRequest["master_start"]);
            $masterFinish = Utils::getDateTimeFromJs($arRequest["master_finish"]);
            $activeSales = $arRequest["activeSales"];
            $comment = $arRequest["comment"];
            global $USER;
            if ($USER->GetID() != 1) {
//                Grooming\Deal::verifyDealTime($startTime->format('d.m.Y H:i:s'), $finishTime->format('d.m.Y H:i:s'), $masterStart->format('d.m.Y H:i:s'), $masterFinish->format('d.m.Y H:i:s'), $arMaster["id"]);
            }
            $price = $arRequest["price"];
            $needAssistant = $arRequest["isAssistentNeeded"];

            $Deal = new \CCrmDeal();

            $arFields = [
                Grooming\Deal::FIELD_MASTER_CODE             => $arMaster["id"],
                Grooming\Deal::FIELD_SALON_CODE              => $salonId,
                Grooming\Deal::FIELD_PET_CODE                => $arPet["id"],
                "CONTACT_ID"                                 => $arClient["id"],
                Grooming\Deal::FIELD_TIME_START_CODE         => $startTime->format('d.m.Y H:i:s'),
                Grooming\Deal::FIELD_TIME_FINISH_CODE        => $finishTime->format('d.m.Y H:i:s'),
                Grooming\Deal::FIELD_MASTER_TIME_START_CODE  => $masterStart->format('d.m.Y H:i:s'),
                Grooming\Deal::FIELD_MASTER_TIME_FINISH_CODE => $masterFinish->format('d.m.Y H:i:s'),
                "CATEGORY_ID"                                => Grooming\Deal::GROOMING_CATEGORY_ID,
                "OPPORTUNITY"                                => $price,
                "COMMENTS"                                   => $comment,
                Grooming\Deal::FIELD_USE_CASHBACK_CODE => true
            ];

            $Master = new Master();
            $Salon = new Iblock\Salon();
            $Products = new Iblock\Products();
            $isPremiumSalon = $Salon->isPremium($salonId);
            $isTopMaster = $Master->isTop($arMaster["id"]);

            if ($dealId = $Deal->Add($arFields)) {
                foreach ($arServices as $arService) {
                    $strServices .= $arService["name"] . '<br>';
                    $productRows[] = [
                        "PRODUCT_ID"   => $arService["id"],
                        "PRODUCT_NAME" => $arService["name"],
                        "PRICE"        => $Products->getPrice($arService["id"], $isTopMaster, $isPremiumSalon)
                    ];
                }
                $assistantId = [];
                if ($needAssistant) {
                    $scheduleRes = WorkScheduleTable::query()
                        ->setSelect(["MASTER_ID", "ASSISTANT_ID", "WORK_DATE"])
                        ->setFilter(
                            [
                                "MASTER_ID" => $arMaster["id"],
                                "WORK_DATE" => new DateTime($finishTime->format('Y-m-d'), 'Y-m-d')
                            ]
                        )
                        ->exec();
                    if ($arRecord = $scheduleRes->fetch()) {
                        $assistantId = [$arRecord['ASSISTANT_ID']];
                        Log::AddEvent($arRecord);
                    }
                }

                \CCrmDeal::SaveProductRows($dealId, $productRows, true, true, false);
                global $USER;

                $arTaskFields = [
                    "TITLE"           => "Клиент - " . $arClient["name"] . ' Питомец - ' . $arPet["name"] . ' (' . $arPet["breed"] . ')',
                    "DESCRIPTION"     => "Клиент - " . $arClient["name"] . '<br>' .
                        'Питомец - ' . $arPet["name"] . ' (' . $arPet["breed"] . ')<br>' .
                        'Услуги:<br>' . $strServices . '<br>' .
                        'Отведенное время: с ' . $startTime->format('H:i') . ' по ' . $finishTime->format(
                            'H:i'
                        ) . '<br>' .
                        'Сумма - ' . $price . '<br>' .
                        'Комментарий от администратора: ' . $comment,
                    "RESPONSIBLE_ID"  => $arMaster["id"],
                    "UF_CRM_TASK"     => ["D_" . $dealId],
                    "GROUP_ID"        => Grooming\Tasks::GROUP_APPOINTMENT_GROOMING_ID,
                    "START_DATE_PLAN" => $startTime,
                    "END_DATE_PLAN"   => $finishTime,
                    "DEADLINE"        => $startTime,
                    "ACCOMPLICES"     => $assistantId,
                ];

                $res = \CTaskItem::add($arTaskFields, $USER->GetID());
            }
            return ["id" => $dealId];
        } catch (\Throwable $exception) {
            return ["error" => $exception->getMessage()];
        }
    }

    public function winDealAction()
    {
        $this->returnAsIs = true;
        $arRequest = Utils::getRequestBodyContent();
        Loader::includeModule('crm');
        $dealId = $arRequest["id"];
        $salesList = $arRequest["salesList"];
        $Deal = new \CCrmDeal();
        try {
            $arFields = [
                "STAGE_ID" => Grooming\Deal::getWonStatusId()
            ];
            foreach ($salesList as $arSale) {
                if ($arSale["id"] == Grooming\Cashback::SALE_ID) {
                    $arFields[Grooming\Deal::FIELD_USE_CASHBACK_CODE] = true;
                }
            }
            $res = $Deal->Update($dealId, $arFields);
            if ($Deal->LAST_ERROR) {
                return ["error" => $res . $Deal->LAST_ERROR];
            }
        } catch (\Throwable $e) {
            return ["error" => $e->getMessage()];
        }

        return ["id" => $dealId];
    }

    public function getAnimalTypesAction()
    {
        $this->returnAsIs = true;
        $AnimalTypes = new Iblock\AnimalTypes();
        $QueryRes = $AnimalTypes->createQuery()
            ->setSelect(["ID", "NAME"])
            ->exec();
        $arResult = [];
        while ($arType = $QueryRes->fetch()) {
            $arResult[] = [
                "name" => $arType["NAME"],
                "id"   => $arType["ID"]
            ];
        }
        return $arResult;
    }

    public function getBreedsAction()
    {
        $this->returnAsIs = true;
        $arRequest = Utils::getRequestBodyContent();
        $animalTypeId = $arRequest["id"];
        $Breed = new Iblock\Breed();
        $QueryRes = $Breed->createQuery()
            ->setSelect(["ID", "NAME", "ANIMAL_TYPE_" => $Breed::PROP_PET_TYPE_CODE])
            ->setFilter(["ANIMAL_TYPE_VALUE" => $animalTypeId])
            ->exec();
        $arResult = [];
        while ($arBreed = $QueryRes->fetch()) {
            $arResult[] = [
                "name" => $arBreed["NAME"],
                "id"   => $arBreed["ID"]
            ];
        }
        return $arResult;
    }

    public function getSourcesAction()
    {
        Loader::includeModule('crm');
        $this->returnAsIs = true;
        $statusQuery = \Bitrix\Crm\StatusTable::query()
            ->setFilter(["ENTITY_ID" => \Bitrix\Crm\StatusTable::ENTITY_ID_SOURCE])
            ->setSelect(["STATUS_ID", "NAME"])
            ->setOrder(["SORT" => "ASC"])
            ->exec();
        while ($arStatus = $statusQuery->fetch()) {
            $arResult[] = [
                "name" => $arStatus["NAME"],
                "id"   => $arStatus["STATUS_ID"]
            ];
        }
        return $arResult;
    }

    public function deleteEventAction()
    {
        Loader::includeModule('crm');
        Loader::includeModule('tasks');
        $this->returnAsIs = true;
        $arRequest = Utils::getRequestBodyContent();
        $dealId = $arRequest["id"];
        $taskId = Grooming\Tasks::getGroomingTaskIdByDealId($dealId);

        $TaskItem = new \CTaskItem($taskId, 1);
        $TaskItem->complete();

        $Deal = new \CCrmDeal();
        $arFields = [//TODO: вот тут семантик дописать
            "STAGE_ID" => Grooming\Deal::getDeletedStatusId(),
            "STAGE_SEMANTIC_ID" => PhaseSemantics::FAILURE
        ];
        $Deal->Update($dealId, $arFields);
        return true;
    }

    public function addContactAction()
    {
        $this->returnAsIs = true;
        $arRequest = Utils::getRequestBodyContent();
        Loader::includeModule('crm');
        $contactName = $arRequest["name"];
        $contactPhone = $arRequest["phone"];
        $arPets = $arRequest["pets"];
        $sourceId = $arRequest["source"];
        $Contact = new \CCrmContact();
        $Pets = new SmartProcess\Pets();
        $arContactPets = [];
        foreach ($arPets as $arPet) {
            $arContactPets[] = $Pets->add(
                [
                    "TITLE"                   => $arPet["name"],
                    $Pets->getUfBreedCode()   => $arPet["breed"],
                    $Pets->getUfPetTypeCode() => $arPet["type"]
                ]
            );
        }
        $arContactFields = [
            "NAME"                           => $contactName,
            "TITLE"                          => $contactName,
            "SOURCE_ID"                      => $sourceId,
            'FM'                             => [
                'PHONE' => [
                    'n0' => [
                        'VALUE_TYPE' => 'WORK',
                        'VALUE'      => $contactPhone
                    ]
                ]
            ],
            Grooming\Contact::FIELD_PET_CODE => $arContactPets
        ];
        return ["id" => $Contact->Add($arContactFields)];
    }

    public function updateContactAction()
    {
        $this->returnAsIs = true;
        $arRequest = Utils::getRequestBodyContent();
        Loader::includeModule('crm');
        $contactId = $arRequest["id"];
        $phone = $arRequest["phone"];
        $name = $arRequest["name"];
        $arPets = $arRequest["pets"];
        $Contact = new \CCrmContact();
        $Pets = new SmartProcess\Pets();
        $arContactPets = [];
        foreach ($arPets as $arPet) {
            if (!$arPet["id"]) {
                $arNewPets[] = $arPet;
                continue;
            }
            if ($arPet["deleted"]) {
                $arDeletedPets[$arPet["id"]] = $arPet;
            } else {
                $arUpdatedPets[] = $arPet;
            }
        }

        if ($arDeletedPets) {
            $checkDeletedPetsQuery = DealTable::query()
                ->setFilter(["CATEGORY_ID" => Grooming\Deal::GROOMING_CATEGORY_ID, Grooming\Deal::FIELD_PET_CODE => array_keys($arDeletedPets)])
                ->setSelect(["ID", Grooming\Deal::FIELD_TIME_START_CODE, Grooming\Deal::FIELD_SALON_CODE, Grooming\Deal::FIELD_PET_CODE])
                ->exec();
            $errorDelPets = '';
            while ($arDeal = $checkDeletedPetsQuery->fetch()) {
                $errorDelPets .= 'Питомец ' . $arDeletedPets[$arDeal[Grooming\Deal::FIELD_PET_CODE]]["name"] . ' записан на стрижку ' . $arDeal[Grooming\Deal::FIELD_TIME_START_CODE]->format('d.m.Y в H:i') . ". ";
            }

            if ($errorDelPets) {
                $error = "Произошла ошибка! " . $errorDelPets . 'Отмените данные записи и затем удалите питомца';
                return ["error" => $error];
            } else {
                foreach ($arDeletedPets as $id =>$arDeletedPet) {
                    $Pets->getObjectById($id)->delete();
                }
            }
        }

        foreach ($arNewPets as $arNewPet) {
            $arContactPets[] = $Pets->add(
                [
                    "TITLE"                   => $arNewPet["name"],
                    $Pets->getUfBreedCode()   => $arNewPet["breed"],
                    $Pets->getUfPetTypeCode() => $arNewPet["type"]
                ]
            );
        }

        foreach ($arUpdatedPets as $arUpdatedPet) {
            $petObj = $Pets->getObjectById($arUpdatedPet["id"]);
            $petObj->set('TITLE', $arUpdatedPet["name"]);
            $petObj->set($Pets->getUfBreedCode(), $arUpdatedPet["breed"]);
            $petObj->set($Pets->getUfPetTypeCode(), $arUpdatedPet["type"]);
            $petObj->save();
            $arContactPets[] = $arUpdatedPet["id"];
        }


        $arContactFields = [
            "NAME"                           => $name,
            "TITLE"                          => $name,
            Grooming\Contact::FIELD_PET_CODE => $arContactPets
        ];
        $res = \Bitrix\Crm\FieldMultiTable::query()
            ->setFilter(
                [
                    "ENTITY_ID"  => \CCrmOwnerType::ContactName,
                    "TYPE_ID"    => \CCrmFieldMulti::PHONE,
                    "ELEMENT_ID" => $contactId,
                    "VALUE"      => $phone
                ]
            )
            ->setSelect(["VALUE"])
            ->exec();
        if (!$res->fetch()) {
            $arContactFields["FM"] = [
                'PHONE' => [
                    'n0' => [
                        'VALUE_TYPE' => 'WORK',
                        'VALUE'      => $phone
                    ]
                ]
            ];
        }
        $Contact->Update($contactId, $arContactFields);


        return [true];
    }

    public function editEventAction()
    {
        try {
            Loader::includeModule('crm');
            $this->returnAsIs = true;
            $arRequest = Utils::getRequestBodyContent();
            if (is_array($arRequest["salon"])) {
                $salonId = $arRequest["salon"]["id"];
            } else {
                $salonId = $arRequest["salon"];
            }
            $arMaster = $arRequest["master"];
            $arPet = $arRequest["pet"];
            $arServicesId = $arRequest["service"];
            $arClient = $arRequest["client"];
            $startTime = Utils::getDateTimeFromJs($arRequest["start"]);
            $finishTime = Utils::getDateTimeFromJs($arRequest["finish"]);
            $masterStart = Utils::getDateTimeFromJs($arRequest["master_start"]);
            $masterFinish = Utils::getDateTimeFromJs($arRequest["master_finish"]);
            $dealId = $arRequest["id"];
            $activeSales = $arRequest["activeSales"];

            Grooming\Deal::verifyDealTime($startTime, $finishTime, $masterStart, $masterFinish, $arMaster["id"], $dealId);

            $price = $arRequest["price"];
            $comment = $arRequest["comment"];
            $needAssistant = $arRequest["isAssistentNeeded"];

            $Deal = new \CCrmDeal();

            $arFields = [
                Grooming\Deal::FIELD_MASTER_CODE             => $arMaster["id"],
                Grooming\Deal::FIELD_SALON_CODE              => $salonId,
                Grooming\Deal::FIELD_PET_CODE                => $arPet["id"],
                "CONTACT_ID"                                 => $arClient["id"],
                Grooming\Deal::FIELD_TIME_START_CODE         => $startTime->format('d.m.Y H:i:s'),
                Grooming\Deal::FIELD_TIME_FINISH_CODE        => $finishTime->format('d.m.Y H:i:s'),
                Grooming\Deal::FIELD_MASTER_TIME_START_CODE  => $masterStart->format('d.m.Y H:i:s'),
                Grooming\Deal::FIELD_MASTER_TIME_FINISH_CODE => $masterFinish->format('d.m.Y H:i:s'),
                "CATEGORY_ID"                                => Grooming\Deal::GROOMING_CATEGORY_ID,
                "COMMENTS"                                   => $comment,
                Grooming\Deal::FIELD_USE_CASHBACK_CODE => false
            ];
            foreach ($activeSales as $arSale) {
                if ($arSale["id"] == Grooming\Cashback::SALE_ID) {
                    $arFields[Grooming\Deal::FIELD_USE_CASHBACK_CODE] = true;
                }
            }
            $Products = new Iblock\Products();
            $ProductsQuery = $Products->createQuery()
                ->setSelect(
                    [
                        "ID",
                        "NAME"
                    ]
                )
                ->setFilter(["ID" => $arServicesId])
                ->exec();
            while ($arProduct = $ProductsQuery->fetch()) {
                $arServices[] = [
                    "id"   => $arProduct["ID"],
                    "name" => $arProduct["NAME"]
                ];
            }
            $Master = new Master();
            $Salon = new Iblock\Salon();
            $isPremiumSalon = $Salon->isPremium($salonId);
            $isTopMaster = $Master->isTop($arMaster["id"]);

            if ($Deal->Update($dealId, $arFields)) {
                foreach ($arServices as $arService) {
                    $productRows[] = [
                        "PRODUCT_ID"   => $arService["id"],
                        "PRODUCT_NAME" => $arService["name"],
                        "PRICE"        => $Products->getPrice($arService["id"], $isTopMaster, $isPremiumSalon)
                    ];
                    $strServices .= $arService["name"] . '<br>';
                }

                \CCrmDeal::SaveProductRows($dealId, $productRows);
                $arOpportunity = [
                    "OPPORTUNITY" => $price
                ];
                $Deal->Update($dealId, $arOpportunity);
                global $USER;
                $assistantId = [];
                if ($needAssistant) {
                    $scheduleRes = WorkScheduleTable::query()
                        ->setSelect(["MASTER_ID", "ASSISTANT_ID", "WORK_DATE"])
                        ->setFilter(
                            [
                                "MASTER_ID" => $arMaster["id"],
                                "WORK_DATE" => new Date($finishTime->format('Y-m-d'), 'Y-m-d')
                            ]
                        )
                        ->exec();
                    if ($arRecord = $scheduleRes->fetch()) {
                        $assistantId = [$arRecord['ASSISTANT_ID']];
                    }
                }
                $arTaskFields = [
                    "TITLE"           => "Клиент - " . $arClient["name"] . ' Питомец - ' . $arPet["name"] . ' (' . $arPet["breed"] . ')',
                    "DESCRIPTION"     => "Клиент - " . $arClient["name"] . '<br>' .
                        'Питомец - ' . $arPet["name"] . ' (' . $arPet["breed"] . ')<br>' .
                        'Услуги:<br>' . $strServices . '<br>' .
                        'Отведенное время: с ' . $startTime->format('H:i') . ' по ' . $finishTime->format(
                            'H:i'
                        ) . '<br>' .
                        'Сумма - ' . $price . '<br>' .
                        'Комментарий от администратора: ' . $comment,
                    "RESPONSIBLE_ID"  => $arMaster["id"],
                    "START_DATE_PLAN" => $startTime->format('d.m.Y+H:i:s'),
                    "END_DATE_PLAN"   => $finishTime->format('d.m.Y+H:i:s'),
                    "DEADLINE"        => $startTime->format('d.m.Y+H:i:s'),
                    "ACCOMPLICES"     => $assistantId,
                ];
                $taskId = Grooming\Tasks::getGroomingTaskIdByDealId($dealId);

                $taskItem = new \CTaskItem($taskId, 1);

                $taskItem->update($arTaskFields);
                Log::AddEvent([$arTaskFields, $arRequest]);
            } else {
                return ["error" => $Deal->LAST_ERROR];
            }
            return ["id" => $dealId];

        } catch (\Throwable $exception) {
            return ["error" => $exception->getMessage()];
        }
    }

    public function addNoteAction()
    {
        try {
            $this->returnAsIs = true;
            $arRequest = Utils::getRequestBodyContent();
            if (is_array($arRequest["salon"])) {
                $salonId = $arRequest["salon"]["id"];
            } else {
                $salonId = $arRequest["salon"];
            }
            $arMaster = $arRequest["master"];
            $start = Utils::getDateTimeFromJs($arRequest["start"]);;
            $finish = Utils::getDateTimeFromJs($arRequest["finish"]);
            $description = $arRequest["description"];
            $title = $arRequest["title"];
            $arFields = [
                "MASTER_ID"   => $arMaster["id"],
                "TIME_START"  => $start,
                "TIME_FINISH" => $finish,
                "DESCRIPTION" => $description,
                "TITLE"       => $title,
                "SALON_ID"    => $salonId
            ];
            $Note = new Notes();
            return ["id" => $Note->add($arFields)];
        } catch (\Throwable $exception) {
            return ["error" => $exception->getMessage()];
        }
    }

    public function editNoteAction()
    {
        try {
            $this->returnAsIs = true;
            $arRequest = Utils::getRequestBodyContent();
            if (is_array($arRequest["salon"])) {
                $salonId = $arRequest["salon"]["id"];
            } else {
                $salonId = $arRequest["salon"];
            }
            $noteId = $arRequest["id"];
            $arMaster = $arRequest["master"];
            $start = Utils::getDateTimeFromJs($arRequest["start"]);;
            $finish = Utils::getDateTimeFromJs($arRequest["finish"]);
            $description = $arRequest["description"];
            $title = $arRequest["title"];
            $arData = [
                "MASTER_ID"   => $arMaster["id"],
                "TIME_START"  => $start,
                "TIME_FINISH" => $finish,
                "DESCRIPTION" => $description,
                "TITLE"       => $title,
                "SALON_ID"    => $salonId
            ];
            $Note = new Notes();
            return ["id" => $Note->update($noteId, $arData)];
        } catch (\Throwable $exception) {
            return ["error" => $exception->getMessage()];
        }
    }

    public function deleteNoteAction()
    {
        try {
            $this->returnAsIs = true;
            $arRequest = Utils::getRequestBodyContent();
            $noteId = $arRequest["id"];
            $Note = new Notes();
            return $Note->delete($noteId);
        } catch (\Throwable $exception) {
            return ["error" => $exception->getMessage()];
        }
    }

    public function dragElementAction()
    {
        try {
            $this->returnAsIs = true;
            $arRequest = Utils::getRequestBodyContent();
            $elementId = $arRequest["id"];
            $type = $arRequest["type"];
            $start = Utils::getDateTimeFromJs($arRequest["start"]);
            $finish = Utils::getDateTimeFromJs($arRequest["finish"]);

            switch ($type) {
                case 'event':
                    $arDealSelect = [
                        "ID",
                        "STAGE_ID",
                        Grooming\Deal::FIELD_TIME_START_CODE,
                        Grooming\Deal::FIELD_TIME_FINISH_CODE,
                        Grooming\Deal::FIELD_MASTER_TIME_START_CODE,
                        Grooming\Deal::FIELD_MASTER_TIME_FINISH_CODE
                    ];

                    $oldDeal = DealTable::query()
                        ->setSelect($arDealSelect)
                        ->setFilter(["ID" => $elementId])
                        ->fetch();

                    if ($oldDeal["STAGE_ID"] == Grooming\Deal::getWonStatusId()) {
                        throw new \Exception('Сделка уже завершена, ее изменение невозможно');
                    }

                    $eventDuration = $oldDeal[Grooming\Deal::FIELD_TIME_FINISH_CODE]->getDiff($oldDeal[Grooming\Deal::FIELD_TIME_START_CODE]);
                    $eventDurationMinutes = $eventDuration->h*60 + $eventDuration->i;
                    $masterDuration = $oldDeal[Grooming\Deal::FIELD_MASTER_TIME_FINISH_CODE]->getDiff($oldDeal[Grooming\Deal::FIELD_MASTER_TIME_START_CODE]);
                    $masterDurationMinutes = $masterDuration->h*60 + $masterDuration->i;

                    if ($masterDurationMinutes < $eventDurationMinutes) {
                        $masterStart = clone $finish;
                        $masterStart->add('-' . $masterDurationMinutes . ' minutes');
                    } else {
                        $masterStart = $start;
                    }

                    $arDealFields = [
                        Grooming\Deal::FIELD_TIME_START_CODE         => $start->format('d.m.Y H:i:s'),
                        Grooming\Deal::FIELD_TIME_FINISH_CODE        => $finish->format('d.m.Y H:i:s'),
                        Grooming\Deal::FIELD_MASTER_TIME_START_CODE  => $masterStart->format('d.m.Y H:i:s'),
                        Grooming\Deal::FIELD_MASTER_TIME_FINISH_CODE => $finish->format('d.m.Y H:i:s'),
                    ];
                    $Deal = new \CCrmDeal();
                    $Deal->Update($elementId, $arDealFields);

                    $taskId = Grooming\Tasks::getGroomingTaskIdByDealId($elementId);
                    $oldTask = \CTaskItem::getInstance($taskId, 1);
                    $arOldData = $oldTask->getData();

                    $repl = 'Отведенное время: с ' . $start->format('H:i') . ' по ' . $finish->format(
                            'H:i'
                        );
                    $newDesc = preg_replace('/Отведенное время: с \d\d:\d\d по \d\d:\d\d/', $repl, $arOldData["DESCRIPTION"]);

                    $arTaskFields = [
                        "DESCRIPTION"     => $newDesc,
                        "START_DATE_PLAN" => $start->format('d.m.Y+H:i:s'),
                        "END_DATE_PLAN"   => $finish->format('d.m.Y+H:i:s'),
                        "DEADLINE"        => $start->format('d.m.Y+H:i:s'),
                    ];
                    $oldTask->update($arTaskFields);
                    break;
                case 'note':
                default:
                    throw new \Exception('Перемещать можно только записи на стрижку');
            }
            return ["id" => $elementId];
        } catch (\Throwable $exception) {
            return ["error" => $exception->getMessage()];
        }
    }

}
