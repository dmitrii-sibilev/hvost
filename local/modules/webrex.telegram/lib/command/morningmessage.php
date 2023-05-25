<?php

namespace Webrex\Telegram\Command;

use Webrex\Telegram\Bot\Stage;

class MorningMessage extends Command
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
        if ($this->checkCurrentStage()) {
            $arButtons = $this->prepareButtons();
            $this->chat->sendMessage('<b>Привет 👋 ! Ты помнишь, что нас сегодня ждут великие дела?
Сегодня в 18:00 по МСК</b>  пройдет урок, на котором Дарья Казимова расскажет тебе о том, как начать работать с животными и зарабатывать на этом.

<b>По окончании урока ты получишь гайды</b>, которые помогут тебе в уходе за животными, а также расскажем что нужно, чтобы ты мог(ла) начать работать грумером.', $arButtons);
        }
    }

    protected function prepareButtons(): array
    {
        $rowBtn[] = [
            'text' => 'ССЫЛКА НА ТРАНСЛЯЦИЮ',
            'url' => 'https://my.smartwebinar.info/HWF3MjJgKBhVprZJaCAG4'
        ];
        $preparedButtons['inline_keyboard'][] = $rowBtn;

        if (!$preparedButtons['inline_keyboard']) {
            return [];
        }

        return $preparedButtons;
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