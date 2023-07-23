<?php

namespace Webrex\TelegramMaster\Command;

use Starlabs\Project\Personal\Master;
use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Bot\StageButton;
use Webrex\Telegram\Helpers\Log;

class Register extends Command
{
    protected function actionsBeforeProcess()
    {
        if (!$this->chat->isChatExist()) {
            $this->chat->saveNew();
        } else {
            $this->chat->updateStage(Stage::getMasterRegisteredStageId());
        }
//        if ($this->checkCurrentStage()) {
            $this->chat->deleteInputMessage();
//        }

    }
    protected function actionsAfterProcess()
    {

    }
    protected function process()
    {
        $masters = new Master();
        $currentUser = $masters->getByTelegramChatId($this->chat->getChatId());
        if ($currentUser) {
            $message = $currentUser['NAME'] . ' ' . $currentUser['LAST_NAME'] . ', вы успешно зарегистрированы!';
        } else {
            $message = 'Я не смог найти вас в системе, напишите @wayfqrer, чтобы он добавил ваc в базу. Ваш код - ' . $this->chat->getChatId();
        }
//        if ($this->checkCurrentStage()) {
            $this->chat->sendMessage('<b>Добро пожаловать в бот-помощник для управления задачами</b> 
' . $message . ' 
С помощью команды /help вы можете увидеть список возможных действий. Попробуйте!');
//        }
    }

    protected function getCurrentStageCode():string
    {
        return Stage::MASTER_REGISTERED_STAGE_CODE;
    }

    protected function getNextStageCode(): string
    {
        return '';
    }
}