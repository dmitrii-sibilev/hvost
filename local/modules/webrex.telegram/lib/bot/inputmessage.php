<?php

namespace Webrex\Telegram\Bot;

use Webrex\Telegram\Helpers\MessageTypes;

class InputMessage
{
    private int $messageId;
    private string $messageText;
    private string $messageType;
    private int $chatId;
    private string $username;
    private string $firstName;
    private int $callbackId;
    private array $replyKeyboard;
    private bool  $changeMemberStatus;

    public function __construct(string $telegramJson)
    {
        $this->changeMemberStatus = false;
        $telegramData = json_decode($telegramJson, true);
        if ($telegramData['message']) {
            $arMessage = $telegramData['message'];
            $this->messageText = $arMessage['text'];
            if ($arMessage['entities']) {
                $this->messageType = $arMessage['entities'][0]['type'];//TODO: Разобраться с массивом entities
            } else {
                $this->messageType = '';
            }

        }
        $this->callbackId = 0;
        $this->replyKeyboard = [];
        if ($telegramData['callback_query']) {
            $this->callbackId = $telegramData['callback_query']['id'];
            $this->messageText = $telegramData['callback_query']['data'];
            $this->messageType = MessageTypes::BOT_COMMAND;
            $arMessage = $telegramData['callback_query']['message'];
            $this->replyKeyboard = $arMessage['reply_markup'];
        }

        $arChat = $arMessage['chat'];
        $this->messageId = $arMessage['message_id'] ?: 0;
        if ($telegramData['my_chat_member']) {
            $this->changeMemberStatus = true;
            $arChat = $telegramData['my_chat_member']['chat'];
            if ($telegramData['my_chat_member']['new_chat_member']['user']['id'] != 6195189784) {
                return;
            }
            $this->messageText = $telegramData['my_chat_member']['new_chat_member']['status'];
        }

        $this->chatId = $arChat['id'];
        $this->username = $arChat['username'] ?: '';
        $this->firstName = $arChat['first_name'] ?: '';
    }

    /**
     * @return mixed|string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed|string
     */
    public function getMessageType()
    {
        return $this->messageType;
    }

    /**
     * @return mixed|string
     */
    public function getMessageText()
    {
        return $this->messageText;
    }

    /**
     * @return int|mixed
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @return mixed|string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return int|mixed
     */
    public function getChatId()
    {
        return $this->chatId;
    }

    /**
     * @return int|mixed
     */
    public function getCallbackId()
    {
        return $this->callbackId;
    }

    /**
     * @return array|mixed
     */
    public function getReplyKeyboard()
    {
        return $this->replyKeyboard;
    }

    /**
     * @return bool
     */
    public function isChangeMemberStatus(): bool
    {
        return $this->changeMemberStatus;
    }
}