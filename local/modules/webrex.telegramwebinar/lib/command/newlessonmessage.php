<?php

namespace Webrex\TelegramWebinar\Command;

use Webrex\Telegram\Bot\Stage;

class NewLessonMessage extends Command
{
    protected function actionsBeforeProcess()
    {
        return;
    }
    protected function actionsAfterProcess()
    {
        $this->chat->updateStage($this->getNextStageId());
    }
    protected function process()
    {
        $arButtons = $this->prepareButtons();
        $this->chat->sendMessage('<b>Привет, Друг!</b> Ранее я присылал тебе информацию об уроке для владельцев собак, надеемся данный урок был для тебя полезен! Все полезные материалы, а также запись урока останутся в данном чате❤️

<b>Сейчас наша команда готовится к старту онлайн курса для тех, кто хочет стать грумером и обрести профессию.</b> Если тебе интересно данное направление обучения, то нажимай кнопку "НАЧАТЬ" и 12 мая мы расскажем тебе о том, как можно изменить свою жизнь и начать заниматься грумингом профессионально ✂️', $arButtons);
    }

    protected function prepareButtons(): array
    {
        $rowBtn[] = [
            'text' => 'Начать',
            'callback_data' => '/start',
        ];
        $preparedButtons['inline_keyboard'][] = $rowBtn;

        if (!$preparedButtons['inline_keyboard']) {
            return [];
        }

        return $preparedButtons;
    }

    protected function getCurrentStageCode():string
    {
        return '';
    }

    protected function getNextStageCode(): string
    {
        return Stage::ASK_BEGIN_STAGE_CODE;
    }
}