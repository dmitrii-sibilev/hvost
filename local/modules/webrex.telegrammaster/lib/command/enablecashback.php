<?php

namespace Webrex\TelegramMaster\Command;

use Bitrix\Main\Loader;
use Starlabs\Project\Grooming\Deal;
use Starlabs\Project\Grooming\Tasks;
use Webrex\Telegram\Bot\Chat;
use Webrex\Telegram\Bot\Stage;
use Webrex\TelegramMaster\Helpers\Log;
use Webrex\TelegramMaster\Helpers\TaskMessage;

class EnableCashback extends Command
{
    protected function actionsBeforeProcess()
    {
        $this->chat->updateStage($this->chat->getChatEntityObject()->get('PREVIOUS_STAGE_ID'));
    }
    protected function actionsAfterProcess()
    {

    }
    protected function process()
    {
        Loader::includeModule('tasks');
        $message = $this->chat->getInputChatMessage();
        $taskId = explode('-', $message->get('CODE'))[1];
        $dealId = Tasks::getDealIdByTaskId($taskId);
        $Deal = new \CCrmDeal(false);
        $arDealFields = [
            Deal::FIELD_USE_CASHBACK_CODE => true
        ];
        $Deal->Update($dealId,$arDealFields);
        $taskMessage = TaskMessage::prepareTaskMessageByTaskId($taskId);
        $this->chat->editInputMessageText($taskMessage['TEXT'], $taskMessage['BUTTONS'], $taskMessage['CODE']);
    }

    protected function getCurrentStageCode():string
    {
        return '';
    }

    protected function getNextStageCode(): string
    {
        return '';
    }

    protected function sendCallbackAnswer()
    {
        if (!$this->chat->haveInputMessage()) {
            return;
        }
        $callbackId = $this->chat->getInputMessage()->getCallbackId();
        if ($callbackId) {
            $this->chat->answerCallbackQuery($callbackId, 'Кешбек включен');
        }
    }
}