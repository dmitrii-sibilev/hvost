<?php

namespace Starlabs\Project\Calendar;

use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;
use Starlabs\Project\Calendar\Model\CalendarNotesTable;
use Starlabs\Project\Grooming\Tasks;
use Starlabs\Project\Personal\Master;
use Starlabs\Project\WorkSchedule\Model\WorkScheduleTable;

class Notes
{
    const CALENDAR_EVENT_TYPE = "note";

    public function __construct()
    {
        Loader::includeModule('tasks');
    }

    public static function getNotesBySalonId($salonId)
    {
        $Query = CalendarNotesTable::query()
            ->setSelect(
                [
                    "*",
                    "MASTER_NAME"      => "MASTER.NAME",
                    "MASTER_LAST_NAME" => "MASTER.LAST_NAME",
                    "MASTER_COLOR"     => "MASTER." . Master::COLOR_FIELD_CODE
                ]
            )
            ->setFilter(["SALON_ID" => $salonId])
            ->exec();
        $arResult = [];
        while ($arNote = $Query->fetch()) {
            $arResult[] = [
                "master"      => [
                    "name"  => $arNote["MASTER_NAME"] . ' ' . $arNote["MASTER_LAST_NAME"],
                    "color" => $arNote["MASTER_COLOR"],
                    "id"    => $arNote["MASTER_ID"]
                ],
                "id"          => $arNote["ID"],
                "start"       => $arNote["TIME_START"]->format('Y-m-d\TH:i:s.000Z'),
                "finish"      => $arNote["TIME_FINISH"]->format('Y-m-d\TH:i:s.000Z'),
                "title"       => $arNote["TITLE"],
                "description" => $arNote["DESCRIPTION"],
                "type"        => self::CALENDAR_EVENT_TYPE
            ];
        }
        return $arResult;
    }

    public function add($arFields)
    {
        global $USER;
        if ($arTaskFields = $this->prepareTaskFields($arFields)) {
            $Task = \CTaskItem::add($arTaskFields, $USER->GetID());
            $arFields["TASK_ID"] = $Task->getId();
        }
        $item = CalendarNotesTable::add(["fields" => $arFields]);
        return $item->getId();
    }

    public function update($id, $arFields)
    {
        global $USER;
        $Note = CalendarNotesTable::query()
            ->setSelect(["ID", "TASK_ID"])
            ->setFilter(["ID" => $id])
            ->exec()
            ->fetchObject();
        if ($taskId = $Note->get('TASK_ID')) {
            $taskItem = new \CTaskItem($taskId, 1);
            if ($arTaskFields = $this->prepareTaskFields($arFields)) {
                $taskItem->update($arTaskFields);
            } else {
                $taskItem->delete();
                $arFields["TASK_ID"] = 0;
            }
        } else {
            if ($arTaskFields = $this->prepareTaskFields($arFields)) {
                $Task = \CTaskItem::add($arTaskFields, $USER->GetID());
                $arFields["TASK_ID"] = $Task->getId();
            }
        }

        return CalendarNotesTable::update($id, ["fields" => $arFields])->isSuccess();
    }

    public function delete($id)
    {
        $Note = CalendarNotesTable::query()
            ->setSelect(["ID", "TASK_ID"])
            ->setFilter(["ID" => $id])
            ->exec()
            ->fetchObject();
        if ($taskId = $Note->get('TASK_ID')) {
            $Task = new \CTaskItem($taskId, 1);
            $Task->delete();
        }
        return $Note->delete()->isSuccess();
    }

    private function prepareTaskFields($arNoteFields)
    {
        $assistantId = '';
        $arTaskFields = [];
        if (!$arNoteFields["MASTER_ID"]) {
            return false;
        }
        $scheduleRes = WorkScheduleTable::query()
            ->setSelect(["MASTER_ID", "ASSISTANT_ID", "WORK_DATE"])
            ->setFilter(
                [
                    "MASTER_ID" => $arNoteFields["MASTER_ID"],
                    "WORK_DATE" => new DateTime($arNoteFields["TIME_FINISH"]->format('Y-m-d'), 'Y-m-d')
                ]
            )
            ->exec();
        if ($arRecord = $scheduleRes->fetch()) {
            $assistantId = [$arRecord['ASSISTANT_ID']];
            $taskDesc = $arNoteFields["DESCRIPTION"] . '<br>' .
                '<br>' .
                'Отведенное время: с ' . $arNoteFields["TIME_START"]->format('H:i') . ' по ' .
                $arNoteFields["TIME_FINISH"]->format('H:i');

            $arTaskFields = [
                "TITLE"           => $arNoteFields["TITLE"],
                "DESCRIPTION"     => $taskDesc,
                "RESPONSIBLE_ID"  => $arNoteFields["MASTER_ID"],
                "START_DATE_PLAN" => $arNoteFields["TIME_START"],
                "END_DATE_PLAN"   => $arNoteFields["TIME_FINISH"],
                "DEADLINE"        => $arNoteFields["TIME_START"],
                "ACCOMPLICES"     => $assistantId,
                "GROUP_ID"        => Tasks::GROUP_APPOINTMENT_GROOMING_ID,
            ];
        }
        return $arTaskFields;
    }

    /**
     * @return mixed
     */
    public function getNoteId()
    {
        return $this->noteId;
    }

    /**
     * @return mixed
     */
    public function getTaskId()
    {
        return $this->taskId;
    }
}