<?php

namespace Webrex\TelegramWebinar\Command;

use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Bot\StageButton;
use Webrex\Telegram\Helpers\Log;

class Register extends Command
{
    protected function actionsBeforeProcess()
    {
        if (!$this->chat->isChatExist()) {
            $this->chat->saveNew();
        } else {
            $this->chat->updateStage(Stage::getRegisteredStageId());
        }
        if ($this->checkCurrentStage()) {
            $this->chat->deleteInputMessage();
        }

    }
    protected function actionsAfterProcess()
    {
        if ($this->checkCurrentStage()) {
//            $morning = new MorningMessage($this->chat);
//            $morning->do();
//            $beforeStart = new BeforeStartMessage($this->chat);
//            $beforeStart->do();
//            $startMes = new StartMessage($this->chat);
//            $startMes->do();
//            $finMes = new FinishMessage($this->chat);
//            $finMes->do();
            $recordReady = new RecordReady($this->chat);
            $recordReady->do();
            $finish = new FinishMessage($this->chat);
            $finish->do();
        }
    }
    protected function process()
    {
        if ($this->checkCurrentStage()) {
            $this->chat->sendMessage('<b>Поздравляем! Ты зарегистрирован(а) на урок!</b> 
Далее тебе ничего делать не нужно, я позабочусь о тебе сам 😉

Я пришлю тебе напоминание и ссылку на урок <b>12 МАЯ</b>
<b>Рекомендую подписаться на канал:</b> https://t.me/online_grooming , там больше полезной информацию на тему урока ❤️');
        }
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