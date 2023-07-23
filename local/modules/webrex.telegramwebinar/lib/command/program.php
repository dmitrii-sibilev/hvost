<?php

namespace Webrex\TelegramWebinar\Command;

use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Bot\StageButton;
use Webrex\Telegram\Helpers\Log;

class Program extends Command
{
    const FILE_ID = 'BQACAgIAAxkBAAIxW2RXqFPexiL5V1Gf6UUXsQdasZcxAAIwKwAC97rBSjvGv2ldteg4LwQ';
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
        $this->chat->sendDocument(self::FILE_ID);
        $this->chat->sendMessage('<b>Общая длительность курса - 2 месяца</b>

За это время ты научишься держать уверенно в руках ножницы, научишься мыть\сушить, делать гигиеническую стрижку.
Подстрижешь минимум 9 собак пород йорк, шпиц и 
пудель, и сдашь экзаменационную работу.

Также ты узнаешь что такое тримминг, как выполнять экспресс-линьку собакам и кошкам и стричь кошек', $arButtons);
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