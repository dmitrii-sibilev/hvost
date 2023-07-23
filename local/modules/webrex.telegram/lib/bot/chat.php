<?php

namespace Webrex\Telegram\Bot;

use Bitrix\Main\ORM\Objectify\EntityObject;
use Webrex\Telegram\Helpers\Log;
use Webrex\Telegram\Helpers\Option;
use Webrex\Telegram\Model\TelegramChatMessageTable;
use Webrex\Telegram\Model\TelegramChatTable;
use Webrex\Telegram\Request\Sender;

class Chat
{
    const TYPE_INPUT = 'INPUT';
    const TYPE_OUTPUT = 'OUTPUT';

    private int $chatId;
    private bool $chatExist;
    private bool $chatActive;
    private bool $haveInputMessage;
    private InputMessage $inputMessage;
    private EntityObject $chatEntityObject;
    private Sender $sender;
    private int $botId;


    public function __construct(int $chatId, string $botToken)
    {
        $this->chatId = $chatId;
        $this->haveInputMessage = false;
        $this->chatExist = false;
        $this->chatActive = false;
        $this->sender = new Sender($botToken);
        $this->botId = Bot::getIdByToken($botToken);
        $chatResult = TelegramChatTable::query()
            ->setSelect(['*'])
            ->setFilter(['CHAT_ID' => $this->chatId, 'BOT_ID' => $this->botId])
            ->exec();
        if ($chatObj = $chatResult->fetchObject()) {
            $this->chatExist = true;
            $this->chatActive = $chatObj->getActive();
            $this->chatEntityObject = $chatObj;
        }
    }

    /**
     * @return int
     */
    public function getChatId(): int
    {
        return $this->chatId;
    }

    /**
     * @return bool
     */
    public function isChatExist(): bool
    {
        return $this->chatExist;
    }

    public function saveNew()
    {
        $data = [
            'CHAT_ID' => $this->chatId,
            'USERNAME' => $this->inputMessage->getUsername(),
            'FIRST_NAME' => $this->inputMessage->getFirstName(),
            'STAGE_ID' => Stage::getRegisteredStageId(),
            'PREVIOUS_STAGE_ID' => '',
            'ACTIVE' => true,
            'BOT_ID' => $this->botId,
        ];
        $result = TelegramChatTable::add($data);
        $result->getId();
        $this->chatEntityObject = $result->getObject();
        $this->chatExist = true;
    }

    public function saveInputMessage()
    {
        $data = [
            'SENDER_TYPE' => self::TYPE_INPUT,
            'CHAT_ID' => $this->getChatId(),
            'CHAT_MESSAGE_ID' => $this->inputMessage->getMessageId(),
            'MESSAGE_TEXT' => $this->inputMessage->getMessageText(),
            'BOT_ID' => $this->botId,
            'CODE' => TelegramChatMessageTable::USUAL_MESSAGE
        ];
        if ($this->inputMessage->isChangeMemberStatus()) {
            if ($this->inputMessage->getMessageText() == '/kicked') {
                $this->deactivate();
            } elseif ($this->inputMessage->getMessageText() == '/member'){
                $this->activate();
            }
        }
        TelegramChatMessageTable::add($data);
    }

    public function activate()
    {
        $this->chatEntityObject->set('ACTIVE', true)->save();
    }

    public function deactivate()
    {
        $this->chatEntityObject->set('ACTIVE', false)->save();
    }

    public function sendMessage(string $messageText, array $arButtons = [], $disableNotify = false, $messageCode = '')
    {
        $arParams = [
            'chat_id' => $this->chatId,
            'text' => $messageText,
            'parse_mode' => 'HTML',
            'disable_notification' => $disableNotify,
        ];
        if ($arButtons) {
            $arParams['reply_markup'] = json_encode($arButtons);
        }
        $arSentMessage = $this->sender->sendMessage($arParams);
        $this->saveOutputMessage($arSentMessage['message_id'], $messageText, $messageCode);
    }

    public function answerCallbackQuery(int $callbackQueryId, $text = '', $showAlert = false)
    {
        $arParams['callback_query_id'] = $callbackQueryId;
        if ($text) {
            $arParams['text'] = $text;
        }
        if ($showAlert) {
            $arParams['show_alert'] = $showAlert;
        }
        $sentMessage = $this->sender->answerCallbackQuery($arParams);
    }

    public function sendDocument(string $fileId, array $arButtons = [])
    {
        $arParams = [
            'chat_id' => $this->chatId,
            'document' => $fileId,
        ];
        if ($arButtons) {
            $arParams['reply_markup'] = json_encode($arButtons);
        }
        $arSentMessage = $this->sender->sendDocument($arParams);
        $this->saveOutputMessage($arSentMessage['message_id'], $fileId);
    }

    public function editInputMessageText(string $messageText, $arButtons = [], $messageCode = '')
    {
        $replyKeyboard = $arButtons?: $this->inputMessage->getReplyKeyboard();

        $this->editMessageText($messageText,$this->inputMessage->getMessageId(), $replyKeyboard , $messageCode);
    }

    public function editMessageText(string $messageText, int $messageId, $replyKeyboard = [], $messageCode = '')
    {
        $arParams = [
            'chat_id' => $this->chatId,
            'message_id' => $messageId,
            'text' => $messageText,
            'parse_mode' => 'HTML',
        ];
        $replyMarkup = $replyKeyboard;
        if ($replyMarkup) {
            $arParams['reply_markup'] = json_encode($replyMarkup);
        }
        $arSentMessage = $this->sender->editMessageText($arParams);
        $this->editOutputMessage($arSentMessage['message_id'], $messageText, $messageCode);
    }

