<?php

namespace Webrex\Telegram\Bot;

use Webrex\Telegram\Model\TelegramBotTable;

class Bot
{
    public static function getIdByToken(string $token)
    {
        return self::getByToken($token)['ID'];
    }

    public static function getByToken(string $token)
    {
        $arFilter = [
            'CODE' => $token
        ];
        return TelegramBotTable::query()
            ->setSelect(['*'])
            ->setFilter($arFilter)
            ->fetch();
    }
}