<?php

namespace Webrex\TelegramMaster\Command;

use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Model\TelegramChatMessageTable;
use Webrex\TelegramMaster\Helpers\Log;
use Webrex\TelegramMaster\Helpers\TaskMessage;

class Price extends Command
{
    protected function actionsBeforeProcess()
    {
        $this->chat->getChatEntityObject()->set('STAGE_ID', Stage::getMasterEditPriceStageId())->save();
    }
    protected function actionsAfterProcess()
    {

    }
    protected function process()
    {
        $chatMessage = $this->chat->getInputChatMessage();
        $taskId = explode('-', $chatMessage->get('CODE'))[1];
        if (!$taskId) {
            return;
        }
        $this->chat->deleteAllMessages(TelegramChatMessageTable::DEAL_PRICE_MESSAGE_PREFIX);
        $message = TaskMessage::preparePriceMessage($taskId);
        $this->chat->sendMessage($message['TEXT'], $message['BUTTONS'], true, $message['CODE']);
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
            $this->chat->answerCallbackQuery($callbackId, 'Отправьте сумму числом, в рублях', true);
        }
    }
}