<?php

namespace Webrex\TelegramMaster\Command;

use Webrex\Telegram\Bot\Stage;
use Webrex\TelegramMaster\Helpers\Log;
use Webrex\TelegramMaster\Helpers\TaskMessage;

class DeleteNotify extends Command
{
    protected function actionsBeforeProcess()
    {

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