<?php

namespace Webrex\TelegramMaster\Helpers;

use Bitrix\Main\Type\DateTime;

class Log {

    private static $path = '/upload/tg_log/';

    public static function add($message, $fileName = "log") {
        $currentDate = new DateTime();
        $fileName = $_SERVER['DOCUMENT_ROOT'] . self::$path . $fileName . $currentDate->format('Y-m-d') . '.log';
        file_put_contents($fileName, print_r('---------------------------' . PHP_EOL, true), FILE_APPEND);
        file_put_contents($fileName, print_r(date('d.m.Y H:i:s') . PHP_EOL, true), FILE_APPEND);
        file_put_contents($fileName, print_r($message, true), FILE_APPEND);
        file_put_contents($fileName, print_r(PHP_EOL . '---------------------------' . PHP_EOL, true), FILE_APPEND);
    }

    public static function addError($message)
    {
        self::add($message, "error");
    }
    public static function addDebug($message)
    {
        self::add($message, "debug");
    }
    
}