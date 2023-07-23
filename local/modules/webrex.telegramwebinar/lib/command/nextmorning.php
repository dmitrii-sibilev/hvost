<?php

namespace Webrex\TelegramWebinar\Command;

use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Bot\StageButton;
use Webrex\Telegram\Helpers\Log;

class NextMorning extends Command
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
//        if ($this->checkCurrentStage()){
            $arButtons = $this->prepareButtons();
            $this->chat->sendMessage('<b>Посмотрел урок? Как тебе программа?
Напоминаем, что максимальная скидка на курс действует только ближайшие сутки,</b> а ведь скидка 50%, не упускай такую удачу

Методические материалы находятся по ссылке: 
https://drive.google.com/drive/folders/1YbQp6nQvdtQ0RfUjWb4sy0Dwwti18nKG?usp=share_link', $arButtons);
//        }
    }

    protected function getCurrentStageCode():string
    {
//        return '';
        return Stage::RECORD_READY_STAGE_CODE;
    }

    protected function getNextStageCode(): string
    {
        return '';
    }
    
}