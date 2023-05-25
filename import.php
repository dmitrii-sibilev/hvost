<?php

use Bitrix\Crm\ContactTable;
use Bitrix\Crm\DealTable;
use Bitrix\Crm\PhaseSemantics;
use Bitrix\Crm\StatusTable;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\Type\Date;
use Bitrix\Main\UserPhoneAuthTable;
use Bitrix\Main\Web\HttpClient;
use Starlabs\Project\Grooming;
use Starlabs\Project\SmartProcess;
use Starlabs\Project\Iblock;
use Starlabs\Project\Helpers\Utils;
use Starlabs\Project\Personal\Assistant;
use Starlabs\Project\Personal\Master;
use Starlabs\Project\WorkSchedule\Model\WorkScheduleTable;
use Starlabs\Project\WorkSchedule\ScheduleList;
use Starlabs\Tools\Helpers\Log;
use Starlabs\Project\Grooming\Deal;
use Starlabs\Tools\Helpers\p;
use Webrex\Telegram\Model\TelegramChatTable;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
\Bitrix\Main\Loader::includeModule('webrex.telegram');
try {
    $chatQuery = TelegramChatTable::query()
        ->setSelect(['*'])
        ->exec();
    while ($chat = $chatQuery->fetchObject()) {
        $arChats[$chat->getChatId()] = $chat;
    }
    $messageQuery = \Webrex\Telegram\Model\TelegramChatMessageTable::query()
        ->setSelect(['*'])
        ->setOrder(['MESSAGE_TIME' => 'ASC'])
        ->exec();
    $arMessages = [];
    while ($message = $messageQuery->fetch()) {
        if (!$arMessages[$message['CHAT_ID']]) {
            $arMessages[$message['CHAT_ID']] = $message['MESSAGE_TIME'];
        }
    }
    foreach ($arMessages as $chatId => $date) {
        if (!$arChats[$chatId]) {
            continue;
        }
        $arChats[$chatId]->set('CREATED_TIME', $date)->save();
    }

} catch (\Throwable $exception) {
    \Starlabs\Tools\Helpers\p::init($exception->getMessage());
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>