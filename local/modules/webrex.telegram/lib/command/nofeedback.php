<?php

namespace Webrex\Telegram\Command;

use Webrex\Telegram\Bot\StageButton;

class NoFeedback extends Command
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
        $this->chat->sendMessage('<b>Тариф "БЕЗ ОБРАТНОЙ СВЯЗИ"

На текущем тарифе вам предоставляется доступ к курсу для самостоятельного изучения.</b> Кураторы не будут осуществлять проверку ваших практических работ.

Стоимость тарифа - 14900₽
Скидка на тариф действует до 15.05 включительно 
Можно оформить рассрочку 👍

Ссылка на оплату с максимальной скидкой: https://payform.ru/7b1YQBI/');
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