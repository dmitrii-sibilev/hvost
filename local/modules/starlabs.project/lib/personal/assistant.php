<?php

namespace Starlabs\Project\Personal;

use Bitrix\Sale\SectionTable;
use Starlabs\Tools\Helpers\p;

class Assistant extends Employee
{
    const DEPARTMENT_CODE = 'Assistants';

    /**
     * @param array $arExtraSelect
     * @param array $arExtraFilter
     */
    public function getAllAssistants($arExtraSelect = [], $arExtraFilter = [])
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
}