<?php

namespace Webrex\TelegramMaster\Command;

use Webrex\Telegram\Bot\Chat;
use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Model\TelegramChatMessageTable;
use Webrex\TelegramMaster\Helpers\Log;
use Webrex\TelegramMaster\Helpers\TaskMessage;

class NotifyTask extends Command
{
    private $notifyMessage;
    public function __construct(Chat $chat, string $notifyMessage)
    {
        parent::__construct($chat);
        $this->notifyMessage = $notifyMessage;
    }

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
        $this->chat->sendMessage($this->notifyMessage, $arButtons, true, TelegramChatMessageTable::NOTIFY_MESSAGE);
    }

    protected function prepareButtons(): array
    {
        $rowBtn[] = [
            'text' => 'OK',
            'callback_data' => '/delete-notify',
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
        return '';
    }
}