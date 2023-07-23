<?php

namespace Webrex\TelegramWebinar\Command;

use Webrex\Telegram\Bot\StageButton;

class WithMentor extends Command
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
        $this->chat->sendMessage('<b>Тариф "С НАСТАВНИКОМ"

На текущем тарифе вы будете получать обратную связь по вашей работе от нашего наставника онлайн-программ</b>, которая является действующим грумером

Стоимость тарифа - 22900₽
Скидка на курс будет действовать только до 15 мая
(включительно).
Можно оформить в рассрочку 👍

Ссылка на оплату с максимальной скидкой: https://payform.ru/eu1YQFK/');
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