    private function saveOutputMessage($messageId, $messageText, $messageCode = '')
    {
        if (!$messageCode) {
            $messageCode = TelegramChatMessageTable::USUAL_MESSAGE;
        }
        $data = [
            'SENDER_TYPE' => self::TYPE_OUTPUT,
            'CHAT_ID' => $this->getChatId(),
            'CHAT_MESSAGE_ID' => $messageId,
            'MESSAGE_TEXT' => $messageText,
            'BOT_ID' => $this->botId,
            'CODE' => $messageCode,
        ];
        TelegramChatMessageTable::add($data);
    }
    private function editOutputMessage($messageId, $messageText, $messageCode = '')
    {
        $currentMessage = TelegramChatMessageTable::query()
            ->setSelect(['ID'])
            ->setFilter(['CHAT_MESSAGE_ID' => $messageId, 'BOT_ID' => $this->botId, 'CHAT_ID' => $this->getChatId()])
            ->exec();
        if (!$message = $currentMessage->fetchObject()) {
            $this->saveOutputMessage($messageId, $messageText, $messageCode);
            return;
        }
        if (!$messageCode) {
            $messageCode = TelegramChatMessageTable::USUAL_MESSAGE;
        }
        $data = [
            'SENDER_TYPE' => self::TYPE_OUTPUT,
            'CHAT_ID' => $this->getChatId(),
            'CHAT_MESSAGE_ID' => $messageId,
            'MESSAGE_TEXT' => $messageText,
            'BOT_ID' => $this->botId,
            'CODE' => $messageCode,
        ];
        TelegramChatMessageTable::update($message->getId(), $data);
    }


    public function deleteInputMessage()
    {
        $this->deleteMessage($this->inputMessage->getMessageId());
    }

    public function deleteMessage(int $messageId)
    {
        $arParams = [
            'chat_id' => $this->chatId,
            'message_id' => $messageId
        ];
        $this->sender->deleteMessage($arParams);
    }

    public function updateStage(int $stageId)
    {
        $prevStage = $this->chatEntityObject->get('STAGE_ID');
        $updateData = [
            'STAGE_ID' => $stageId,
            'PREVIOUS_STAGE_ID' => $prevStage,
        ];
        $updateRes = TelegramChatTable::update($this->chatEntityObject->getId(), $updateData);
        $this->chatEntityObject = $updateRes->getObject();
    }

    public function clearReplyMarkup()
    {
        $callbackId = $this->getInputMessage()->getCallbackId();
        if ($callbackId) {
            $arParams = [
                'chat_id' => $this->chatId,
                'message_id' => $this->getInputMessage()->getMessageId()
            ];
            $this->sender->editInlineMarkup($arParams);
        }
    }

    /**
     * @return EntityObject
     */
    public function getChatEntityObject(): EntityObject
    {
        return $this->chatEntityObject;
    }

    /**
     * @param InputMessage $inputMessage
     */
    public function setInputMessage(InputMessage $inputMessage): void
    {
        $this->inputMessage = $inputMessage;
        $this->haveInputMessage = true;
    }

    /**
     * @return InputMessage
     */
    public function getInputMessage(): InputMessage
    {
        return $this->inputMessage;
    }

    /**
     * @return bool
     */
    public function haveInputMessage(): bool
    {
        return $this->haveInputMessage;
    }

    /**
     * @return bool
     */
    public function isChatActive(): bool
    {
        return $this->chatActive;
    }

    public function deleteAllMessages($code = '')
    {
        $messages = TelegramChatMessageTable::query()
            ->setSelect(['ID', 'CHAT_MESSAGE_ID'])
            ->setFilter(['BOT_ID' => $this->botId, 'CHAT_ID' => $this->chatId, '!CODE' => TelegramChatMessageTable::NOTIFY_MESSAGE]);
        if ($code) {
            $messages->whereLike('CODE', "%$code%");
        }
        $arQueries = [];
        while ($message = $messages->fetchObject()) {
            $arQueries[] = [
                'chat_id' => $this->chatId,
                'message_id' => $message->get('CHAT_MESSAGE_ID')
            ];
            $message->delete();
        }

        $this->sender->sendAsync('deleteMessage', $arQueries);
    }

    public function getChatMessage($messageId)
    {
        return TelegramChatMessageTable::query()
            ->setSelect(['*'])
            ->setFilter(['CHAT_MESSAGE_ID' => $messageId, 'BOT_ID' => $this->botId])
            ->exec()->fetchObject();
    }

    public function getInputChatMessage()
    {
        return $this->getChatMessage($this->inputMessage->getMessageId());
    }

    public function findEditingPriceMessage()
    {
        $messages = TelegramChatMessageTable::query()
            ->setSelect(['ID', 'CHAT_MESSAGE_ID', 'CODE'])
            ->setFilter(['BOT_ID' => $this->botId, 'CHAT_ID' => $this->chatId])
            ->whereLike('CODE', '%' . TelegramChatMessageTable::DEAL_PRICE_MESSAGE_PREFIX .'%')
            ->exec();
        if ($message = $messages->fetchObject()) {
            return $message;
        }
        return false;
    }
}