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


    public function __construct(int $chatId)
    {
        $this->chatId = $chatId;
        $this->haveInputMessage = false;
        $this->chatExist = false;
        $this->chatActive = false;
        $chatResult = TelegramChatTable::query()
            ->setSelect(['*'])
            ->setFilter(['CHAT_ID' => $this->chatId])
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

    public function sendMessage(string $messageText, array $arButtons = [])
    {
        $arParams = [
            'chat_id' => $this->chatId,
            'text' => $messageText,
            'parse_mode' => 'HTML'
        ];
        if ($arButtons) {
            $arParams['reply_markup'] = json_encode($arButtons);
        }
        $sender = new Sender(Option::get('BOT_TOKEN'));
        $arSentMessage = $sender->sendMessage($arParams);
        $this->saveOutputMessage($arSentMessage['message_id'], $messageText);
    }

    public function answerCallbackQuery(int $callbackQueryId)
    {
        $arParams['callback_query_id'] = $callbackQueryId;
        $sender = new Sender(Option::get('BOT_TOKEN'));
        $sentMessage = $sender->answerCallbackQuery($arParams);
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
        $sender = new Sender(Option::get('BOT_TOKEN'));
        $arSentMessage = $sender->sendDocument($arParams);
        $this->saveOutputMessage($arSentMessage['message_id'], $fileId);
    }

    public function editInputMessageText(string $messageText)
    {
        $arParams = [
            'chat_id' => $this->chatId,
            'message_id' => $this->inputMessage->getMessageId(),
            'text' => $messageText,
        ];
        $replyMarkup = $this->inputMessage->getReplyKeyboard();
        if ($replyMarkup) {
            $arParams['reply_markup'] = json_encode($replyMarkup);
        }
        $sender = new Sender(Option::get('BOT_TOKEN'));
        $arSentMessage = $sender->editMessageText($arParams);
        $this->saveOutputMessage($arSentMessage['message_id'], $messageText);
    }

    private function saveOutputMessage($messageId, $messageText)
    {
        $data = [
            'SENDER_TYPE' => self::TYPE_OUTPUT,
            'CHAT_ID' => $this->getChatId(),
            'CHAT_MESSAGE_ID' => $messageId,
            'MESSAGE_TEXT' => $messageText,
        ];
        TelegramChatMessageTable::add($data);
    }


    public function deleteInputMessage()
    {
        $this->deleteMessage($this->inputMessage->getMessageId());
    }

    public function deleteMessage(int $messageId)
    {
        $sender = new Sender(Option::get('BOT_TOKEN'));
        $arParams = [
            'chat_id' => $this->chatId,
            'message_id' => $messageId
        ];
        $sender->deleteMessage($arParams);
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
            $sender = new Sender(Option::get('BOT_TOKEN'));
            $arParams = [
                'chat_id' => $this->chatId,
                'message_id' => $this->getInputMessage()->getMessageId()
            ];
            $sender->editInlineMarkup($arParams);
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
}