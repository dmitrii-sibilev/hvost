<?php
namespace Starlabs\Project\Ajax\Controller;

use Bitrix\Crm\DealTable;
use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;
use Starlabs\Project\Grooming\Deal;
use Starlabs\Project\Iblock\Salon;
use Starlabs\Project\Personal\Assistant;
use Starlabs\Project\Personal\Master;
use Starlabs\Project\WorkSchedule\Model\WorkScheduleTable;
use Starlabs\Project\WorkSchedule\ScheduleList;
use Starlabs\Tools\Ajax\Controller\Prototype;
use Starlabs\Tools\Helpers\Log;
use Starlabs\Tools\Helpers\Utils;

class WorkSchedule extends Prototype
{
	public function FetchDataAction()
	{
		$this->returnAsIs = true;
        try {
            global $USER;
            $Schedule = new ScheduleList();
            $arMasters = (new Master())->getAllMasters(["PERSONAL_PHOTO"]);
            //TODO: предусмотреть что стажер может быть и мастером и наоборот
            $arAssistants = (new Assistant())->getAllAssistants(["PERSONAL_PHOTO"]);
//            $assistansAsMasterId = [7];
            foreach ($arAssistants as $arUser) {
                if (in_array($arUser["ID"], $assistansAsMasterId)) {
                    $arMasters[] = $arUser;
                }
            }
            $arSchedule = $Schedule->getFullSchedule();
            $SalonsQuery = (new Salon())->createQuery()
                ->setSelect(["ID", "NAME", "MASTERS_COUNT_" => "MASTERS_COUNT"])
                ->setFilter(["ACTIVE" => "Y"])
                ->exec();
            $arSalons = $SalonsQuery->fetchAll();
            $arData = [
                "MASTERS" => $arMasters,
                "ASSISTANTS" => $arAssistants,
                "SALONS" => $arSalons,
                "SCHEDULE" => $arSchedule,
                "EDIT_RIGHTS" => $Schedule->canUserEditSchedule($USER->GetID())
            ];
        } catch (\Exception $e) {
            $arData["ERROR"] = $e->getMessage();
        }

		return $arData;
	}

    public function addAction()
    {
        $this->returnAsIs = true;
        $arRequest = Utils::getRequestBodyContent();
        $result = WorkScheduleTable::add(["fields" => [
            "MASTER_ID" => $arRequest["MASTER_ID"],
            "SALON_ID" => $arRequest["SALON_ID"],
            "ASSISTANT_ID" => $arRequest["ASSISTANT_ID"],
            "WORK_DATE" => new \Bitrix\Main\Type\Date($arRequest["DATE"],'Y-m-d\TH:i:s.000Z'),
        ]]);
        return $result->getId();
	}
    public function updateAction()
    {
        $this->returnAsIs = true;
        $arRequest = Utils::getRequestBodyContent();
        $Query = WorkScheduleTable::query()
            ->setFilter(["ID" => $arRequest["ID"]])
            ->setSelect(["*"])
            ->exec();
        if ($Element = $Query->fetchObject()) {
            $Element->set("MASTER_ID", $arRequest["MASTER_ID"]);
            $Element->set("SALON_ID", $arRequest["SALON_ID"]);
            $Element->set("ASSISTANT_ID", $arRequest["ASSISTANT_ID"]);
            $Element->set("WORK_DATE", new \Bitrix\Main\Type\Date($arRequest["DATE"],'Y-m-d\TH:i:s.000Z'));
            $res = $Element->save();
        }
        return true;
	}
    public function deleteAction()
    {
        $this->returnAsIs = true;
        $arRequest = Utils::getRequestBodyContent();
        $Query = WorkScheduleTable::query()
            ->setFilter(["ID" => $arRequest["ID"]])
            ->setSelect(["ID", "MASTER_ID", "WORK_DATE"])
            ->exec();
        if ($Element = $Query->fetchObject()) {
            /**
             * @var Date $timeStart
             */
            $timeStart = $Element->get("WORK_DATE");
            $timeFinish = (clone $timeStart)->add('+1 day');
            $FilterMain = new ConditionTree();
            $FilterMain->logic(ConditionTree::LOGIC_AND);
            $FilterMain->where(
                [
                    [Deal::FIELD_TIME_START_CODE, '>=', $timeStart],
                    [Deal::FIELD_TIME_FINISH_CODE, '<=', $timeFinish],
                ]
            );

            $CrossDealsQuery = DealTable::query()
                ->setSelect(
                    [
                        "ID",
                        "MASTER_NAME" => "MASTER.NAME",
                        "MASTER_LAST_NAME" => "MASTER.LAST_NAME",
                        Deal::FIELD_MASTER_CODE,
                        Deal::FIELD_TIME_START_CODE,
                        Deal::FIELD_TIME_FINISH_CODE
                    ]
                )
                ->where($FilterMain)
                ->addFilter(Deal::FIELD_MASTER_CODE, $Element->get('MASTER_ID'))
                ->registerRuntimeField('MASTER', [
                    'data_type' => \Bitrix\Main\UserTable::getEntity(),
                    'reference' => ['=this.' . Deal::FIELD_MASTER_CODE => 'ref.ID']
                ])
                ->exec();
            if ($arDeal = $CrossDealsQuery->fetch()) {
                $message = "Удаление завершилось с ошибкой!\nВ данную дату " . $timeStart->format(
                        'd.m.Y'
                    ) . ' у мастера ' . $arDeal["MASTER_NAME"] . ' ' . $arDeal["MASTER_LAST_NAME"] . ' записаны клиенты. Сообщите администратору о необходимости их отменить или перенести, после этого повторите попытку.';
                global $USER;
                $fields = array(
                    "TO_USER_ID" => $USER->GetID(),
                    "FROM_USER_ID" => 0,
                    "NOTIFY_TYPE" => 4,
                    "NOTIFY_MODULE" => "crm",
                    "NOTIFY_MESSAGE" => $message
                );
                Loader::includeModule('im');
                \CIMNotify::Add($fields);
            } else {
                $Element->delete();
            }
        }
        return true;
	}

    public function mobileAction()
    {
        $this->returnAsIs = true;
        $arRequest = Utils::getRequestBodyContent();
        Log::AddEvent($arRequest);
	}
}