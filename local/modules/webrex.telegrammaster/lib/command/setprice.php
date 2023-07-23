<?php

namespace Webrex\TelegramMaster\Command;

use Starlabs\Project\Grooming\Tasks;
use Webrex\Telegram\Bot\Chat;
use Webrex\Telegram\Bot\Stage;
use Webrex\TelegramMaster\Helpers\Log;
use Webrex\TelegramMaster\Helpers\TaskMessage;

class SetPrice extends Command
{
    private $newSum;
    public function __construct(Chat $chat, $newSum)
    {
        $this->newSum = $newSum;
        parent::__construct($chat);
    }

    protected function actionsBeforeProcess()
    {
        $this->chat->updateStage($this->chat->getChatEntityObject()->get('PREVIOUS_STAGE_ID'));
    }
    protected function actionsAfterProcess()
    {

    }
    protected function process()
    {
        $priceMessage = $this->chat->findEditingPriceMessage();
        if (!$priceMessage) {
            $this->chat->sendMessage('Выберите задачу для редактирования');
        }
        $dealId = explode('-', $priceMessage->get('CODE'))[1];
        $Deal = new \CCrmDeal(false);
        $arDealFields = [
            'OPPORTUNITY' => $this->newSum
        ];
        $Deal->Update($dealId,$arDealFields);
        $task = Tasks::getGroomingTaskByDealId($dealId);
        $newDescription = preg_replace('/(Сумма -) (\d+)/', '$1 '. $this->newSum, $task['DESCRIPTION']);
        $taskItem = new \CTaskItem($task['ID'], 1);
        $arTaskFields['DESCRIPTION'] = $newDescription;
        $taskItem->update($arTaskFields);
        $this->chat->deleteMessage($priceMessage->get('CHAT_MESSAGE_ID'));
        $this->chat->deleteInputMessage();
//        $this->chat->sendMessage($message['TEXT'], $message['BUTTONS'], true, $message['CODE']);
    }

    protected function getCurrentStageCode():string
    {
        return '';
    }

    protected function getNextStageCode(): string
    {
        return '';
    }
}