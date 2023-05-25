<?php

use Bitrix\Crm\ContactTable;
use Starlabs\Project\Grooming;
use Starlabs\Project\SmartProcess;
use Starlabs\Project\Iblock;
use Starlabs\Project\Helpers\Utils;
use Starlabs\Project\Personal\Assistant;
use Starlabs\Project\Personal\Master;
use Starlabs\Project\WorkSchedule\Model\WorkScheduleTable;
use Starlabs\Project\WorkSchedule\ScheduleList;
use Starlabs\Tools\Helpers\Log;
use Starlabs\Project\Grooming\Deal;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
\Bitrix\Main\Loader::includeModule('crm');

$arMaster = [
    "id"       => 18,
    "is_trimm" => true,
];

$Product = new Iblock\Products();

$res = $Product->createQuery()
    ->setSelect(["ID", "NAME", "BREED_" => $Product::PROP_BREED_CODE])
    ->exec();

while ($prod = $res->fetch()) {
    if (strpos($prod["NAME"], 'Тримминг')) {
        $arProducts[(int)$prod["BREED_VALUE"]]["trimid"] = $prod["ID"];
    } else {
        $arProducts[(int)$prod["BREED_VALUE"]]["otherProds"][] = $prod["ID"];
    }
}

$arBreeds = [
    "акита ину" => 48,
    "алабай" => 52,
    "аляскинский маламут" => 49,
    "американская акита" => 50,
    "афганская борзая" => 51,
    "африканский бурбуль" => 330,
    "аффен-пинчер" => 331,
    "бассенджи" => 53,
    "бассет-хаунд" => 54,
    "бедлингтон терьер" => 55,
    "бельгийская овчарка" => 56,
    "бернский зенненхунд" => 57,
    "бивер йоркширский терьер" => 58,
    "бигль" => 59,
    "бишон" => 60,
    "бладхаунд" => 332,
    "бобтейл" => 61,
    "боксер" => 62,
    "болонез" => 63,
    "бордер колли" => 64,
    "бордер терьер" => 65,
    "бордосский дог" => 333,
    "бриар" => 334,
    "брюсельский грифон" => 66,
    "буль мастиф" => 70,
    "бульдог американский" => 67,
    "бульдог английский" => 68,
    "бультерьер" => 69,
    "вельш терьер" => 71,
    "вельш-корги" => 72,
    "вест-хайленд-вайт терьер" => 73,
    "восточно-европейская овчарка" => 74,
    "грейхаунд" => 335,
    "далматин" => 75,
    "джек-рассел терьер (парсен-рассел)" => 76,
    "джек-рассел терьер жесткошер." => 77,
    "доберман" => 78,
    "дог" => 79,
    "дратхаар" => 80,
    "дратхаар (тримминг)" => 80,
    "дэнди даймонд терьер" => 336,
    "ирландский волкодав" => 81,
    "ирландский терьер" => 82,
    "ирландский терьер (тримминг)" => 82,
    "йоркипу" => 84,
    "йоркширский терьер" => 85,
    "йоркширский терьер (от 6 кг)" => 179,
    "кавказская овчарка" => 87,
    "кеесхонд" => 89,
    "керн терьер" => 337,
    "керри-блю терьер" => 88,
    "китайская хохлатая голая" => 90,
    "китайская хохлатая пуховка" => 91,
    "кламбер спаниель" => 338,
    "коккер спаниель американский" => 92,
    "коккер спаниель английский" => 93,
    "колли гладкошерстная" => 94,
    "колли длинношерстная" => 95,
    "кошки" => 329,
    "кролик" => 328,
    "курцхаар" => 96,
    "лабрадор" => 97,
    "лабрадудель" => 98,
    "лайка (большая)от 15кг" => 99,
    "лайка (маленькая) до 15кг" => 180,
    "левретка" => 100,
    "леонбергер" => 101,
    "лхасский апсо" => 102,
    "мальтез" => 106,
    "мальтипу" => 107,
    "мастино-неаполетано" => 103,
    "мастиф английский" => 104,
    "мастиф испанский" => 105,
    "испанский мастиф" => 105,
    "мейн-кун" => 327,
    "метис (более 20 кг)" => 182,
    "метис (до 20 кг)" => 181,
    "метис (до 7 кг)" => 108,
    "миттельшнауцер" => 109,
    "мопс" => 110,
    "моск.длинношерст. той-терьер" => 111,
    "московский дракон" => 112,
    "немецкая овчарка" => 114,
    "норвич терьер" => 115,
    "норфолк терьер" => 116,
    "ньюфаундленд" => 113,
    "папильон" => 117,
    "пекинес" => 118,
    "петербургская орхидея" => 121,
    "пиренейская собака" => 119,
    "пит буль" => 120,
    "пти-брабансон" => 339,
    "пудель карликовый до 36 см" => 124,
    "пудель королевский" => 122,
    "пудель малый (средний) от 36см" => 123,
    "пудель той до 28 см" => 125,
    "пшеничный терьер" => 126,
    "ретривер (голден)" => 127,
    "риджбек" => 128,
    "ризеншнауцер" => 129,
    "ротвейлер" => 130,
    "русская борзая" => 131,
    "русская цветная болонка" => 132,
    "русский спаниель" => 133,
    "салюки" => 340,
    "самоед" => 134,
    "сенбернар" => 135,
    "сеттер" => 136,
    "сиба ину (шиба ину)" => 138,
    "сибирский хаски" => 137,
    "силихем терьер" => 341,
    "скай терьер" => 139,
    "скотч терьер" => 141,
    "спаниель кавалер кинг чарльз" => 140,
    "спрингер спаниель" => 342,
    "среднеазиатская овчарка" => 142,
    "стаффордшир терьер" => 143,
    "такса гладкошерстная стандарт" => 144,
    "такса длинношерстная стандарт" => 145,
    "такса жесткошерстная карликовая" => 147,
    "такса жесткошерстная стандарт" => 146,
    "такса кроличья гладкошерстная" => 148,
    "такса кроличья длинношерстная" => 149,
    "тибетский мастиф" => 150,
    "тибетский терьер (метис)" => 151,
    "той-терьер гладкошерстный" => 152,
    "той-терьер длинношерстный" => 153,
    "фараонова собака" => 343,
    "фила-бразильеро" => 344,
    "фландрский бувье" => 345,
    "фокс терьер гладкошерстный" => 154,
    "фокс терьер жесткошерстный" => 155,
    "французский бульдог" => 156,
    "ханаанская собака" => 346,
    "хин японский" => 157,
    "цверг пинчер" => 159,
    "цвергшнауцер" => 158,
    "чау-чау" => 160,
    "черный терьер" => 161,
    "чи-хуа-хуа гладкошерстная" => 162,
    "чи-хуа-хуа длинношерстная" => 163,
    "шарпей" => 164,
    "шелти" => 165,
    "ши-пу" => 167,
    "ши-тцу" => 166,
    "шпиц вольф" => 170,
    "шпиц карликов. померанец (до23см)" => 168,
    "шпиц малый (до 29см)" => 169,
    "эрдельтерьер" => 171,
    "эстонская гончая" => 172,
    "южнорусская овчарка" => 173,
    "ягд терьер" => 174,
];

