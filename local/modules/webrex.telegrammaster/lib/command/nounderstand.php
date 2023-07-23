<?php

namespace Webrex\TelegramMaster\Command;

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
        $this->chat->sendMessage('Команда не найдена. Если есть вопросы или что-то не работает, напишите разработчику https://t.me/wayfqrer', $arButtons);
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