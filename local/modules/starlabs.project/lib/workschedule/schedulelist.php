<?php

namespace Starlabs\Project\WorkSchedule;

use Bitrix\Main\Type\Date;
use Bitrix\Main\UserGroupTable;
use Starlabs\Project\Iblock\Salon;
use Starlabs\Project\Helpers\Utils;
use Starlabs\Project\Personal\Assistant;
use Starlabs\Project\Personal\Master;
use Starlabs\Project\WorkSchedule\Model\WorkScheduleTable;

class ScheduleList
{
    const USER_GROUPS_CODE_CAN_EDIT = [
        "headadmins",
        "workschedulereeditors"
    ];
    public function getFullSchedule()
    {
        $MasterEntity = new Master();
        $SalonEntity = new Salon();
        $Query = WorkScheduleTable::query()
            ->setSelect(["*"])
            ->setFilter(["MASTER_ID" => $MasterEntity->getUsersId(), "SALON_ID" => $SalonEntity->getSalonsId()])
            ->setOrder(["MASTER_ID" => "ASC", "WORK_DATE" => "ASC"])
            ->exec();
        /**
         * @var Date $workDate
         */
        $arResult = [];
        while ($arRecord = $Query->fetch()) {
            $arRow = [];
            $workDate = $arRecord["WORK_DATE"];
            $Assistant = new Assistant();
                $arRow = [
                    "ID" => $arRecord["ID"],
                    "SALON_ID" => $arRecord["SALON_ID"],
                    "MASTER_ID" => $arRecord["MASTER_ID"],
                    "DATE" => Utils::formatDateToJs($workDate),
                ];
                if ($Assistant->isMemberOfDepartment($arRecord["ASSISTANT_ID"]) || $MasterEntity->isMemberOfDepartment($arRecord["ASSISTANT_ID"])) {
                    $arRow["ASSISTANT_ID"] = $arRecord["ASSISTANT_ID"];
                }
                $arResult[] = $arRow;

        }
        return $arResult;
    }

    public function canUserEditSchedule($userId)
    {
        $Query = UserGroupTable::query()
            ->setFilter(["USER_ID" => $userId, "GROUP.STRING_ID" => self::USER_GROUPS_CODE_CAN_EDIT])
            ->setSelect(["USER_ID"])
            ->exec();
        if ($Query->fetch()) {
            return true;
        } else {
            return false;
        }
    }
}