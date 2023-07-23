<?php
namespace Webrex\Telegram\Model;

use Bitrix\Main\ORM\{Data\DataManager,
    Fields\IntegerField,
    Fields\StringField};

use Bitrix\Main\SystemException;

class TelegramBotTable extends DataManager
{
    /**
     * Возвращает название таблицы БД
     * @return string
     */
    public static function getTableName()
    {
        return 'webrex_telegram_bot';
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
            (new StringField('CODE')),
            (new StringField('NAME')),
        ];
    }
}