$arNames = [
    'Акита Ину'                          => ["time" => '', "message" => '', "trimtime" => ''],
    'Аляскинский маламут'                => ["time" => '', "message" => '', "trimtime" => ''],
    'Американская Акита '                => ["time" => '', "message" => '', "trimtime" => ''],
    'Афганская борзая'                   => ["time" => '', "message" => '', "trimtime" => ''],
    'Алабай'                             => ["time" => '', "message" => '', "trimtime" => ''],
    'Бассенджи'                          => ["time" => '', "message" => '', "trimtime" => ''],
    'Бассет-хаунд'                       => ["time" => '', "message" => '', "trimtime" => ''],
    'Бедлингтон терьер'                  => ["time" => '', "message" => '', "trimtime" => ''],
    'Бельгийская овчарка'                => ["time" => '', "message" => '', "trimtime" => ''],
    'Бернский зенненхунд'                => ["time" => '', "message" => '', "trimtime" => ''],
    'Бивер йоркширский терьер'           => ["time" => '', "message" => '', "trimtime" => ''],
    'Бигль'                              => ["time" => '', "message" => '', "trimtime" => ''],
    'Бишон'                              => ["time" => '', "message" => '', "trimtime" => ''],
    'Бобтейл'                            => ["time" => '', "message" => '', "trimtime" => ''],
    'Боксер'                             => ["time" => '', "message" => '', "trimtime" => ''],
    'Болонез'                            => ["time" => '', "message" => '', "trimtime" => ''],
    'Бордер колли'                       => ["time" => '', "message" => '', "trimtime" => ''],
    'Бордер терьер'                      => ["time" => '', "message" => '', "trimtime" => ''],
    'Брюсельский грифон'                 => ["time" => '', "message" => '', "trimtime" => ''],
    'Бульдог американский'               => ["time" => '', "message" => '', "trimtime" => ''],
    'Бульдог английский'                 => ["time" => '', "message" => '', "trimtime" => ''],
    'Бультерьер'                         => ["time" => '', "message" => '', "trimtime" => ''],
    'Буль Мастиф'                        => ["time" => '', "message" => '', "trimtime" => ''],
    'Вельш терьер'                       => ["time" => '', "message" => '', "trimtime" => ''],
    'Вельш-корги'                        => ["time" => '', "message" => '', "trimtime" => ''],
    'Вест-Хайленд-Вайт терьер'           => ["time" => '', "message" => '', "trimtime" => ''],
    'Восточно-европейская овчарка'       => ["time" => '', "message" => '', "trimtime" => ''],
    'Далматин'                           => ["time" => '', "message" => '', "trimtime" => ''],
    'Джек-рассел терьер (Парсен-Рассел)' => ["time" => '', "message" => '', "trimtime" => ''],
    'Джек-рассел терьер жесткошер.'      => ["time" => '', "message" => '', "trimtime" => ''],
    'Доберман'                           => ["time" => '', "message" => '', "trimtime" => ''],
    'Дог'                                => ["time" => '', "message" => '', "trimtime" => ''],
    'Дратхаар (тримминг)'                => ["time" => '', "message" => '', "trimtime" => ''],
    'Ирландский волкодав'                => ["time" => '', "message" => '', "trimtime" => ''],
    'Ирландский терьер (тримминг)'       => ["time" => '', "message" => '', "trimtime" => ''],
    'Испанский мастиф'                   => ["time" => '', "message" => '', "trimtime" => ''],
    'Йоркипу'                            => ["time" => '', "message" => '', "trimtime" => ''],
    'Йоркширский терьер'                 => ["time" => '', "message" => '', "trimtime" => ''],
    'Йоркширский терьер (от 6 кг)'       => ["time" => '', "message" => '', "trimtime" => ''],
    'Кавказская овчарка'                 => ["time" => '', "message" => '', "trimtime" => ''],
    'Керри-блю терьер'                   => ["time" => '', "message" => '', "trimtime" => ''],
    'Кеесхонд'                           => ["time" => '', "message" => '', "trimtime" => ''],
    'Китайская хохлатая голая'           => ["time" => '', "message" => '', "trimtime" => ''],
    'Китайская хохлатая пуховка'         => ["time" => '', "message" => '', "trimtime" => ''],
    'Коккер спаниель американский'       => ["time" => '', "message" => '', "trimtime" => ''],
    'Коккер спаниель английский'         => ["time" => '', "message" => '', "trimtime" => ''],
    'Колли гладкошерстная'               => ["time" => '', "message" => '', "trimtime" => ''],
    'Колли длинношерстная'               => ["time" => '', "message" => '', "trimtime" => ''],
    'Курцхаар'                           => ["time" => '', "message" => '', "trimtime" => ''],
    'Лабрадор'                           => ["time" => '', "message" => '', "trimtime" => ''],
    'Лабрадудель'                        => ["time" => '', "message" => '', "trimtime" => ''],
    'Лайка (большая)от 15кг'             => ["time" => '', "message" => '', "trimtime" => ''],
    'Лайка (маленькая) до 15кг'          => ["time" => '', "message" => '', "trimtime" => ''],
    'Левретка'                           => ["time" => '', "message" => '', "trimtime" => ''],
    'Леонбергер'                         => ["time" => '', "message" => '', "trimtime" => ''],
    'Лхасский апсо'                      => ["time" => '', "message" => '', "trimtime" => ''],
    'Мастино-неаполетано'                => ["time" => '', "message" => '', "trimtime" => ''],
    'Мастиф английский'                  => ["time" => '', "message" => '', "trimtime" => ''],
    'Мастиф испанский'                   => ["time" => '', "message" => '', "trimtime" => ''],
    'Мальтез'                            => ["time" => '', "message" => '', "trimtime" => ''],
    'Мальтипу'                           => ["time" => '', "message" => '', "trimtime" => ''],
    'Метис (более 20 кг)'                => ["time" => '', "message" => '', "trimtime" => ''],
    'Метис (до 20 кг)'                   => ["time" => '', "message" => '', "trimtime" => ''],
    'Метис (до 7 кг)'                    => ["time" => '', "message" => '', "trimtime" => ''],
    'Миттельшнауцер'                     => ["time" => '', "message" => '', "trimtime" => ''],
    'Мопс'                               => ["time" => '', "message" => '', "trimtime" => ''],
    'Моск.длинношерст. той-терьер'       => ["time" => '', "message" => '', "trimtime" => ''],
    'Московский дракон'                  => ["time" => '', "message" => '', "trimtime" => ''],
    'Ньюфаундленд'                       => ["time" => '', "message" => '', "trimtime" => ''],
    'Немецкая овчарка'                   => ["time" => '', "message" => '', "trimtime" => ''],
    'Норвич терьер'                      => ["time" => '', "message" => '', "trimtime" => ''],
    'Норфолк терьер'                     => ["time" => '', "message" => '', "trimtime" => ''],
    'Папильон'                           => ["time" => '', "message" => '', "trimtime" => ''],
    'Пекинес'                            => ["time" => '', "message" => '', "trimtime" => ''],
    'Пиренейская собака'                 => ["time" => '', "message" => '', "trimtime" => ''],
    'Пит Буль'                           => ["time" => '', "message" => '', "trimtime" => ''],
    'Петербургская орхидея'              => ["time" => '', "message" => '', "trimtime" => ''],
    'Пудель королевский'                 => ["time" => '', "message" => '', "trimtime" => ''],
    'Пудель малый (средний) от 36см'     => ["time" => '', "message" => '', "trimtime" => ''],
    'Пудель карликовый до 36 см '        => ["time" => '', "message" => '', "trimtime" => ''],
    'Пудель той до 28 см'                => ["time" => '', "message" => '', "trimtime" => ''],
    'Пшеничный терьер'                   => ["time" => '', "message" => '', "trimtime" => ''],
    'Ретривер (Голден)'                  => ["time" => '', "message" => '', "trimtime" => ''],
    'Риджбек'                            => ["time" => '', "message" => '', "trimtime" => ''],
    'Ризеншнауцер'                       => ["time" => '', "message" => '', "trimtime" => ''],
    'Ротвейлер'                          => ["time" => '', "message" => '', "trimtime" => ''],
    'Русская борзая'                     => ["time" => '', "message" => '', "trimtime" => ''],
    'Русская цветная болонка'            => ["time" => '', "message" => '', "trimtime" => ''],
    'Русский спаниель'                   => ["time" => '', "message" => '', "trimtime" => ''],
    'Самоед'                             => ["time" => '', "message" => '', "trimtime" => ''],
    'Сенбернар'                          => ["time" => '', "message" => '', "trimtime" => ''],
    'Сеттер'                             => ["time" => '', "message" => '', "trimtime" => ''],
    'Сибирский хаски'                    => ["time" => '', "message" => '', "trimtime" => ''],
    'Сиба Ину (Шиба Ину)'                => ["time" => '', "message" => '', "trimtime" => ''],
    'Скай терьер '                       => ["time" => '', "message" => '', "trimtime" => ''],
    'Спаниель Кавалер Кинг Чарльз'       => ["time" => '', "message" => '', "trimtime" => ''],
    'Скотч терьер'                       => ["time" => '', "message" => '', "trimtime" => ''],
    'Среднеазиатская овчарка'            => ["time" => '', "message" => '', "trimtime" => ''],
    'Стаффордшир терьер'                 => ["time" => '', "message" => '', "trimtime" => ''],
    'Такса гладкошерстная стандарт'      => ["time" => '', "message" => '', "trimtime" => ''],
    'Такса длинношерстная стандарт'      => ["time" => '', "message" => '', "trimtime" => ''],
    'Такса жесткошерстная стандарт'      => ["time" => '', "message" => '', "trimtime" => ''],
    'Такса жесткошерстная карликовая'    => ["time" => '', "message" => '', "trimtime" => ''],
    'Такса кроличья гладкошерстная'      => ["time" => '', "message" => '', "trimtime" => ''],
    'Такса кроличья длинношерстная'      => ["time" => '', "message" => '', "trimtime" => ''],
    'Тибетский мастиф'                   => ["time" => '', "message" => '', "trimtime" => ''],
    'Тибетский терьер (метис)'           => ["time" => '', "message" => '', "trimtime" => ''],
    'Той-терьер гладкошерстный'          => ["time" => '', "message" => '', "trimtime" => ''],
    'Той-терьер длинношерстный'          => ["time" => '', "message" => '', "trimtime" => ''],
    'Фокс терьер гладкошерстный'         => ["time" => '', "message" => '', "trimtime" => ''],
    'Фокс терьер жесткошерстный'         => ["time" => '', "message" => '', "trimtime" => ''],
    'Французский бульдог'                => ["time" => '', "message" => '', "trimtime" => ''],
    'Хин Японский'                       => ["time" => '', "message" => '', "trimtime" => ''],
    'Цвергшнауцер'                       => ["time" => '', "message" => '', "trimtime" => ''],
    'Цверг пинчер'                       => ["time" => '', "message" => '', "trimtime" => ''],
    'Чау-Чау'                            => ["time" => '', "message" => '', "trimtime" => ''],
    'Черный терьер'                      => ["time" => '', "message" => '', "trimtime" => ''],
    'Чи-хуа-хуа гладкошерстная'          => ["time" => '', "message" => '', "trimtime" => ''],
    'Чи-хуа-хуа длинношерстная'          => ["time" => '', "message" => '', "trimtime" => ''],
    'Шарпей'                             => ["time" => '', "message" => '', "trimtime" => ''],
    'Шелти'                              => ["time" => '', "message" => '', "trimtime" => ''],
    'Ши-тцу'                             => ["time" => '', "message" => '', "trimtime" => ''],
    'Ши-пу'                              => ["time" => '', "message" => '', "trimtime" => ''],
    'Шпиц карликов. померанец (до23см)'  => ["time" => '', "message" => '', "trimtime" => ''],
    'Шпиц малый (до 29см)'               => ["time" => '', "message" => '', "trimtime" => ''],
    'Шпиц Вольф '                        => ["time" => '', "message" => '', "trimtime" => ''],
    'Эрдельтерьер'                       => ["time" => '', "message" => '', "trimtime" => ''],
    'Эстонская гончая '                  => ["time" => '', "message" => '', "trimtime" => ''],
    'Южнорусская овчарка'                => ["time" => '', "message" => '', "trimtime" => ''],
    'Ягд терьер '                        => ["time" => '', "message" => '', "trimtime" => ''],
];

