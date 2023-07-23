<?php

namespace Webrex\TelegramMaster\Command;

use Webrex\Telegram\Bot\Stage;
use Webrex\TelegramMaster\Helpers\Log;
use Webrex\TelegramMaster\Helpers\TaskMessage;

class Refresh extends Command
{
    protected function actionsBeforeProcess()
    {

    }
    protected function actionsAfterProcess()
    {

    }
    protected function process()
    {
        switch ($this->chat->getChatEntityObject()->get('STAGE_ID')) {
            case Stage::getMasterTodayStageId():
                $this->chat->deleteAllMessages();
                $messages = TaskMessage::prepareTodayMessages($this->arMaster['ID']);
                break;
            case Stage::getMasterWeekStageId():
                $this->chat->deleteAllMessages();
                $messages = TaskMessage::prepareWeekMessages($this->arMaster['ID']);
                break;
            case Stage::getMasterMonthStageId():
                $this->chat->deleteAllMessages();
                $messages = TaskMessage::prepareMonthMessages($this->arMaster['ID']);
                break;
            default:
                return;
        }
        foreach ($messages as $message) {
            $this->chat->sendMessage($message['TEXT'], $message['BUTTONS'], true, $message['CODE']);
        }
    }

    protected function getCurrentStageCode():string
    {
        return '';
    }

    protected function getNextStageCode(): string
    {
        return '';
    }
}