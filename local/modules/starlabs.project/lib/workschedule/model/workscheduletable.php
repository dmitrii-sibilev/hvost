<?php
namespace Starlabs\Project\WorkSchedule\Model;

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\UserTable;
use Bitrix\Main\ORM\{
    Data\DataManager,
    Fields\IntegerField,
    Fields\DateField,
    Fields\Relations\Reference
};

use Bitrix\Main\SystemException;

class WorkScheduleTable extends DataManager
{
	/**
	 * Возвращает название таблицы БД
	 * @return string
	 */
	public static function getTableName()
	{
		return 'work_schedule';
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
			(new IntegerField('MASTER_ID')),
			(new IntegerField('SALON_ID')),
			(new IntegerField('ASSISTANT_ID'))
				->configureRequired(false),
            (new DateField('WORK_DATE')),
            (new Reference(
				'MASTER',
				UserTable::getEntity(),
				[
					'=this.MASTER_ID' => 'ref.ID',
				]
			)),
            (new Reference(
				'ASSISTANT',
				UserTable::getEntity(),
				[
					'=this.ASSISTANT_ID' => 'ref.ID',
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