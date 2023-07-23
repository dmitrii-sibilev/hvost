<?php
namespace Webrex\TelegramWebinar\Command;

use Webrex\Telegram\Bot\Stage;

class JoinCourse extends Command
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
        $this->chat->sendMessage('<b>Выбери подходящий тебе тариф участия в онлайн курсе.</b>
Курс можно оплатить в рассрочку без %', $arButtons);
    }

    protected function getCurrentStageCode():string
    {
        return Stage::PAYMENT_INFO_STAGE_CODE;
    }

    protected function getNextStageCode(): string
    {
        return '';
    }
}