<?php

namespace Webrex\Telegram\Bot;

use Throwable;
use Webrex\Telegram\Helpers\Log;
use Webrex\Telegram\Model\TelegramChatMessageTable;
use Webrex\Telegram\Model\TelegramChatTable;
use Webrex\Telegram\Model\TelegramStageTable;

class Stage
{
    const BEGIN_STAGE_CODE = 'BEGIN';
    const REGISTERED_STAGE_CODE = 'REGISTERED';
    const FINISH_STAGE_CODE = 'FINISH';
    const WATCHED_STAGE_CODE = 'WATCHED';
    const NO_WATCHED_STAGE_CODE = 'NO_WATCHED';
    const RECORD_READY_STAGE_CODE = 'RECORD_READY';
    const JOIN_COURSE_STAGE_CODE = 'JOIN_COURSE';
    const FAQ_STAGE_CODE = 'FAQ';
    const FINAL_STAGE_CODE = 'FINAL';
    const ASK_BEGIN_STAGE_CODE = 'ASK_BEGIN';
    const COURSE_INFO_STAGE_CODE = 'COURSE_INFO';
    const PAYMENT_INFO_STAGE_CODE = 'PAYMENT_INFO';

    /**
     * @param string $code
     * @return array|false
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getStageByCode(string $code)
    {
        return TelegramStageTable::query()
            ->setSelect(['*'])
            ->setFilter(['CODE' => $code])
            ->fetch();
    }

    /**
     * @return array|false
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getBeginStage()
    {
        return self::getStageByCode(self::BEGIN_STAGE_CODE);
    }

    public static function getRegisteredStageId()
    {
        return self::getStageByCode(self::REGISTERED_STAGE_CODE)['ID'];
    }

    public static function getFinishStageId()
    {
        return self::getStageByCode(self::FINISH_STAGE_CODE)['ID'];
    }

    public static function getNoWatchedStageId()
    {
        return self::getStageByCode(self::NO_WATCHED_STAGE_CODE)['ID'];
    }
    public static function getRecordReadyStageId()
    {
        return self::getStageByCode(self::RECORD_READY_STAGE_CODE)['ID'];
    }

    public static function getAskBeginStageId()
    {
        return self::getStageByCode(self::ASK_BEGIN_STAGE_CODE)['ID'];
    }
}