<?php

namespace Starlabs\Project\Grooming;

use Bitrix\Main\Loader;
use Bitrix\Main\Type\Date;
use Webrex\TelegramMaster\Helpers\Log;

class Tasks
{
    const GROUP_APPOINTMENT_GROOMING_ID = 1;
    const GROUP_CUSTOMERS_COMMUNICATION_ID = 2;
    const TAG_SHOW_TASK_IN_CARD = "ОтобразитьВКалендаре";

    public static function getGroomingTaskIdByDealId($dealId)
    {
        return self::getGroomingTaskByDealId($dealId)['ID'];
    }

    public static function getGroomingTaskByDealId($dealId)
    {
        $arOrder = [];
        $arFilter = [
            "UF_CRM_TASK" => ["D_" . $dealId],
            "GROUP_ID" => self::GROUP_APPOINTMENT_GROOMING_ID
        ];
        $arSelect = [
            "ID",
            "DESCRIPTION",
            "UF_CRM_TASK"
        ];
        return (\CTasks::GetList($arOrder, $arFilter, $arSelect))->Fetch();
    }

    public static function getDealIdByTaskId($taskId)
    {
        $arOrder = [];
        $arFilter = [
            "ID" => $taskId,
            "GROUP_ID" => self::GROUP_APPOINTMENT_GROOMING_ID
        ];
        $arSelect = [
            "ID",
            "UF_CRM_TASK"
        ];
        $tasksResult = \CTasks::GetList($arOrder, $arFilter, $arSelect);
        if (!$arTask = $tasksResult->Fetch()) {
            return false;
        }

        foreach ($arTask["UF_CRM_TASK"] as $crmEntity) {
            $entityExplode = explode('_', $crmEntity);
            $entityPrefix = $entityExplode[0];
            $entityId = $entityExplode[1];
            if ($entityPrefix == \CCrmOwnerTypeAbbr::Deal) {
                return $entityId;
            }
        }
        return false;
    }

    public static function getCardTasksByDealId($dealId)
    {
        $arOrder = [];
        $arFilter = [
            "UF_CRM_TASK" => ["D_" . $dealId],
            "!STATUS" => 5,
            "TAG" => [self::TAG_SHOW_TASK_IN_CARD],
            "GROUP_ID" => [self::GROUP_CUSTOMERS_COMMUNICATION_ID],
        ];
        $arSelect = [
            "ID",
            "TITLE",
            "UF_CRM_TASK"
        ];
        $tasksResult = \CTasks::GetList($arOrder, $arFilter, $arSelect);
        $arTasks = [];
        while ($arTask = $tasksResult->Fetch()) {
            $arTasks[$arTask["ID"]] = $arTask;
        }
        return $arTasks;
    }

    public static function getTodayMasterTasks($masterId)
    {
        $today = new Date();
        $tomorrow = (clone $today)->add('1 day');
        return self::getPeriodMasterTasks($masterId, $today, $tomorrow);
    }

    public static function getWeekMasterTasks($masterId)
    {

        $today = new Date();
        $tomorrow = (clone $today)->add('7 day');
        return self::getPeriodMasterTasks($masterId, $today, $tomorrow);
    }

    public static function getMonthMasterTasks($masterId)
    {
        $today = new Date();
        $tomorrow = (clone $today)->add('1 month');
        return self::getPeriodMasterTasks($masterId, $today, $tomorrow);
    }

    public static function getPeriodMasterTasks($masterId, $startTime, $endTime)
    {
        Loader::includeModule('tasks');
        $arOrder = [
            'START_DATE_PLAN' => 'ASC'
        ];
        $arFilter = [
            "RESPONSIBLE_ID" => $masterId,
            "GROUP_ID" => self::GROUP_APPOINTMENT_GROOMING_ID,
            ">START_DATE_PLAN" => $startTime,
            "<START_DATE_PLAN" => $endTime,
            "REAL_STATUS" => [\CTasks::STATE_NEW, \CTasks::STATE_PENDING, \CTasks::STATE_IN_PROGRESS]
        ];
        $arSelect = [
            "ID",
            "TITLE",
            "DESCRIPTION",
        ];
        $tasksResult = \CTasks::GetList($arOrder, $arFilter, $arSelect);
        $arTasks = [];
        while ($arTask = $tasksResult->Fetch()) {
            $arTasks[$arTask["ID"]] = $arTask;
        }
        return $arTasks;
    }

    public static function getById($taskId)
    {
        Loader::includeModule('tasks');
        $arFilter = [
            "ID" => $taskId,
        ];
        $arSelect = [
            "ID",
            "TITLE",
            "DESCRIPTION",
            "UF_CRM_TASK",
            '*'
        ];
        $tasksResult = \CTasks::GetList([], $arFilter, $arSelect);
        if ($arTask = $tasksResult->Fetch()) {
            return $arTask;
        }
        return false;
    }
}