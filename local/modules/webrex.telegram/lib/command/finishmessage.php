<?php

namespace Webrex\Telegram\Command;

use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Bot\StageButton;
use Webrex\Telegram\Helpers\Log;

class FinishMessage extends Command
{
    protected function actionsBeforeProcess()
    {
        return;
    }
    protected function actionsAfterProcess()
    {
        $this->chat->updateStage(Stage::getFinishStageId());
    }
    protected function process()
    {
        if ($this->checkCurrentStage()) {

            $this->chat->updateStage($this->getNextStageId());
            $arButtons = $this->prepareButtons();
            $this->chat->sendMessage('<b>Закончили 🙌! Спасибо за то, что был(а) с нами.
Скажи, тебе удалось посмотреть урок?</b>

Все полезные методички станут тебе доступны после урока', $arButtons);
        }
    }

    protected function getCurrentStageCode():string
    {
        return Stage::REGISTERED_STAGE_CODE;
    }

    protected function getCommandButtons()
    {
        return StageButton::getStageButtons($this->getNextStageId());
    }

    protected function getNextStageCode(): string
    {
        return Stage::FINISH_STAGE_CODE;
    }
}