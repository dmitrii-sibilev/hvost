<?php
namespace Starlabs\Project\Calendar\Model;

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\UserTable;
use Bitrix\Main\ORM\{Data\DataManager,
    Fields\DatetimeField,
    Fields\IntegerField,
    Fields\DateField,
    Fields\Relations\Reference,
    Fields\StringField,
    Fields\TextField};

use Bitrix\Main\SystemException;

class CalendarNotesTable extends DataManager
{
	/**
	 * Возвращает название таблицы БД
	 * @return string
	 */
	public static function getTableName()
	{
		return 'calendar_notes';
	}

	/**
	 * Возвращает описание полей сущности
	 * @return array
	 * @throws SystemException
     * CREATE TABLE calendar_notes
    (
    ID INT (18) UNSIGNED NOT NULL AUTO_INCREMENT,
    MASTER_ID INT (18),
    SALON_ID INT (18) NOT NULL,
    TIME_START datetime NOT NULL,
    TIME_FINISH datetime NOT NULL,
    TITLE varchar(255) not null,
    DESCRIPTION text,
    PRIMARY KEY (ID)
    );
	 */
	public static function getMap()
	{
		return [
			(new IntegerField('ID'))
				->configurePrimary()
				->configureAutocomplete(),
			(new IntegerField('MASTER_ID'))
                ->configureRequired(false),
			(new IntegerField('SALON_ID')),
			(new IntegerField('TASK_ID'))
                ->configureRequired(false),
            (new DatetimeField('TIME_START')),
            (new DatetimeField('TIME_FINISH')),
            (new StringField('TITLE')),
            (new TextField('DESCRIPTION'))
                ->configureRequired(false),
            (new Reference(
				'MASTER',
				UserTable::getEntity(),
				[
					'=this.MASTER_ID' => 'ref.ID',
				]
			)),
            (new Reference(
				'SALON',
				ElementTable::getEntity(),
				[
					'=this.SALON_ID' => 'ref.ID',
				]
			)),
		];
	}
}