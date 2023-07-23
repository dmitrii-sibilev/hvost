<?php

namespace Webrex\TelegramWebinar\Command;

use Webrex\Telegram\Bot\Chat;
use Webrex\Telegram\Bot\InputMessage;
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
//            case '/adminrefresh':
//                return new AdminRefresh($chat);
//            case '/recordready':
//                return new RecordReady($chat);
//            case '/nextmorning':
//                return new NextMorning($chat);
//            case '/final':
//                return new FinalMessage($chat);
            case '/yes-watch':
                return new YesWatch($chat);
            case '/no-watch':
                return new NoWatch($chat);
            case '/join-course':
            case '/price':
                return new JoinCourse($chat);
            case '/manager-contact':
                return new ManagerContact($chat);
            case '/course-info':
                return new CourseInfo($chat);
            case '/stuff':
                return new Stuff($chat);
            case '/program':
                return new Program($chat);
            case '/no-feedback':
                return new NoFeedback($chat);
            case '/with-mentor':
                return new WithMentor($chat);
            case '/with-author':
                return new WithAuthor($chat);
//            case '/breed-list':
//                return new BreedList($chat);
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
        return new NoUnderstand($chat);
    }
}