<?php

namespace Webrex\TelegramWebinar\Command;

use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Bot\StageButton;
use Webrex\Telegram\Helpers\Log;

class ManagerContact extends Command
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
        $this->chat->sendMessage('<b>Если у тебя остался вопрос, на который может ответить только менеджер</b>, то переходи по ссылке и Елена ответит на любой твой вопрос https://t.me/voronkovaen', $arButtons);
    }

    protected function getCurrentStageCode():string
    {
        return '';
    }

    protected function getNextStageCode(): string
    {
        return '';
    }
    protected function getCommandButtons()
    {
        return StageButton::getStageButtons($this->chat->getChatEntityObject()->getStageId());
    }
}