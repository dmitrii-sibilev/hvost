<?php

namespace Webrex\TelegramWebinar\Command;

use Webrex\Telegram\Bot\Chat;
use Webrex\Telegram\Bot\InputMessage;
use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Bot\StageButton;

abstract class Command
{
    protected Chat $chat;
    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    abstract protected function actionsBeforeProcess();
    abstract protected function actionsAfterProcess();
    abstract protected function process();
    abstract protected function getCurrentStageCode(): string;
    abstract protected function getNextStageCode(): string;
//    abstract public static function getStatusMessage(): string;


    public function do()
    {
        $this->sendCallbackAnswer();
        $this->actionsBeforeProcess();
        $this->process();
        $this->actionsAfterProcess();
    }

    protected function sendCallbackAnswer()
    {
        if (!$this->chat->haveInputMessage()) {
            return;
        }
        $callbackId = $this->chat->getInputMessage()->getCallbackId();
        if ($callbackId) {
            $this->chat->answerCallbackQuery($callbackId);
        }
    }

    protected function getCommandButtons()
    {
        return StageButton::getStageButtons($this->getCurrentStageId());
    }

    /**
     * @return array
     */
    protected function prepareButtons(): array
    {
        $arButtons = $this->getCommandButtons();
        $btnCount = 0;
        foreach ($arButtons as $arButton) {
            $btnCount++;
            $arRow = [
                'text' => $arButton['TEXT'],
                'callback_data' => $arButton['CODE'],
            ];
            if ($arButton['URL']) {
                $arRow['url'] = $arButton['URL'];
            }
            $rowBtn[] = $arRow;
            if (($btnCount % 2) == 0) {
                $preparedButtons['inline_keyboard'][] = $rowBtn;
                $rowBtn = [];
            }
        }
        if ($rowBtn) {
            $preparedButtons['inline_keyboard'][] = $rowBtn;
        }

        if (!$preparedButtons['inline_keyboard']) {
            return [];
        }

        return $preparedButtons;
    }

    /**
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function getCurrentStageId()
    {
        return Stage::getStageByCode($this->getCurrentStageCode())['ID'];
    }
    /**
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function getNextStageId()
    {
        return Stage::getStageByCode($this->getNextStageCode())['ID'];
    }

    /**
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function checkCurrentStage(): bool
    {
        $chatEntityObj = $this->chat->getChatEntityObject();
        if ($chatEntityObj->getStageId() == $this->getCurrentStageId()) {
            return true;
        }
        return false;
    }
}