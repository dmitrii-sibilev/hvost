<?php

namespace Starlabs\Project\Personal;

use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;
use Bitrix\Iblock\SectionTable;
use Starlabs\Tools\Helpers\p;

abstract class Employee
{
    protected $departmentId;
    protected $usersId;
    const COLOR_FIELD_CODE = 'UF_COLOR';

    protected function getAllEmployers($arExtraSelect = [], $arExtraFilter = [])
    {
        $departmentId = $this->getDepartmentId();
        $arSelect = [
            "ID",
        ];
        $arFilter = [
            "ACTIVE" => "Y",
            "UF_DEPARTMENT" => $departmentId
        ];
        $Query = UserTable::query()
            ->setSelect(array_merge($arSelect, $arExtraSelect))
            ->setFilter(array_merge($arFilter, $arExtraFilter))
            ->exec();

        while ($arUser = $Query->fetch()) {
            if (key_exists("PERSONAL_PHOTO", $arUser)) {
                if ($arUser["PERSONAL_PHOTO"]) {
                    $arUser["AVATAR"] = \CFile::GetPath($arUser["PERSONAL_PHOTO"]);
                } else {
                    $arUser["AVATAR"] = '/upload/starlabs/avatar.svg';
                }
            }
            $arResult[] = $arUser;
        }

        return $arResult;
    }

    /**
     * @param $userId
     * @return bool
     */
    public function isMemberOfDepartment($userId)
    {
        $arUsersId = $this->getUsersId();
        if (in_array($userId, $arUsersId)) {
            return true;
        }
        return false;
    }

    private function getDepartmentId()
    {
        Loader::includeModule("iblock");
        if (!$this->departmentId) {
            $departmentCode = $this->getDepartmentCode();
            $Query = SectionTable::query()
                ->setSelect(["ID"])
                ->setFilter(["CODE" => $departmentCode])
                ->exec();
            if ($department = $Query->fetch()) {
                $this->departmentId = $department["ID"];
            } else {
                throw new \Exception("Не найдено подразделение $departmentCode в структуре компании");
            }
        }
        return $this->departmentId;
    }

    /**
     * @return mixed
     */
    public function getUsersId()
    {
        if (!$this->usersId) {
            $arUsers = $this->getAllEmployers();
            foreach ($arUsers as $user) {
                $this->usersId[] = $user["ID"];
            }
        }
        return $this->usersId;
    }

    abstract function getDepartmentCode();
}