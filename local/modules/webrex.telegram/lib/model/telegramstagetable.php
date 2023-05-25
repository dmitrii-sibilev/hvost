<?php
namespace Webrex\Telegram\Model;

use Bitrix\Main\ORM\{Data\DataManager,
    Fields\BooleanField,
    Fields\IntegerField,
    Fields\StringField};

use Bitrix\Main\SystemException;

class TelegramStageTable extends DataManager
{
    /**
     * Возвращает название таблицы БД
     * @return string
     */
    public static function getTableName()
    {
        return 'webrex_telegram_stage';
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