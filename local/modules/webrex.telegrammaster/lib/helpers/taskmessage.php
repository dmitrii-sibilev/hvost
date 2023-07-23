<?php

namespace Webrex\TelegramMaster\Helpers;

use Bitrix\Main\Type\Date;
use Starlabs\Project\Grooming\Deal;
use Starlabs\Project\Grooming\Tasks;
use Webrex\Telegram\Model\TelegramChatMessageTable;

class TaskMessage
{
    private static $daysOfWeek = [
        0 => 'воскресенье',
        1 => 'понедельник',
        2 => 'вторник',
        3 => 'среда',
        4 => 'четверг',
        5 => 'пятница',
        6 => 'суббота',
    ];
    public static function prepareTodayMessages($masterId)
    {
        $tasks = Tasks::getTodayMasterTasks($masterId);
        return self::prepareTasksMessages($tasks);
    }

    public static function prepareWeekMessages($masterId)
    {
        $tasks = Tasks::getWeekMasterTasks($masterId);
        return self::prepareTasksMessages($tasks);
    }

    public static function prepareMonthMessages($masterId)
    {
        $tasks = Tasks::getMonthMasterTasks($masterId);
        return self::prepareTasksMessages($tasks);
    }

    private static function prepareTasksMessages($tasks)
    {
        $arMessages = [];
        $days = [];
        foreach ($tasks as $task) {
            $taskDate = new Date($task['START_DATE_PLAN'], 'd.m.Y H:i:s');
            $taskDay = $taskDate->format('d.m.Y');
            if (!in_array($taskDay, $days)) {
                $days[] = $taskDay;
                $arMessages[] = [
                    'TEXT' => '<b>⏬⏬⏬⏬⏬⏬⏬⏬⏬
Задачи на ' . $taskDay . ' (' . self::$daysOfWeek[$taskDate->format('w')] . '):
⏬⏬⏬⏬⏬⏬⏬⏬⏬</b>',
                    'BUTTONS' => []
                ];
            }
            $arTaskMessage = self::prepareTaskMessage($task);
            $arMessages[] = $arTaskMessage;
        }
        return $arMessages;
    }

    private static function inlineprepareTasksMessages($tasks)
    {
        $arMessages = [];
        $days = [];
        foreach ($tasks as $taskId => $task) {
            $taskDate = new Date($task['START_DATE_PLAN'], 'd.m.Y H:i:s');
            $taskDay = $taskDate->format('d.m.Y');
            if (!in_array($taskDay, $days)) {
                $days[] = $taskDay;
                $arMessages[0] .= '
<b>⏬⏬⏬⏬⏬⏬⏬⏬⏬
Задачи на ' . $taskDay . ' (' . self::$daysOfWeek[$taskDate->format('w')] . '):</b>

'
                ;
            }
            $arTaskMessage = self::prepareTaskMessage($task);
            $arMessages[0] .= '
' . $arTaskMessage['TEXT'] . '
/edit_' . $taskId . ' ⬆️Редактировать запись⬆️
/finish_' . $taskId . ' ⬆️Редактировать запись⬆️
';
        }
        return $arMessages;
    }

