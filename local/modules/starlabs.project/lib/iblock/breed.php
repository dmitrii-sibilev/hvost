<?php

namespace Starlabs\Project\Iblock;

use Starlabs\Tools\Helpers\p;

class Breed extends IblockPrototype
{
    /**
     * TODO: Разобраться как быстро получать информацию о типе животного ( эта порода - собака или нет?)
     */
    const IBLOCK_API_CODE = "Breed";
    const IBLOCK_CODE = "Breed";
    const PROP_PET_TYPE_CODE = "PET_TYPE";
    const PROP_LARGE_BREED_CODE = "LARGE_BREED";
    private $arBigBreeds = [];

    protected function getIblockApiCode():string
    {
        return self::IBLOCK_API_CODE;
    }

    public function getNameById($id)
    {
        return $this->createQuery()
            ->setSelect(["NAME"])
            ->setFilter(["ID" => $id])
            ->exec()
            ->fetch()["NAME"];
    }

    /**
     * @param $id
     * @return bool
     */
    public function isSmallDog($id)
    {
        $arSmallBreeds = $this->getSmallDogsBreeds();
        if (in_array($id, array_keys($arSmallBreeds))) {
            return true;
        }
        return false;
    }

    public function getSmallDogsBreeds()
    {
        if (empty($this->arBigBreeds)) {
            $res = $this->createQuery()
                ->setSelect(["NAME", "ID", "BIG_" => self::PROP_LARGE_BREED_CODE, "TYPE_" => self::PROP_PET_TYPE_CODE])
                ->setFilter([/*"!BIG_VALUE" => "Y",*/ "TYPE_VALUE" => 176])
                ->exec();

            while ($arBreed = $res->fetch()) {
                if ($arBreed["BIG_VALUE"] != 'Y') {
                    $this->arBigBreeds[$arBreed["ID"]] = $arBreed;
                }
            }
        }

        return $this->arBigBreeds;
    }
}