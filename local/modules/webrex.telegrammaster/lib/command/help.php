<?php

namespace Webrex\TelegramMaster\Command;

use Starlabs\Project\Personal\Master;
use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Bot\StageButton;
use Webrex\Telegram\Helpers\Log;

class Help extends Command
{
    protected function actionsBeforeProcess()
    {

    }
    protected function actionsAfterProcess()
    {

    }
    protected function process()
    {
            $this->chat->sendMessage('Список доступных команд: 
1 - /today - Список задач на сегодня
2 - /week - Список задач на неделю вперед
3 - /date - Список задач на конкретную дату
');
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