<?php

namespace Starlabs\Project\Iblock;

abstract class IblockPrototype
{
    protected $Entity;

    /**
     * @return \Bitrix\Main\ORM\Query\Query
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function createQuery()
    {
        return new \Bitrix\Main\ORM\Query\Query($this->getEntity());
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        if (!$this->Entity) {
            $this->Entity = \Bitrix\Iblock\IblockTable::compileEntity($this->getIblockApiCode());
        }
        return $this->Entity;
    }

    abstract protected function getIblockApiCode():string;
}