<?php

namespace Webrex\TelegramWebinar\Command;

use Webrex\Telegram\Bot\Stage;

class StartMessage extends Command
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
        if ($this->checkCurrentStage()) {
            $arButtons = $this->prepareButtons();
            $this->chat->sendMessage('<b>МЫ НАЧАЛИ! Заходи! Мы начинаем сразу со всей полезности, нажимай скорей</b>', $arButtons);
        }
    }

    protected function prepareButtons(): array
    {
        $rowBtn[] = [
            'text' => 'СМОТРЕТЬ УРОК',
            'url' => 'https://my.smartwebinar.info/HWF3MjJgKBhVprZJaCAG4'
        ];
        $preparedButtons['inline_keyboard'][] = $rowBtn;

        if (!$preparedButtons['inline_keyboard']) {
            return [];
        }

        return $preparedButtons;
    }


    protected function getCurrentStageCode():string
    {
        return Stage::REGISTERED_STAGE_CODE;
    }

    protected function getNextStageCode(): string
    {
        return '';
    }
}