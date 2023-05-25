<?php

namespace Starlabs\Project\Iblock;

class Salon extends IblockPrototype
{
    const IBLOCK_API_CODE = "Salon";

    public function getSalonsId()
    {
        $Query = $this->createQuery();
        $Result = $Query
            ->setSelect(["ID"])
            ->setFilter(["ACTIVE" => "Y"])
            ->exec();
        while ($salon = $Result->fetch()) {
            $arResult[] = $salon["ID"];
        }
        return $arResult;
    }

    public function isPremium($salonId)
    {
        $isPremium = false;
        $Query = $this->createQuery()
            ->setFilter(["ID" => $salonId, "PREMIUM_VALUE" => "Y"])
            ->setSelect(["PREMIUM_" => "PREMIUM_SALON", "ID"])
            ->exec();
        if ($Query->fetch()) {
            $isPremium = true;
        }
        return $isPremium;
    }

    protected function getIblockApiCode():string
    {
        return self::IBLOCK_API_CODE;
    }
}