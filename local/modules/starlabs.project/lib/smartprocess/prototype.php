<?php

namespace Starlabs\Project\SmartProcess;

use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\ORM\Entity;

abstract class Prototype
{
    protected $Factory;
    protected $arType;

    public function __construct()
    {
        $container = \Bitrix\Crm\Service\Container::getInstance();
        $typeTable = $container->getDynamicTypeDataClass();
        $entityType = $typeTable::query()
            ->setSelect(["NAME", "TABLE_NAME", "ENTITY_TYPE_ID"])
            ->setFilter(["NAME" => $this->getSmartProcessName()])
            ->exec()
            ->fetch();
        $this->arType = $entityType;
        $this->Factory = $container->getFactory($entityType["ENTITY_TYPE_ID"]);
    }

    abstract protected function getSmartProcessName():string;

    abstract protected function getPropertiesCode():array;

    /**
     * @return Query
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public function createQuery(): Query
    {
        return new Query($this->Factory->getDataClass());
    }

    /**
     * @return \Bitrix\Crm\Service\Factory|null
     */
    public function getFactory(): ?\Bitrix\Crm\Service\Factory
    {
        return $this->Factory;
    }

    /**
     * @param $id
     * @return \Bitrix\Main\ORM\Objectify\EntityObject|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getObjectById($id)
    {
        return $this->createQuery()
            ->setSelect(array_merge(["TITLE"], $this->getPropertiesCode()))
            ->setFilter(["ID" => $id])
            ->exec()->fetchObject();
    }

    /**
     * @param $arFields
     * @return int
     */
    public function add($arFields):int
    {
        return $this->getFactory()->createItem($arFields)->save()->getId();
    }

    /**
     * @param string $fieldName
     * @return string
     */
    protected function getFullFieldCode(string $fieldName):string
    {
        return 'UF_' . $this->getFactory()->getUserFieldEntityId() . '_' . $fieldName;
    }
}