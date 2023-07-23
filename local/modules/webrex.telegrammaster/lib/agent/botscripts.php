<?php

namespace Webrex\TelegramMaster\Agent;

use Webrex\Telegram\Bot\Chat;
use Webrex\Telegram\Bot\Stage;
use Webrex\TelegramWebinar\Command\BeforeStartMessage;
use Webrex\TelegramWebinar\Command\FinalMessage;
use Webrex\TelegramWebinar\Command\FinishMessage;
use Webrex\TelegramWebinar\Command\MorningMessage;
use Webrex\TelegramWebinar\Command\NewLessonMessage;
use Webrex\TelegramWebinar\Command\NextMorning;
use Webrex\TelegramWebinar\Command\RecordReady;
use Webrex\TelegramWebinar\Command\StartMessage;
use Webrex\TelegramWebinar\Helpers\Log;
use Webrex\Telegram\Model\TelegramChatTable;

class BotScripts
{
    /* TODO: Переделать методы на использование основного модуля
    public static function sendNewLessonMessages()
    {
        $arChats = TelegramChatTable::query()
            ->setSelect(['*'])
            ->setFilter(['ACTIVE' => true])
            ->exec();
        while ($arChat = $arChats->fetch()) {
            try {
                $chat = new Chat($arChat['CHAT_ID']);
                $command = new NewLessonMessage($chat);
                $command->do();
            } catch (\Throwable $exception) {
                Log::addError(
                    $exception->getTraceAsString() . ' ' . $exception->getLine() . ' ' . $exception->getMessage()
                );
            }
        }
    }

    public static function sendTodayMorningMessages()
    {
        $registeredStageId = Stage::getRegisteredStageId();
        $arChats = TelegramChatTable::query()
            ->setSelect(['*'])
            ->setFilter(['ACTIVE' => true, 'STAGE_ID' => $registeredStageId])
            ->exec();
        while ($arChat = $arChats->fetch()) {
            try {
                $chat = new Chat($arChat['CHAT_ID']);
                $command = new MorningMessage($chat);
                $command->do();
            } catch (\Throwable $exception) {
                Log::addError(
                    $exception->getTraceAsString() . ' ' . $exception->getLine() . ' ' . $exception->getMessage()
                );
            }
        }
    }

    public static function sendBeforeFifteenMinutesMessages()
    {
        $registeredStageId = Stage::getRegisteredStageId();
        $arChats = TelegramChatTable::query()
            ->setSelect(['*'])
            ->setFilter(['ACTIVE' => true, 'STAGE_ID' => $registeredStageId])
            ->exec();
        while ($arChat = $arChats->fetch()) {
            try {
                $chat = new Chat($arChat['CHAT_ID']);
                $command = new BeforeStartMessage($chat);
                $command->do();
            } catch (\Throwable $exception) {
                Log::addError($exception->getFile() . ' ' . $exception->getLine() . ' ' . $exception->getMessage());
            }
        }
    }

    public static function sendStartMessages()
    {
        $registeredStageId = Stage::getRegisteredStageId();
        $arChats = TelegramChatTable::query()
            ->setSelect(['*'])
            ->setFilter(['ACTIVE' => true, 'STAGE_ID' => $registeredStageId])
            ->exec();
        while ($arChat = $arChats->fetch()) {
            try {
                $chat = new Chat($arChat['CHAT_ID']);
                $command = new StartMessage($chat);
                $command->do();
            } catch (\Throwable $exception) {
                Log::addError($exception->getFile() . ' ' . $exception->getLine() . ' ' . $exception->getMessage());
            }
        }
    }

    public static function sendFinishMessages()
    {
        $registeredStageId = Stage::getRegisteredStageId();
//        $badChats = \Webrex\Telegram\Model\TelegramChatMessageTable::query()
//            ->setSelect(['CHAT_ID'])
//            ->whereLike('MESSAGE_TEXT', '%Закончили%')
//            ->exec();
//        while ($message = $badChats->fetch()) {
//            $badChatsId[] = $message['CHAT_ID'];
//        }
        $arChats = TelegramChatTable::query()
            ->setSelect(['*'])
 //              ->setFilter(['ACTIVE' => true, 'STAGE_ID' => $registeredStageId, 'CHAT_ID' => 970752483])
            ->setFilter(['ACTIVE' => true, 'STAGE_ID' => $registeredStageId])
//            ->setFilter(['ACTIVE' => true, '!CHAT_ID' => $badChatsId, 'STAGE_ID' => $registeredStageId])
            ->exec();
        while ($arChat = $arChats->fetch()) {
            try {
                $chat = new Chat($arChat['CHAT_ID']);
                $command = new FinishMessage($chat);
                $command->do();
            } catch (\Throwable $exception) {
                Log::addError($exception->getFile() . ' ' . $exception->getLine() . ' ' . $exception->getMessage());
            }
        }
    }

    public static function sendRecordReadyMessages()
    {
        $askBeginStageId = Stage::getAskBeginStageId();
//        $badChats = \Webrex\Telegram\Model\TelegramChatMessageTable::query()
//            ->setSelect(['CHAT_ID'])
//            ->whereLike('MESSAGE_TEXT', '%УРА, запись готова! Смотри урок%')
//            ->exec();
//        while ($message = $badChats->fetch()) {
//            $badChatsId[] = $message['CHAT_ID'];
//        }
        $arChats = TelegramChatTable::query()
            ->setSelect(['*'])
//            ->setFilter(['ACTIVE' => true, '!CHAT_ID' => $badChatsId])
            ->setFilter(['ACTIVE' => true, '!STAGE_ID' => $askBeginStageId])
            ->exec();
        while ($arChat = $arChats->fetch()) {
            try {
                $chat = new Chat($arChat['CHAT_ID']);
                $command = new RecordReady($chat);
                $command->do();
            } catch (\Throwable $exception) {
                Log::addError($exception->getFile() . ' ' . $exception->getLine() . ' ' . $exception->getMessage());
            }
        }
    }

    public static function sendNextMorningMessages()
    {
        $askBeginStageId = Stage::getAskBeginStageId();
        $arChats = TelegramChatTable::query()
            ->setSelect(['*'])
//                ->setFilter(['ACTIVE' => true, 'STAGE_ID' => $registeredStageId, 'CHAT_ID' => 542669090])
            ->setFilter(['ACTIVE' => true, '!STAGE_ID' => $askBeginStageId])
            ->exec();
        while ($arChat = $arChats->fetch()) {
            try {
                $chat = new Chat($arChat['CHAT_ID']);
                $command = new NextMorning($chat);
                $command->do();
            } catch (\Throwable $exception) {
                Log::addError($exception->getFile() . ' ' . $exception->getLine() . ' ' . $exception->getMessage());
            }
        }
    }

    public static function sendFinalMessages()
    {
        $askBeginStageId = Stage::getAskBeginStageId();
        $arChats = TelegramChatTable::query()
            ->setSelect(['*'])
//                ->setFilter(['ACTIVE' => true, 'CHAT_ID' => 542669090])
            ->setFilter(['ACTIVE' => true, '!STAGE_ID' => $askBeginStageId])
            ->exec();
        while ($arChat = $arChats->fetch()) {
            try {
                $chat = new Chat($arChat['CHAT_ID']);
                $command = new FinalMessage($chat);
                $command->do();
            } catch (\Throwable $exception) {
                Log::addError($exception->getFile() . ' ' . $exception->getLine() . ' ' . $exception->getMessage());
            }
        }
    }
    */
}