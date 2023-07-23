<?

namespace Webrex\Telegram\Helpers;

use Bitrix\Main\Config\Option as BitrixOption;

class Option
{
    const MODULE_ID = 'webrex.telegram';
    private static function get($optionName)
    {
        return BitrixOption::get(self::MODULE_ID, $optionName, '');
    }

    private static function set($optionName, $value)
    {
        BitrixOption::set(self::MODULE_ID, $optionName, $value);
    }

    public static function getBotToken(): string
    {
        return self::get('BOT_TOKEN');
    }

    public static function setBotToken(string $token)
    {
        self::set('BOT_TOKEN', $token);
    }

    public static function getWebhookToken(): string
    {
        return self::get('WEBHOOK_SECRET_TOKEN');
    }

    public static function setWebhookToken(string $token)
    {
        self::set('WEBHOOK_SECRET_TOKEN', $token);
    }
}