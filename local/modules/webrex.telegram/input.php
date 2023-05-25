<?php
define("NOT_CHECK_PERMISSIONS", true);
define("NEED_AUTH", false);
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Webrex\Telegram\Bot\Chat;
use Webrex\Telegram\Command\CommandList;
use Webrex\Telegram\Bot\InputMessage;
use Webrex\Telegram\Helpers\Log;
use Webrex\Telegram\Helpers\MessageTypes;
use Webrex\Telegram\Helpers\Option;

if (
    !Loader::includeModule("webrex.telegram")
) {
    die("Не установлены требуемые модули");
}
$request = Context::getCurrent()->getRequest();
$header = $request->getHeader('X-Telegram-Bot-Api-Secret-Token');

try {
    if ($header != Option::get('WEBHOOK_SECRET_TOKEN')) {
        throw new Exception('API_TOKEN_INVALID');
    }
    Log::add(json_decode(file_get_contents('php://input'), true));
    $inputMessage = new InputMessage(file_get_contents('php://input'));

    $chat = new Chat($inputMessage->getChatId());
    $chat->setInputMessage($inputMessage);
    $chat->saveInputMessage();
    if ($inputMessage->isChangeMemberStatus()) {
        return;
    }
    if (!$chat->isChatActive() && $chat->isChatExist()) {
        return;
    }
    if ($inputMessage->getMessageType() === MessageTypes::BOT_COMMAND) {
        $command = CommandList::getCommandByCode($chat, $inputMessage->getMessageText());
        $command->do();
    } else {
        $command = CommandList::getCommandByMessage($chat, $inputMessage->getMessageText());
        $command->do();
    }
} catch (\Throwable $exception) {
    Log::addError($exception->getFile() . ' ' . $exception->getLine() . ' ' . $exception->getMessage());
}
