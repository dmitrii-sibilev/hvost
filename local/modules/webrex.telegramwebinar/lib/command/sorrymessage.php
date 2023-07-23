<?php

namespace Webrex\TelegramWebinar\Command;

use Webrex\Telegram\Bot\Stage;

class SorryMessage extends Command
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
        $this->chat->sendMessage('<b>Привет еще раз, Друг!</b> Я заметил, что у тебя могла не сработать кнопка для регистрации. Теперь я все исправил и готов записать тебя на наш урок! Нажимай на кнопку ниже и я сообщу тебе всю информацию!', $arButtons);
    }

    protected function prepareButtons(): array
    {
        $rowBtn[] = [
            'text' => 'Начать',
            'callback_data' => '/start',
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
    }

    protected function getNextStageCode(): string
    {
        return '';
    }
}