<?php

namespace Webrex\TelegramWebinar\Command;

use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Bot\StageButton;
use Webrex\Telegram\Helpers\Log;

class YesWatch extends Command
{
    protected function actionsBeforeProcess()
    {
        if ($this->checkCurrentStage()) {
            $this->chat->updateStage($this->getNextStageId());
        }
    }
    protected function actionsAfterProcess()
    {
        $this->chat->clearReplyMarkup();
    }
    protected function process()
    {
        $arButtons = $this->prepareButtons();
        $this->chat->sendMessage('<b>Отлично! Теперь ты знаешь все о профессии грумер!</b>

Методические материалы находятся тут 👉 
https://drive.google.com/drive/folders/1YbQp6nQvdtQ0RfUjWb4sy0Dwwti18nKG?usp=share_link

<b>КАК НА СЧЕТ ТОГО, чтобы начать обучение и навсегда
изменить свою жизнь?</b>
Мы рассказали тебе о программе нашего курса - УЧАСТВУЕШЬ?
<b>Сайт курса:</b> 
https://www.abcgrooming.ru/start-online-groomer/', $arButtons);
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
        return Stage::WATCHED_STAGE_CODE;
    }
}