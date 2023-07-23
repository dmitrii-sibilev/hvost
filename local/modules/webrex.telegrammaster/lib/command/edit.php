<?php

namespace Webrex\TelegramMaster\Command;

use Webrex\Telegram\Bot\Stage;
use Webrex\TelegramMaster\Helpers\Log;
use Webrex\TelegramMaster\Helpers\TaskMessage;

class Edit extends Command
{
    protected function actionsBeforeProcess()
    {
        $this->chat->updateStage(Stage::getMasterEditStageId());
    }
    protected function actionsAfterProcess()
    {

    }
    protected function process()
    {
        $chatMessage = $this->chat->getInputChatMessage();
        $taskId = explode('-', $chatMessage->get('CODE'))[1];
        if (!$taskId) {
            return;
        }
        $message = TaskMessage::prepareEditMessage($taskId);
        $this->chat->editInputMessageText($message['TEXT'], $message['BUTTONS'], $message['CODE']);
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