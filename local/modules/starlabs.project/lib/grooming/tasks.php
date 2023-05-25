<?php

namespace Starlabs\Project\Grooming;

class Tasks
{
    const GROUP_APPOINTMENT_GROOMING_ID = 1;
    const GROUP_CUSTOMERS_COMMUNICATION_ID = 2;
    const TAG_SHOW_TASK_IN_CARD = "ОтобразитьВКалендаре";

    public static function getGroomingTaskIdByDealId($dealId)
    {
        $arOrder = [];
        $arFilter = [
            "UF_CRM_TASK" => ["D_" . $dealId],
            "GROUP_ID" => self::GROUP_APPOINTMENT_GROOMING_ID
        ];
        $arSelect = [
            "ID"
        ];
        return (\CTasks::GetList($arOrder, $arFilter, $arSelect))->Fetch()["ID"];
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
}