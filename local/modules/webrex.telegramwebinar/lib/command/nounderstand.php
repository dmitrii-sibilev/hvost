<?php

namespace Webrex\TelegramWebinar\Command;

use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Bot\StageButton;
use Webrex\Telegram\Helpers\Log;

class NoUnderstand extends Command
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
        $arButtons = $this->prepareButtons(false);
        $this->chat->sendMessage('<b>Напоминаю тебе, что я только чат-бот, который, к сожалению, не умеет читать твои сообщения.</b>
Если у тебя есть вопрос или тебе необходима консультация менеджера, то напиши, пожалуйста Елене https://t.me/voronkovaen', $arButtons);
    }

    protected function getCurrentStageCode():string
    {
        return '';
    }

    protected function getCommandButtons()
    {
        return StageButton::getStageButtons($this->chat->getChatEntityObject()->getStageId());
    }

    protected function getNextStageCode(): string
    {
        return '';
    }
    
}