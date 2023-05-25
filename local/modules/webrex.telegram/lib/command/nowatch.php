<?php

namespace Webrex\Telegram\Command;

use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Bot\StageButton;
use Webrex\Telegram\Helpers\Log;

class NoWatch extends Command
{
    protected function actionsBeforeProcess()
    {
        if ($this->checkCurrentStage()) {
            $this->chat->updateStage($this->getNextStageId());
        }
    }
    protected function actionsAfterProcess()
    {
        return;
    }
    protected function process()
    {
        $arButtons = $this->prepareButtons(false);
        $this->chat->sendMessage('<b>Жаль, что ты не смог(ла) быть с нами в онлайн. Давай так 🙌, мы сделаем в ближайшее время запись и пришлём ее тебе сюда. Как тебе такая идея?</b>
После просмотра ты получишь все полезные методические материалы  😉

А пока, можешь почитать про программу курса, на котором
ты можешь научиться грумингу!
Скидка на курс будет действовать только до 15 мая включительно!

Сайт курса: https://www.abcgrooming.ru/start-online-groomer/', $arButtons);
    }

    protected function getCurrentStageCode():string
    {
        return Stage::FINISH_STAGE_CODE;
    }

    protected function getCommandButtons()
    {
        return StageButton::getStageButtons($this->getNextStageId());
    }

    protected function getNextStageCode(): string
    {
        return Stage::NO_WATCHED_STAGE_CODE;
    }
}