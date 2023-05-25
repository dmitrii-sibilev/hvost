<?php
namespace Webrex\Telegram\Model;

use Bitrix\Main\Type\DateTime;
use Bitrix\Main\ORM\{Data\DataManager, Fields\DatetimeField, Fields\IntegerField, Fields\StringField, Fields\TextField};

use Bitrix\Main\SystemException;

class TelegramChatMessageTable extends DataManager
{
    /**
     * Возвращает название таблицы БД
     * @return string
     */
    public static function getTableName()
    {
        return 'webrex_telegram_chat_message';
    }

    /**
     * Возвращает описание полей сущности
     * @return array
     * @throws SystemException
     */
    public static function getMap()
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),
            (new DatetimeField('MESSAGE_TIME'))
                ->configureDefaultValue(static function()
                {
                    return new DateTime();
                }),
            (new StringField('SENDER_TYPE')),
            (new IntegerField('CHAT_ID')),
            (new IntegerField('CHAT_MESSAGE_ID')),
            (new TextField('MESSAGE_TEXT')),
        ];
    }
}