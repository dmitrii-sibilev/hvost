<?php

namespace Starlabs\Project\Iblock;

class MasterQualification extends IblockPrototype
{
    const IBLOCK_API_CODE = "MastersQualification";
    const IBLOCK_CODE = "MastersQualification";
    const TOP_ELEMENT_CODE = "top";

    protected function getIblockApiCode():string
    {
        return self::IBLOCK_API_CODE;
    }
}