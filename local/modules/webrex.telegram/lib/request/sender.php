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
        if ($arResponse['error_code']) {
            Log::addError('sender->sendRequest ' . $arResponse['description']);
        }
        return $arResponse['result'];
    }

    public function sendAsync($action, $arQueriesData)
    {
        //create the multiple cURL handle
        $multiHandle = curl_multi_init();
        $arChannels = [];
        foreach ($arQueriesData as $queryParams) {
            $channel = curl_init(self::API_URL . $this->token . '/' . $action);
            curl_setopt_array($channel, [
                CURLOPT_HEADER         => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POST           => 1,
                CURLOPT_POSTFIELDS     => $queryParams,
            ]);
            curl_multi_add_handle($multiHandle, $channel);
            $arChannels[] = $channel;
        }

        do {
            $status = curl_multi_exec($multiHandle, $active);
            if ($active) {
                // Wait a short time for more activity
                curl_multi_select($multiHandle);
            }
        } while ($active && $status == CURLM_OK);

        foreach ($arChannels as $channel) {
            curl_multi_remove_handle($multiHandle, $channel);
        }
        curl_multi_close($multiHandle);
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
            'url'          => $url,
            'secret_token' => $secretToken,
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