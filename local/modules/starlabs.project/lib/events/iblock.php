<?php
namespace StarLabs\Project\Events;

use Bitrix\Main\EventManager;
use Starlabs\Project\Properties\BooleanProp;
use StarLabs\Tools\Events\HandlerInterface;

class Iblock implements HandlerInterface
{

    public static function setHandlers()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->addEventHandler(
            'iblock',
            "OnIBlockPropertyBuildList",
            [BooleanProp::class, "GetUserTypeDescription"]
        );
    }

}