<?php

namespace Webrex\TelegramWebinar\Command;

use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Bot\StageButton;
use Webrex\Telegram\Helpers\Log;

class Stuff extends Command
{
    const FILE_ID = 'BQACAgIAAxkBAAIxWWRXpobPBR2qJCgdXfdSlpE8SEm5AAILKwAC97rBSrhj_6y1owd2LwQ';
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
        $this->chat->sendMessage('<b>Для прохождения курса тебе понадобится инструмент, косметика и оборудование.</b> Весь перечень на картинке, а чек-лист с ссылками на товары лежит в гугл-диске

 (https://drive.google.com/drive/folders/1YbQp6nQvdtQ0RfUjWb4sy0Dwwti18nKG?usp=share_link)Для консультации по подбору можешь обращаться к менеджеру Елене https://t.me/voronkovaen

<b>Также тебе понадобятся модели пород: йорк, шпиц, пудель.</b> 
Минимум по 3 собаки каждой породы для прохождения курса', $arButtons);
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