$arr = [
    'Акита Ину'                          => ["time" => '2:30', "message" => '', "trimtime" => ''],
    'Аляскинский маламут'                => ["time" => '3:30', "message" => '', "trimtime" => ''],
    'Американская Акита '                => ["time" => '3:00', "message" => '', "trimtime" => ''],
    'Бассенджи'                          => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Бассет-хаунд'                       => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Бельгийская овчарка'                => ["time" => '3:00', "message" => '', "trimtime" => ''],
    'Бернский зенненхунд'                => ["time" => '5:00', "message" => '4-5:00', "trimtime" => ''],
    'Бивер йоркширский терьер'           => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Бигль'                              => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Бишон'                              => ["time" => '4:00', "message" => '3-4:00 только пет', "trimtime" => ''],
    'Боксер'                             => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Болонез'                            => ["time" => '3:00', "message" => '', "trimtime" => ''],
    'Бордер колли'                       => ["time" => '3:00', "message" => '', "trimtime" => ''],
    'Брюсельский грифон'                 => ["time" => '2:00', "message" => '', "trimtime" => false],
    'Бульдог американский'               => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Бульдог английский'                 => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Бультерьер'                         => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Вельш терьер'                       => ["time" => '3:00', "message" => '', "trimtime" => false],
    'Вельш-корги'                        => ["time" => '3:00', "message" => '2:00 гладкий 3:00 длинный ', "trimtime" => ''],
    'Вест-Хайленд-Вайт терьер'           => ["time" => '2:30', "message" => '', "trimtime" => false],
    'Восточно-европейская овчарка'       => ["time" => '3:00', "message" => '', "trimtime" => ''],
    'Далматин'                           => ["time" => '1:30', "message" => '', "trimtime" => ''],
    'Джек-рассел терьер (Парсен-Рассел)' => ["time" => '1:30', "message" => '', "trimtime" => ''],
    'Джек-рассел терьер жесткошер.'      => ["time" => '2:30', "message" => '', "trimtime" => ''],
    'Доберман'                           => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Дог'                                => ["time" => '2:30', "message" => '', "trimtime" => ''],
    'Испанский мастиф'                   => ["time" => '5:00', "message" => '', "trimtime" => ''],
    'Йоркипу'                            => ["time" => '2:30', "message" => '', "trimtime" => ''],
    'Йоркширский терьер'                 => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Йоркширский терьер (от 6 кг)'       => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Кеесхонд'                           => ["time" => '5:00', "message" => '4-5:00', "trimtime" => ''],
    'Китайская хохлатая голая'           => ["time" => '1:30', "message" => '', "trimtime" => ''],
    'Китайская хохлатая пуховка'         => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Коккер спаниель американский'       => ["time" => '3:00', "message" => '', "trimtime" => false],
    'Коккер спаниель английский'         => ["time" => '3:00', "message" => '', "trimtime" => false],
    'Колли гладкошерстная'               => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Колли длинношерстная'               => ["time" => '3:00', "message" => '', "trimtime" => ''],
    'Курцхаар'                           => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Лабрадор'                           => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Лабрадудель'                        => ["time" => '5:00', "message" => '', "trimtime" => ''],
    'Лайка (большая)от 15кг'             => ["time" => '3:00', "message" => '', "trimtime" => ''],
    'Лайка (маленькая) до 15кг'          => ["time" => '2:30', "message" => '', "trimtime" => ''],
    'Левретка'                           => ["time" => '1:00', "message" => '', "trimtime" => ''],
    'Леонбергер'                         => ["time" => '6:00', "message" => '5-6ч', "trimtime" => ''],
    'Лхасский апсо'                      => ["time" => '3:00', "message" => '', "trimtime" => ''],
    'Мастино-неаполетано'                => ["time" => '3:00', "message" => '', "trimtime" => ''],
    'Мастиф английский'                  => ["time" => '3:00', "message" => '', "trimtime" => ''],
    'Мастиф испанский'                   => ["time" => '4:00', "message" => '3-4:00', "trimtime" => ''],
    'Мальтез'                            => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Мальтипу'                           => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Метис (более 20 кг)'                => ["time" => '3:30', "message" => '', "trimtime" => ''],
    'Метис (до 20 кг)'                   => ["time" => '3:00', "message" => '2:30-3:00', "trimtime" => ''],
    'Метис (до 7 кг)'                    => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Миттельшнауцер'                     => ["time" => '3:30', "message" => '', "trimtime" => ''],
    'Мопс'                               => ["time" => '1:30', "message" => '', "trimtime" => ''],
    'Моск.длинношерст. той-терьер'       => ["time" => '1:30', "message" => '', "trimtime" => ''],
    'Московский дракон'                  => ["time" => '1:30', "message" => '', "trimtime" => ''],
    'Немецкая овчарка'                   => ["time" => '4:00', "message" => '3-4:00', "trimtime" => ''],
    'Папильон'                           => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Пекинес'                            => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Петербургская орхидея'              => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Пудель малый (средний) от 36см'     => ["time" => '4:00', "message" => 'не выставочный', "trimtime" => ''],
    'Пудель карликовый до 36 см '        => ["time" => '3:00', "message" => 'не выставочный', "trimtime" => ''],
    'Пудель той до 28 см'                => ["time" => '2:30', "message" => 'не выставочный', "trimtime" => ''],
    'Пшеничный терьер'                   => ["time" => '4:00', "message" => '', "trimtime" => ''],
    'Ретривер (Голден)'                  => ["time" => '3:30', "message" => '3-3:30', "trimtime" => ''],
    'Риджбек'                            => ["time" => '1:30', "message" => '', "trimtime" => ''],
    'Ризеншнауцер'                       => ["time" => '4:00', "message" => '', "trimtime" => false],
    'Русская борзая'                     => ["time" => '4:00', "message" => '3-4:00', "trimtime" => ''],
    'Русская цветная болонка'            => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Русский спаниель'                   => ["time" => '2:30', "message" => '', "trimtime" => ''],
    'Самоед'                             => ["time" => '4:00', "message" => '3-4:00', "trimtime" => ''],
    'Сенбернар'                          => ["time" => '5:00', "message" => '', "trimtime" => ''],
    'Сеттер'                             => ["time" => '3:00', "message" => '', "trimtime" => ''],
    'Сибирский хаски'                    => ["time" => '2:30', "message" => '(хаски Акелла 3:00)', "trimtime" => ''],
    'Сиба Ину (Шиба Ину)'                => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Спаниель Кавалер Кинг Чарльз'       => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Такса гладкошерстная стандарт'      => ["time" => '1:00', "message" => '', "trimtime" => ''],
    'Такса длинношерстная стандарт'      => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Такса кроличья гладкошерстная'      => ["time" => '1:00', "message" => '', "trimtime" => ''],
    'Такса кроличья длинношерстная'      => ["time" => '1:30', "message" => '', "trimtime" => ''],
    'Тибетский терьер (метис)'           => ["time" => '4:00', "message" => '3-4:00', "trimtime" => ''],
    'Той-терьер гладкошерстный'          => ["time" => '1:00', "message" => '', "trimtime" => ''],
    'Той-терьер длинношерстный'          => ["time" => '1:30', "message" => '', "trimtime" => ''],
    'Фокс терьер гладкошерстный'         => ["time" => '1:30', "message" => '', "trimtime" => ''],
    'Французский бульдог'                => ["time" => '1:30', "message" => '', "trimtime" => ''],
    'Хин Японский'                       => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Цвергшнауцер'                       => ["time" => '3:00', "message" => '', "trimtime" => false],
    'Цверг пинчер'                       => ["time" => '1:00', "message" => '', "trimtime" => ''],
    'Чи-хуа-хуа гладкошерстная'          => ["time" => '1:00', "message" => '', "trimtime" => ''],
    'Чи-хуа-хуа длинношерстная'          => ["time" => '1:30', "message" => '', "trimtime" => ''],
    'Шелти'                              => ["time" => '2:30', "message" => '', "trimtime" => ''],
    'Ши-тцу'                             => ["time" => '3:30', "message" => '3-3.30 ч', "trimtime" => ''],
    'Ши-пу'                              => ["time" => '3:30', "message" => '3-3:30', "trimtime" => ''],
    'Шпиц карликов. померанец (до23см)'  => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Шпиц малый (до 29см)'               => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Шпиц Вольф '                        => ["time" => '4:00', "message" => '', "trimtime" => ''],
    'Эстонская гончая '                  => ["time" => '2:00', "message" => '', "trimtime" => ''],
    'Ягд терьер '                        => ["time" => '2:30', "message" => '', "trimtime" => false],
];
$MasterSkills = new SmartProcess\MasterSkills();
foreach ($arr as $name => $data) {
    $name = mb_strtolower(trim($name));
    $breedId = $arBreeds[$name];
    $arServicesId = $arProducts[$breedId]["otherProds"];
    if ($arMaster["is_trimm"] && $data["trimtime"] === '' && $arProducts[$breedId]["trimid"]) {
        $arServicesId[] = $arProducts[$breedId]["trimid"];
    }
    if (!empty($arServicesId)) {
        $arData = [
            $MasterSkills->getUfMasterCode() => $arMaster["id"],
            $MasterSkills->getUfServiceCode() => $arServicesId,
            $MasterSkills->getUfDurationCode() => $data["time"],
            $MasterSkills->getUfCommentCode() => $data["message"],
        ];
//        $MasterSkills->add($arData);
    }
    if ($arMaster["is_trimm"] && $data["trimtime"]) {
        $trimServId = $arProducts[$breedId]["trimid"];
        if ($trimServId) {
            $arDataTrim = [
                $MasterSkills->getUfMasterCode() => $arMaster["id"],
                $MasterSkills->getUfServiceCode() => [$trimServId],
                $MasterSkills->getUfDurationCode() => $data["trimtime"],
                $MasterSkills->getUfCommentCode() => $data["trimmessage"],
            ];
//            $MasterSkills->add($arDataTrim);
        }
    }
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>