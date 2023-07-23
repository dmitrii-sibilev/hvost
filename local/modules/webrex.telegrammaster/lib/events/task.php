<?php

namespace Webrex\TelegramMaster\Events;

use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;
use Starlabs\Project\Personal\Master;
use Webrex\Telegram\Bot\Chat;
use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Model\TelegramChatMessageTable;
use Webrex\TelegramMaster\Command\NotifyTask;
use Webrex\TelegramMaster\Command\Refresh;
use Webrex\TelegramMaster\Helpers\Log;
use Webrex\TelegramMaster\Helpers\Option;
use Webrex\TelegramMaster\Helpers\TaskMessage;

class Task
{
    public static function taskAdd($arTask)
    {
        $masters = new Master();
        $masterId = $arTask['RESPONSIBLE_ID'];
        $chatId = $masters->getTelegramChatId($masterId);
        $Chat = new Chat($chatId, Option::getBotToken());
        if (is_string($arTask['START_DATE_PLAN'])) {
            $arTask['START_DATE_PLAN'] = new DateTime($arTask['START_DATE_PLAN'], 'd.m.Y H:i:s');
        }
        if (self::checkTaskInMasterStage($arTask['START_DATE_PLAN'], $Chat)) {
            $command = new Refresh($Chat);
            $command->do();
        }
        $notifyMessage = 'Добавлена запись на ' . $arTask['START_DATE_PLAN']->format('d.m.Y') . ':
' . $arTask['DESCRIPTION'];
        $command = new NotifyTask($Chat, $notifyMessage);
        $command->do();
    }

    public static function taskUpdate($arTask, $dateChanged = false)
    {
        $masters = new Master();
        $masterId = $arTask['RESPONSIBLE_ID'] ?: $arTask['META:PREV_FIELDS']['RESPONSIBLE_ID'];
        $chatId = $masters->getTelegramChatId($masterId);
        $Chat = new Chat($chatId, Option::getBotToken());

        $taskDateTime = new DateTime($arTask['START_DATE_PLAN'], 'd.m.Y H:i:s');
        $isTaskInMasterStage = self::checkTaskInMasterStage($taskDateTime, $Chat);
        if ($isTaskInMasterStage && $dateChanged) {
            $command = new Refresh($Chat);
            $command->do();
        }
        if ($isTaskInMasterStage && !$dateChanged) {
            $messageItem = self::findTaskMessage($arTask['ID'], $Chat->getChatId());
            $taskMessage = TaskMessage::prepareTaskMessage($arTask);
            Log::addDebug([$arTask['ID'], $messageItem->getChatMessageId(), $messageItem->getId()]);
            $Chat->editMessageText($taskMessage['TEXT'], $messageItem->getChatMessageId(), $taskMessage['BUTTONS'],$taskMessage['CODE']);
        }

        $notifyMessage = 'Изменена запись на ' . $taskDateTime->format('d.m.Y') . ':
' . $arTask['DESCRIPTION'];
        $command = new NotifyTask($Chat, $notifyMessage);
        $command->do();
    }

    public static function taskDelete()
    {
        //ищем сообщение с этой задачей у мастера, если есть то удаляем
        //сообщение с уведомлением
    }

    private static function checkTaskInMasterStage(DateTime $taskDateTime, Chat $Chat)
    {
        $today = new Date();
        if (!$taskDateTime->getDiff($today)->invert && $taskDateTime->getDiff($today)->days > 0) {
            return false;
        }

        $afterWeek = (clone $today)->add('+ 7 day');
        $afterMonth = (clone $today)->add('+ 1 month');
        switch ($Chat->getChatEntityObject()->get('STAGE_ID')) {
            case Stage::getMasterTodayStageId():
                if ($taskDateTime->getDiff($today)->invert) {
                    return true;
                }
                break;
            case Stage::getMasterWeekStageId():
                if (
                    $taskDateTime->getDiff($today)->invert ||
                    !$taskDateTime->getDiff($afterWeek)->invert && $taskDateTime->getDiff($afterWeek)->days < 7
                ) {
                    return true;
                }
                break;
            case Stage::getMasterMonthStageId():
                if (
                    $taskDateTime->getDiff($today)->invert ||
                    !$taskDateTime->getDiff($afterWeek)->invert && $taskDateTime->getDiff($afterMonth)->days < 30
                ) {
                    return true;
                }
                break;
            default:
                return false;
        }
    }

    private static function findTaskMessage($taskId, $chatId)
    {
        $messageQuery = TelegramChatMessageTable::query()
            ->setSelect(['ID', 'CHAT_MESSAGE_ID'])
            ->setFilter(['CODE' => TelegramChatMessageTable::TASK_MESSAGE_PREFIX . $taskId, 'CHAT_ID' => $chatId])
            ->exec();
        return $messageQuery->fetchObject();
    }
}