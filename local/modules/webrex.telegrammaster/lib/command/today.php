<?php

namespace Webrex\TelegramMaster\Command;

use Webrex\Telegram\Bot\Stage;
use Webrex\TelegramMaster\Helpers\Log;
use Webrex\TelegramMaster\Helpers\TaskMessage;

class Today extends Command
{
    protected function actionsBeforeProcess()
    {

    }
    protected function actionsAfterProcess()
    {
        $this->chat->updateStage(Stage::getMasterTodayStageId());
    }
    protected function process()
    {
        $this->chat->deleteAllMessages();
        $messages = TaskMessage::prepareTodayMessages($this->arMaster['ID']);
        if (!$messages) {
            $this->chat->sendMessage('Записей не найдено');
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