    public static function prepareTaskMessage($arTask)
    {
        $arTask['DESCRIPTION'] = str_replace('<br>', '
', $arTask['DESCRIPTION']);
        $arMessage['BUTTONS'] = [
            'inline_keyboard' => [
                [
                    [
                        'text'          => 'Завершить⬆️',
                        'callback_data' => '/finish',
                    ],
                    [
                        'text'          => 'Редактировать⬆️',
                        'callback_data' => '/edit',
                    ],
                ],
            ]
        ];
        $arMessage['TEXT'] = $arTask['DESCRIPTION'];
        $arMessage['CODE'] = TelegramChatMessageTable::TASK_MESSAGE_PREFIX . $arTask['ID'];
        return $arMessage;
    }

    public static function prepareTaskMessageByTaskId($taskId)
    {
        $arTask = Tasks::getById($taskId);
        return self::prepareTaskMessage($arTask);
    }

    public static function prepareFinishMessage($taskId)
    {
        $arTask = Tasks::getById($taskId);
        $arTask['DESCRIPTION'] = str_replace('<br>', '
', $arTask['DESCRIPTION']);
        $dealId = explode('D_', $arTask['UF_CRM_TASK'][0])[1];
        $arMessage['BUTTONS'] = [
            'inline_keyboard' => [
                [
                    [
                        'text'          => '⬅️Отмена',
                        'callback_data' => '/back',
                    ],
                ],
                [
                    [
                        'text'          => '✅ Услуга оказана',
                        'callback_data' => '/success',
                    ],
                    [
                        'text'          => '❌ Не пришел',
                        'callback_data' => '/fail',
                    ],
                ],
            ]
        ];

        $Cashback = new \Starlabs\Project\Grooming\Cashback($dealId);
        $cashbackBalance = $Cashback->getAvailableCashback();

        if ($Cashback->getCurrentDeal()->get(Deal::FIELD_USE_CASHBACK_CODE) && $cashbackBalance > 0) {
            $sumString = '💵 <b>Сумма к оплате - ' . ($Cashback->getCurrentDeal()->getOpportunity() - $cashbackBalance) . "руб. (с учетом скидки по кешбэку - " . $cashbackBalance . 'руб.)</b>';
        } else {
            $sumString = '💵 <b>Сумма к оплате - ' . $Cashback->getCurrentDeal()->getOpportunity() . 'руб.</b>';
        }

        $arMessage['TEXT'] = $arTask['DESCRIPTION'] . '

' . $sumString;
        $arMessage['CODE'] = TelegramChatMessageTable::TASK_MESSAGE_PREFIX . $taskId;
        return $arMessage;
    }

    public static function prepareEditMessage($taskId)
    {
        $arTask = Tasks::getById($taskId);
        $arTask['DESCRIPTION'] = str_replace('<br>', '
', $arTask['DESCRIPTION']);
        $dealId = explode('D_', $arTask['UF_CRM_TASK'][0])[1];
        $Cashback = new \Starlabs\Project\Grooming\Cashback($dealId);
        $cashbackBalance = $Cashback->getAvailableCashback();
        $cashbackButton = [
            'text' => $Cashback->getCurrentDeal()->get(Deal::FIELD_USE_CASHBACK_CODE) ? '❌ Отключить кешбек' : '✅ Активировать кешбек',
            'callback_data' => $Cashback->getCurrentDeal()->get(Deal::FIELD_USE_CASHBACK_CODE) ? '/disable-cashback' : '/enable-cashback',
        ];
        $arMessage['BUTTONS'] = [
            'inline_keyboard' => [
                [
                    [
                        'text'          => '⬅️Отмена',
                        'callback_data' => '/back',
                    ],
                ],
                [
                    $cashbackButton,
                ],
                [
                    [
                        'text'          => 'Изменить стоимость',
                        'callback_data' => '/price',
                    ],
                ],
            ]
        ];

        if ($cashbackBalance > 0) {
            $sumString = '<b>Сумма к оплате - ' . ($Cashback->getCurrentDeal()->getOpportunity() - $cashbackBalance) . "руб. (с учетом скидки по кешбэку - " . $cashbackBalance . 'руб.)</b>';
        } else {
            $sumString = '<b>Сумма к оплате - ' . $Cashback->getCurrentDeal()->getOpportunity() . 'руб.</b>';
        }

        $arMessage['TEXT'] = $arTask['DESCRIPTION'] . '

' . $sumString;
        $arMessage['CODE'] = TelegramChatMessageTable::TASK_MESSAGE_PREFIX . $taskId;
        return $arMessage;
    }

    public static function preparePriceMessage($taskId)
    {
        $arTask = Tasks::getById($taskId);
        $arTask['DESCRIPTION'] = str_replace('<br>', '
', $arTask['DESCRIPTION']);
        foreach ($arTask["UF_CRM_TASK"] as $string) {
            $arString = explode('_', $string);
            if ($arString[0] == \CCrmOwnerTypeAbbr::Deal) {
                $dealId = $arString[1];
                break;
            }
        }
        if (!$dealId) {
            throw new \Exception('Не найдена сделка');
        }
        $arMessage['BUTTONS'] = [
            'inline_keyboard' => [
                [
                    [
                        'text'          => '⬅️Отмена',
                        'callback_data' => '/cancel-update',
                    ],
                ],
            ]
        ];

        $arMessage['TEXT'] = $arTask['DESCRIPTION'] . '
Отправьте новую стоимость в рублях сообщением: ⬇️';
        $arMessage['CODE'] = TelegramChatMessageTable::DEAL_PRICE_MESSAGE_PREFIX . $dealId;
        return $arMessage;
    }
}