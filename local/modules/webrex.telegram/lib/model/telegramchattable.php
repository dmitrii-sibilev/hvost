<?php
namespace Webrex\Telegram\Model;

use Bitrix\Main\Type\DateTime;
use Bitrix\Main\ORM\{Data\DataManager,
    Fields\BooleanField,
    Fields\DatetimeField,
    Fields\IntegerField,
    Fields\StringField};

use Bitrix\Main\SystemException;

class TelegramChatTable extends DataManager
{
    /**
     * Возвращает название таблицы БД
     * @return string
     */
    public static function getTableName()
    {
        return 'webrex_telegram_chat';
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
            (new IntegerField('CHAT_ID')),
            (new StringField('USERNAME')),
            (new StringField('FIRST_NAME')),
            (new IntegerField('STAGE_ID')),
            (new IntegerField('PREVIOUS_STAGE_ID')),
            (new BooleanField('ACTIVE')),
            (new DatetimeField('CREATED_TIME'))
                ->configureDefaultValue(static function()
                {
                    return new DateTime();
                }),
        ];
    }
}