<?php

namespace Webrex\TelegramWebinar\Command;

use Webrex\Telegram\Bot\StageButton;

class WithAuthor extends Command
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
        $this->chat->sendMessage('<b>Тариф "С АВТОРОМ КУРСА"

На текущем тарифе ваши работы будут проверяться автором курса Дарьей Казимовой</b>

Стоимость тарифа - 32000₽
Скидка на курс будет действовать только до 15 мая (включительно). 
Можно оформить в рассрочку 👍

Ссылка на оплату с максимальной скидкой: https://payform.ru/4d24I0p/');
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