<?php
namespace StarLabs\Project\Events;

use Bitrix\Main\EventManager;
use Bitrix\Main\Event;
use StarLabs\Tools\Events\HandlerInterface;
use Starlabs\Tools\Helpers\p;

class Main implements HandlerInterface
{

    public static function setHandlers()
    {
        $eventManager = EventManager::getInstance();

//        $eventManager->addEventHandler(
//            'main',
//            'OnEpilog',
//            [self::class, 'addCustomJsOnEpilog']
//        );
    }

}