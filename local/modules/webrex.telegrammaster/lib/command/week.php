<?php

namespace Webrex\TelegramMaster\Command;

use Starlabs\Project\Personal\Master;
use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Bot\StageButton;
use Webrex\Telegram\Helpers\Log;
use Webrex\TelegramMaster\Helpers\TaskMessage;

class Week extends Command
{
    protected function actionsBeforeProcess()
    {

    }
    protected function actionsAfterProcess()
    {
        $this->chat->updateStage(Stage::getMasterWeekStageId());
    }
    protected function process()
    {
        $this->chat->deleteAllMessages();
        $messages = TaskMessage::prepareWeekMessages($this->arMaster['ID']);
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