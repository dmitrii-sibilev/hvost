<?

namespace StarLabs\Project\Events;

use Bitrix\Crm\DealTable;
use Bitrix\Main\EventManager;
use Bitrix\Main\UserPhoneAuthTable;
use Starlabs\Project\Grooming\Cashback;
use Starlabs\Project\Grooming\Deal;
use StarLabs\Tools\Events\HandlerInterface;
use Starlabs\Tools\Helpers\Log;

class Crm implements HandlerInterface
{
    public static function setHandlers()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->addEventHandler(
            'crm',
            'OnBeforeCrmLeadAdd',
            [self::class, 'OnBeforeCrmLeadAdd']
        );

        $eventManager->addEventHandler(
            'crm',
            'OnBeforeCrmLeadUpdate',
            [self::class, 'OnBeforeCrmLeadUpdate']
        );

        $eventManager->addEventHandler(
            'crm',
            'OnBeforeCrmContactUpdate',
            [self::class, 'OnBeforeCrmContactUpdate']
        );

        $eventManager->addEventHandler(
            'crm',
            'OnBeforeCrmContactAdd',
            [self::class, 'OnBeforeCrmContactAdd']
        );
        $eventManager->addEventHandler(
            'crm',
            'OnBeforeCrmDealAdd',
            [self::class, 'OnBeforeCrmDealAdd']
        );
        $eventManager->addEventHandler(
            'crm',
            'OnAfterCrmDealUpdate',
            [self::class, 'OnAfterCrmDealUpdate']
        );
        $eventManager->addEventHandler(
            'crm',
            'OnBeforeCrmDealUpdate',
            [self::class, 'OnBeforeCrmDealUpdate']
        );
    }


    public static function OnBeforeCrmContactAdd(&$arFields)
    {
        self::fixEntityPhoneFormat($arFields);
    }
    public static function OnBeforeCrmContactUpdate(&$arFields)
    {
        Log::AddEvent($arFields);
        self::fixEntityPhoneFormat($arFields);
    }

    public static function OnBeforeCrmDealAdd(&$arFields)
    {
        self::fixEntityPhoneFormat($arFields);
    }


    public static function OnBeforeCrmLeadAdd(&$arFields)
    {
        self::fixEntityPhoneFormat($arFields);
    }

    public static function OnBeforeCrmDealUpdate(&$arFields)
    {
        self::fixEntityPhoneFormat($arFields);
        $oldDeal = DealTable::query()
            ->setSelect([Deal::FIELD_USE_CASHBACK_CODE, "STAGE_ID", "CONTACT_ID"])
            ->setFilter(["ID" => $arFields["ID"]])
            ->exec()->fetch();
        if ($arFields["STAGE_ID"] == Deal::getWonStatusId() && $oldDeal["STAGE_ID"] != Deal::getWonStatusId()) {//TODO: убрать вложенность if, условие лучше разделить на 2 части

            $Cashback = new Cashback($arFields["ID"]);
            if ($arFields[Deal::FIELD_USE_CASHBACK_CODE] || $oldDeal[Deal::FIELD_USE_CASHBACK_CODE]) {
                $Cashback->writeOffCashback();
            }
            $Cashback->earnCashback();
        }
    }

    public static function OnAfterCrmDealUpdate(&$arFields)
    {
        if ($arFields["STAGE_ID"] == Deal::getWonStatusId()) {
            $currentDeal = DealTable::query()
                ->setSelect(["CONTACT_ID", "CONTACT_NAME" => "CONTACT.NAME", "CONTACT_PHONE" => "CONTACT.PHONE"])
                ->setFilter(["ID" => $arFields["ID"]])
                ->exec()->fetch();

            $OtherCompletedDeals = DealTable::query()
                ->setFilter(["CONTACT_ID" => $currentDeal["CONTACT_ID"], "STAGE_ID" => Deal::getWonStatusId(), "!ID" => $arFields["ID"]])
                ->setSelect(["ID"])
                ->exec();
            if (!$OtherCompletedDeals->fetch()) {
                $arTaskFields = [
                    "TITLE"           => 'Связаться с клиентом',
                    "DESCRIPTION"     => 'Клиент ' . $currentDeal["CONTACT_NAME"] . ' впервые посетил наш салон, нужно собрать обратную связь. Телефон: ' . $currentDeal["CONTACT_PHONE"],
                    "RESPONSIBLE_ID"  => 1, //TODO: продумать кто должен быть ответственным, и дедлайн у задачи
                    "UF_CRM_TASK"     => ["D_" . $arFields["ID"]],
                    "TAGS" => [\Starlabs\Project\Grooming\Tasks::TAG_SHOW_TASK_IN_CARD],
                    "GROUP_ID" => \Starlabs\Project\Grooming\Tasks::GROUP_CUSTOMERS_COMMUNICATION_ID,
                ];

                $res = \CTaskItem::add($arTaskFields, 1);
            }
        }
    }

    public static function OnBeforeCrmLeadUpdate(&$arFields)
    {
        self::fixEntityPhoneFormat($arFields);
    }
    private static function fixEntityPhoneFormat(&$arFields)
    {
        foreach ($arFields["FM"]["PHONE"] as $key => $arPhone) {
            $arFields["FM"]["PHONE"][$key]["VALUE"] = UserPhoneAuthTable::normalizePhoneNumber($arPhone["VALUE"]);
        }
    }
}