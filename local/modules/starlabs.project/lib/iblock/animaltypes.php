<?php

namespace Starlabs\Project\Iblock;

class AnimalTypes extends IblockPrototype
{
    const IBLOCK_API_CODE = "AnimalTypes";
    const IBLOCK_CODE = "AnimalTypes";

    const CAT_CODE = "Cats";
    const DOG_CODE = "Dogs";
    const OTHER_CODE = "Other";

    protected function getIblockApiCode():string
    {
        return self::IBLOCK_API_CODE;
    }

    public function isCat($id)
    {
        $res = $this->createQuery()
            ->setSelect(["CODE"])
            ->setFilter(["ID" => $id, "CODE" => self::CAT_CODE])
            ->exec();
        if ($res->fetch()) {
            return true;
        }
        return false;
    }
}