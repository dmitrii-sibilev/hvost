<?php

namespace Webrex\Telegram\Bot;

use Webrex\Telegram\Model\TelegramButtonTable;

class StageButton
{
    /**
     * @param int $stageId
     * @param string $text
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getStageButtons(int $stageId, string $text = '')
    {
        $arFilter = [
            'STAGE_ID' => $stageId
        ];
        if ($text) {
            $arFilter['TEXT'] = $text;
        }
        return TelegramButtonTable::query()
            ->setSelect(['*'])
            ->setFilter($arFilter)
            ->fetchAll();
    }

    public static function getAnyStageButton(string $text)
    {
        $arFilter = [
            '=STAGE_ID' => 0,
            'TEXT' => $text,
        ];
        return TelegramButtonTable::query()
            ->setSelect(['*'])
            ->setFilter($arFilter)
            ->fetch();
    }

    public static function getStagesButtons(array $stagesId, string $text = '')
    {
        $arFilter = [
            'STAGE_ID' => $stagesId
        ];
        if ($text) {
            $arFilter['TEXT'] = $text;
        }
        return TelegramButtonTable::query()
            ->setSelect(['*'])
            ->setFilter($arFilter)
            ->fetchAll();
    }
}