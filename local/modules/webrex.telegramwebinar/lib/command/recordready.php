<?php

namespace Webrex\TelegramWebinar\Command;

use Webrex\Telegram\Bot\Stage;

class RecordReady extends Command
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
//        if ($this->checkCurrentStage()) {
            $arButtons = $this->prepareButtons();
            $this->chat->sendMessage('<b>УРА, запись готова! Смотри урок</b> 👇👇', $arButtons);
//        }
    }

    protected function prepareButtons($oneTime = true): array
    {
        $rowBtn[] = [
            'text' => 'СМОТРЕТЬ УРОК',
            'url' => 'https://www.youtube.com/live/eIMrucu0GjY?feature=share&t=1620'
        ];
        $preparedButtons['inline_keyboard'][] = $rowBtn;

        if (!$preparedButtons['inline_keyboard']) {
            return [];
        }

        return $preparedButtons;
    }


    protected function getCurrentStageCode():string
    {
        return '';
//        return Stage::NO_WATCHED_STAGE_CODE;
    }

    protected function getNextStageCode(): string
    {
//        return Stage::RECORD_READY_STAGE_CODE;
        return '';
    }
}