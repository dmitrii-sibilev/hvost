<?php

namespace Webrex\TelegramMaster\Command;

use Webrex\Telegram\Bot\Stage;
use Webrex\TelegramMaster\Helpers\Log;
use Webrex\TelegramMaster\Helpers\TaskMessage;

class CancelUpdate extends Command
{
    protected function actionsBeforeProcess()
    {
        $this->chat->updateStage($this->chat->getChatEntityObject()->get('PREVIOUS_STAGE_ID'));
    }
    protected function actionsAfterProcess()
    {

    }
    protected function process()
    {
        $this->chat->deleteInputMessage();
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