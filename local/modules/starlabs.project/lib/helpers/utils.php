<?php
namespace Starlabs\Project\Helpers;

use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;
use Starlabs\Tools\helpers\Utils as ToolsUtils;

class Utils extends ToolsUtils
{
    public static function formatDateToJs(Date $Date)
    {
        return $Date->format('Y-m-d') . "T00:00:00.000Z";
    }

    public static function getDateTimeFromJs($jsDateTime)
    {
        return new DateTime($jsDateTime, 'Y-m-d\TH:i:s', new \DateTimeZone('Europe/Samara'));
    }
}
