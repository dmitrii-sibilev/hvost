<?php
namespace Starlabs\Project\Grooming;

use Bitrix\Crm\ContactTable;
use Bitrix\Crm\DealTable;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Bitrix\Main\ORM\{Data\DataManager,
    Fields\DatetimeField,
    Fields\IntegerField,
    Fields\Relations\Reference};

use Bitrix\Main\SystemException;

class CashbackOperationsTable extends DataManager
{
	/**
	 * Возвращает название таблицы БД
	 * @return string
	 */
	public static function getTableName()
	{
		return 'cashback_operations';
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
			(new IntegerField('DEAL_ID')),
			(new IntegerField('CONTACT_ID')),
			(new IntegerField('PET_ID')),
			(new IntegerField('VALUE')),
			(new IntegerField('RESPONSIBLE_ID'))
                ->configureDefaultValue(static function()
                {
                    global $USER;
                    return $USER->GetID();
                }),
            (new DatetimeField('OPERATION_TIME'))
                ->configureDefaultValue(static function()
                {
                    return new DateTime();
                }),
            (new Reference(
				'DEAL',
				DealTable::getEntity(),
				[
					'=this.DEAL_ID' => 'ref.ID',
				]
			)),
            (new Reference(
				'CONTACT',
				ContactTable::getEntity(),
				[
					'=this.CONTACT_ID' => 'ref.ID',
				]
			)),
            (new Reference(
				'RESPONSIBLE',
				UserTable::getEntity(),
				[
					'=this.RESPONSIBLE_ID' => 'ref.ID',
				]
			)),
		];
	}
}