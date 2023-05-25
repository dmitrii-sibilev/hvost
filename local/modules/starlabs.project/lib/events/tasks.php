<?php
namespace StarLabs\Project\Events;

use Bitrix\Crm\DealTable;
use Bitrix\Crm\PhaseSemantics;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Starlabs\Project\Grooming\Deal;
use StarLabs\Tools\Events\HandlerInterface;
use Starlabs\Tools\Helpers\Log;
use Starlabs\Tools\Helpers\p;

class Tasks implements HandlerInterface
{

    public static function setHandlers()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->addEventHandler(
            'tasks',
            'OnBeforeTaskUpdate',
            [self::class, 'completeDeal']
        );
    }

    public static function completeDeal($id, &$data, &$copy)
    {
        $dealId = 0;
        foreach ($copy["UF_CRM_TASK"] as $string) {
            $arString = explode('_', $string);
            if ($arString[0] == \CCrmOwnerTypeAbbr::Deal) {
                $dealId = $arString[1];
                break;
            }
        }
        if (
            $dealId &&
            $data["STATUS"] == \CTasks::STATE_COMPLETED &&
            $data["STATUS"] != $copy["STATUS"] &&
            $copy["GROUP_ID"] == \Starlabs\Project\Grooming\Tasks::GROUP_APPOINTMENT_GROOMING_ID
        ) {
            Loader::requireModule('crm');
            $dealForComplete = DealTable::query()
                ->setFilter(["ID" => $dealId, "STAGE_ID" => Deal::getNewStatusId()])
                ->setSelect(["ID", "CONTACT_ID"])
                ->fetch();
            if (!$dealForComplete) {
                return;
            }

            $Deal = new \CCrmDeal();
            $arFields = [
                "STAGE_ID" => Deal::getWonStatusId(),
                "STAGE_SEMANTIC_ID" => PhaseSemantics::SUCCESS
            ];
            $Deal->Update($dealId,$arFields);
            if ($Deal->LAST_ERROR) {
                \CTaskNotifications::addAnswer($id, $Deal->LAST_ERROR);
                throw new \Bitrix\Tasks\ActionFailedException($Deal->LAST_ERROR);
            }
        }
    }

}