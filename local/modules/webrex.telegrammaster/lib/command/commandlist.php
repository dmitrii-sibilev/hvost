<?php

namespace Webrex\TelegramMaster\Command;

use Webrex\Telegram\Bot\Chat;
use Webrex\Telegram\Bot\InputMessage;
use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Bot\StageButton;
use Webrex\Telegram\Helpers\Log;

class CommandList
{
    /**
     * @param Chat $chat
     * @param $commandCode
     * @return false|Command
     */
    public static function getCommandByCode(Chat $chat, $commandCode)
    {
        switch ($commandCode) {
            case '/start':
                return new Register($chat);
            case '/help':
                return new Help($chat);
            case '/today':
                return new Today($chat);
            case '/week':
                return new Week($chat);
            case '/month':
                return new Month($chat);
            case '/date':
                return new Date($chat);
            case '/delete-notify':
                return new DeleteNotify($chat);
            case '/finish':
                return new Finish($chat);
            case '/back':
                return new Back($chat);
            case '/edit':
                return new Edit($chat);
            case '/price':
                return new Price($chat);
            case '/cancel-update':
                return new CancelUpdate($chat);
            case '/disable-cashback':
                return new DisableCashback($chat);
            case '/enable-cashback':
                return new EnableCashback($chat);
            case '/success':
                return new Success($chat);
        }
        return false;
    }

    /**
     * @param Chat $chat
     * @param $messageText
     * @return false|Command
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getCommandByMessage(Chat $chat, $messageText)
    {
        $chatObj = $chat->getChatEntityObject();
        $btn = StageButton::getStageButtons($chatObj->getStageId(), $messageText);

        if ($btn[0]) {
            return self::getCommandByCode($chat, $btn[0]['CODE']);
        }
        if ($chatObj->get('STAGE_ID') == Stage::getMasterEditPriceStageId() && (int)$messageText) {
            return new SetPrice($chat, $messageText);
        }
        return new NoUnderstand($chat);
    }
}