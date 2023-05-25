<?php

namespace Webrex\Telegram\Command;

use Webrex\Telegram\Bot\Stage;

class BeforeStartMessage extends Command
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
            $this->chat->sendMessage('<b>Через 15 минут НАЧИНАЕМ!!!</b> 
Перед входом проверь, что у тебя отключен VPN, так как он может помешать тебе смотреть урок и получать новые знания, а пока можешь налить себе чай и подготовится к уроку.

<b>Рекомендуем досмотреть урок до конца! Тебя ждет там кое-что очень важное</b>', $arButtons);
        }
    }

    protected function prepareButtons(): array
    {
        $rowBtn[] = [
            'text' => 'ССЫЛКА НА ТРАНСЛЯЦИЮ',
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