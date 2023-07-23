<?php

namespace Webrex\TelegramMaster\Command;

use Starlabs\Project\Personal\Master;
use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Bot\StageButton;
use Webrex\Telegram\Helpers\Log;

class Date extends Command
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
2 - /tomorrow - Список задач на Завтра
3 - /yesterday - Список задач на Завтра
4 - /week - Список задач на неделю вперед
5 - /date - Список задач на конкретную дату
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