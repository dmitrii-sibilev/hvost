<?php

namespace Starlabs\Project\Personal;

use Bitrix\Sale\SectionTable;
use Starlabs\Project\Iblock\MasterQualification;
use Starlabs\Tools\Helpers\p;

class Master extends Employee
{
    const DEPARTMENT_CODE = 'Masters';

    /**
     * @param array $arExtraSelect
     * @param array $arExtraFilter
     */
    public function getAllMasters($arExtraSelect = [], $arExtraFilter = [])
    {
        $arSelect = [
            "NAME",
            "LAST_NAME",
        ];
        return $this->getAllEmployers(array_merge($arSelect, $arExtraSelect), $arExtraFilter);
    }

    /**
     * @return string
     */
    public function getDepartmentCode():string
    {
        return self::DEPARTMENT_CODE;
    }

    public function isTop($masterId)
    {
        $arMasters = $this->getAllMasters(["UF_QUALIFICATION"], ["ID" => $masterId]);
        $isTop = false;
        if ($arMaster = $arMasters[0]) {
            $QualQuery = (new MasterQualification())->createQuery();
            $QualRes = $QualQuery
                ->setFilter(["CODE" => MasterQualification::TOP_ELEMENT_CODE, "ID" => $arMaster["UF_QUALIFICATION"]])
                ->exec();
            if ($QualRes->fetch()) {
                $isTop = true;
            }
        }
        return $isTop;
    }

    public function getByTelegramChatId($chatId)
    {
        return $this->getAllMasters([], ['UF_TELEGRAM_CHAT_ID' => $chatId])[0];
    }

    public function getTelegramChatId($masterId)
    {
        return $this->getAllMasters(['UF_TELEGRAM_CHAT_ID'], ["ID" => $masterId])[0]['UF_TELEGRAM_CHAT_ID'];
    }
}