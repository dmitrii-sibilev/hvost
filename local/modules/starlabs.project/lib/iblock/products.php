<?php

namespace Starlabs\Project\Iblock;


use Bitrix\Iblock\SectionTable;

class Products extends IblockPrototype
{
    const IBLOCK_API_CODE = "Products";
    const IBLOCK_CODE = "Products";
    const PROP_PREMIUM_PRICE_CODE = "PREMIUM_PRICE";
    const PROP_LOW_PRICE_CODE = "LOW_PRICE";
    const PROP_PREMIUM_MASTER_PRICE_CODE = "PREMIUM_MASTER_PRICE";
    const PROP_BREED_CODE = "BREED";
    const PROP_BASE_DURATION_CODE = "BASE_DURATION";
    const GROOMING_SECTION_CODE = 'Grooming';
    const GENERAL_SECTION_CODE = 'General';
    //TODO: Отвязаться от ID
    const BASIC_PRODUCTS = [
        1188,
        1189,
        1190,
        1191,
        1192,
        1193,
        1194,
        1195,
        1196,
        1205,
        1206,
        1207,
        1208,
        1209,
        1210,
        1211,
        1212,
        1213,
        1214,
    ];


    public function getPrice($productId, $isPremiumMaster = false, $isPremiumSalon = false)
    {
        $Query = $this->createQuery();
        $Result = $Query
            ->setSelect(
                [
                    "PREMIUM_PRICE_" => self::PROP_PREMIUM_PRICE_CODE,
                    "PREMIUM_MASTER_PRICE_" => self::PROP_PREMIUM_MASTER_PRICE_CODE,
                    "LOW_PRICE_" => self::PROP_LOW_PRICE_CODE
                ]
            )
            ->setFilter(["ID" => $productId])
            ->exec();
        if ($arProduct = $Result->fetch()) {
            if ($isPremiumSalon) {
                $priceValue = $arProduct["PREMIUM_PRICE_VALUE"];
            } elseif ($isPremiumMaster && $arProduct["PREMIUM_MASTER_PRICE_VALUE"] > 0) {
                $priceValue = $arProduct["PREMIUM_MASTER_PRICE_VALUE"];
            } else {
                $priceValue = $arProduct["LOW_PRICE_VALUE"];
            }
            return explode('|',$priceValue)[0];
        } else {
            Throw new \Exception('Не найден товар с id = ' . $productId);
        }
    }

    public function isGeneralProduct($productId)
    {
        $isGeneral = false;
        $Query = $this->createQuery()
            ->setSelect(["ID", "SECTION_CODE_" => "SECTION_DATA.CODE"])
            ->setFilter(["ID" => $productId, "SECTION_CODE_" => self::GENERAL_SECTION_CODE])
            ->registerRuntimeField(
                'SECTION_DATA',
                [
                    'data_type' => SectionTable::getEntity(),
                    'reference' => ['=this.IBLOCK_SECTION_ID' => 'ref.ID']
                ]
            )
            ->exec();
        if ($Query->fetch()) {
            $isGeneral = true;
        }
        return $isGeneral;
    }

    protected function getIblockApiCode():string
    {
        return self::IBLOCK_API_CODE;
    }
}