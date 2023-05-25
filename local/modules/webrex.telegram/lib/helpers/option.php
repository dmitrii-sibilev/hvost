<?

namespace Webrex\Telegram\Helpers;

use Bitrix\Main\Config\Option as BitrixOption;

class Option
{
    const MODULE_ID = 'webrex.telegram';
    public static function get($optionName)
    {
        return BitrixOption::get(self::MODULE_ID, $optionName, '');
    }

    public static function set($optionName, $value)
    {
        BitrixOption::set(self::MODULE_ID, $optionName, $value);
    }
}