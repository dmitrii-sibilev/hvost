<?php

namespace Webrex\Telegram\Command;

use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Bot\StageButton;
use Webrex\Telegram\Helpers\Log;

class CourseInfo extends Command
{
    protected function actionsBeforeProcess()
    {
        return;
    }
    protected function actionsAfterProcess()
    {
        return;
    }

    protected function process()
    {
        $arButtons = $this->prepareButtons();
        $this->chat->sendMessage('Выбери категорию своего вопроса:', $arButtons);
    }

    protected function getCurrentStageCode():string
    {
        return Stage::COURSE_INFO_STAGE_CODE;
    }

    protected function getNextStageCode(): string
    {
        return '';
    }
}