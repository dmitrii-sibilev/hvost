<?php

namespace Webrex\TelegramWebinar\Command;

use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Bot\StageButton;

class FinalMessage extends Command
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
        $this->chat->sendMessage('<b>Привет 👋! Предобучение на онлайн курсе уже началось!🔥
Сегодня последний день действия скидок! Не упускай шанс обучиться по выгодной цене.</b>

Нажимай кнопку ниже и начинай обучение грумингу!', $arButtons);
    }

    protected function getCommandButtons()
    {
        return StageButton::getStageButtons($this->getNextStageId());
    }

    protected function getCurrentStageCode():string
    {
        return '';
    }

    protected function getNextStageCode(): string
    {
        return Stage::FINAL_STAGE_CODE;
    }
}