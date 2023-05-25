<?php
namespace Webrex\Telegram\Request;

use Bitrix\Main\Web\HttpClient;
use Webrex\Telegram\Helpers\Log;

class Sender
{
    const API_URL = 'https://api.telegram.org/bot';
    private $token;
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * @param $action
     * @param $arParameters
     * @return mixed
     * @throws \Exception
     */
    private function sendRequest($action, $arParameters = [])
    {
        $httpClient = new HttpClient();
        $responseJson = $httpClient->post(self::API_URL . $this->token . '/' . $action, $arParameters);
        $arResponse = json_decode($responseJson, true);
        if ($arResponse['error_code']){
            Log::addError('sender->sendRequest ' . $arResponse['description']);
        }
        return $arResponse['result'];
    }

    /**
     * @param $arParams
     * @return mixed
     * @throws \Exception
     */
    public function sendMessage($arParams)
    {
        return $this->sendRequest('sendMessage', $arParams);
    }

    public function sendDocument($arParams)
    {
        return $this->sendRequest('sendDocument', $arParams);
    }

    public function answerCallbackQuery($arParams)
    {
        return $this->sendRequest('answerCallbackQuery', $arParams);
    }

    public function deleteMessage($arParams)
    {
        return $this->sendRequest('deleteMessage', $arParams);
    }
    public function editInlineMarkup($arParams)
    {
        return $this->sendRequest('editMessageReplyMarkup', $arParams);
    }

    public function editMessageText($arParams)
    {
        return $this->sendRequest('editMessageText', $arParams);
    }

    public function setWebhook($url, $secretToken)
    {
        $arParams = [
            'url' => $url,
            'secret_token' => $secretToken
        ];
        return $this->sendRequest('setWebhook', $arParams);
    }

    public function getWebhookInfo()
    {
        return $this->sendRequest('getWebhookInfo');
    }
    public function forwardMessage($arParams)
    {
        return $this->sendRequest('forwardMessage', $arParams);
    }
}