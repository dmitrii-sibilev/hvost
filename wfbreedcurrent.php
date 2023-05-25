<?php

use Bitrix\Crm\ContactTable;
use Bitrix\Crm\DealTable;
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

use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;
use \Bitrix\Im\Model\MessageTable;
use \Bitrix\Main\Type\DateTime;

\Bitrix\Main\Loader::includeModule('im');

try {
    $array = [
        "Акита ину"                          => "3:00",
        "Аляскинский маламут"                => "3:00",
        "Американская Акита"                 => "",
        "Афганская борзая"                   => "4:30",
        "Алабай"                             => "",
        "Бассенджи"                          => "2:30",
        "Бассет-хаунд"                       => "",
        "Бедлингтон терьер"                  => "4:00",
        "Бельгийская овчарка"                => "3:00",
        "Бернский зенненхунд"                => "4:30",
        "Бивер йоркширский терьер"           => "3:00",
        "Бигль"                              => "2:00",
        "Бишон"                              => "",
        "Бобтейл"                            => "",
        "Боксер"                             => "2:00",
        "Болонез"                            => "3:00",
        "Бордер колли"                       => "3:00",
        "Бордер терьер"                      => "",
        "Брюсельский грифон"                 => "3:00",
        "Бульдог американский"               => "2:00",
        "Бульдог английский"                 => "2:00",
        "Бультерьер"                         => "2:00",
        "Буль Мастиф"                        => "",
        "Вельш терьер"                       => "",
        "Вельш-корги"                        => "2:00",
        "Вест-Хайленд-Вайт терьер"           => "",
        "Восточно-европейская овчарка"       => "",
        "Далматин"                           => "2:00",
        "Джек-рассел терьер (Парсен-Рассел)" => "1:30",
        "Джек-рассел терьер жесткошер."      => "",
        "Доберман"                           => "2:00",
        "Дог"                                => "2:30",
        "Дратхаар"                           => "",
        "Ирландский волкодав"                => "",
        "Ирландский терьер"                  => "",
        "Мастиф испанский"                   => "",
        "Йоркипу"                            => "2:30",
        "Йоркширский терьер"                 => "3:00",
        "Йоркширский терьер (от 6 кг)"       => "3:00",
        "Кавказская овчарка"                 => "",
        "Керри-блю терьер"                   => "",
        "Кеесхонд"                           => "",
        "Китайская хохлатая голая"           => "2:00",
        "Китайская хохлатая пуховка"         => "2:00",
        "Коккер спаниель американский"       => "3:00",
        "Коккер спаниель английский"         => "3:00",
        "Колли гладкошерстная"               => "2:00",
        "Колли длинношерстная"               => "3:00",
        "Курцхаар"                           => "2:30",
        "Лабрадор"                           => "2:30",
        "Лабрадудель"                        => "",
        "Лайка (большая)от 15кг"             => "3:00",
        "Лайка (маленькая) до 15кг"          => "2:00",
        "Левретка"                           => "1:30",
        "Леонбергер"                         => "",
        "Лхасский апсо"                      => "3:00",
        "Мастино-неаполетано"                => "",
        "Мальтез"                            => "",
        "Мальтипу"                           => "",
        "Метис (более 20 кг)"                => "2:30",
        "Метис (до 20 кг)"                   => "3:30",
        "Метис (до 7 кг)"                    => "3:30",
        "Миттельшнауцер"                     => "3:00",
        "Мопс"                               => "2:00",
        "Моск.длинношерст. той-терьер"       => "",
        "Московский дракон"                  => "1:30",
        "Ньюфаундленд"                       => "1:30",
        "Немецкая овчарка"                   => "",
        "Норвич терьер"                      => "",
        "Норфолк терьер"                     => "",
        "Папильон"                           => "",
        "Пекинес"                            => "",
        "Пиренейская собака"                 => "2:00",
        "Пит Буль"                           => "2:00",
        "Петербургская орхидея"              => "",
        "Пудель королевский"                 => "2:00",
        "Пудель малый (средний) от 36см"     => "2:30",
        "Пудель карликовый до 36 см"         => "6:00",
        "Пудель той до 28 см"                => "5:00",
        "Пшеничный терьер"                   => "4:00",
        "Ретривер (Голден)"                  => "3:30",
        "Риджбек"                            => "4:40",
        "Ризеншнауцер"                       => "3:00",
        "Ротвейлер"                          => "",
        "Русская борзая"                     => "",
        "Русская цветная болонка"            => "",
        "Русский спаниель"                   => "3:30",
        "Самоед"                             => "2:30",
        "Сенбернар"                          => "3:00",
        "Сеттер"                             => "3:30",
        "Сибирский хаски"                    => "4:30",
        "Сиба Ину (Шиба Ину)"                => "3:00",
        "Скай терьер"                        => "3:00",
        "Спаниель Кавалер Кинг Чарльз"       => "2:00",
        "Скотч терьер"                       => "",
        "Среднеазиатская овчарка"            => "2:00",
        "Стаффордшир терьер"                 => "",
        "Такса гладкошерстная стандарт"      => "",
        "Такса длинношерстная стандарт"      => "",
        "Такса жесткошерстная стандарт"      => "1:30",
        "Такса жесткошерстная карликовая"    => "2:00",
        "Такса кроличья гладкошерстная"      => "",
        "Такса кроличья длинношерстная"      => "",
        "Тибетский мастиф"                   => "2:00",
        "Тибетский терьер (метис)"           => "2:00",
        "Той-терьер гладкошерстный"          => "",
        "Той-терьер длинношерстный"          => "",
        "Фокс терьер гладкошерстный"         => "1:00",
        "Фокс терьер жесткошерстный"         => "1:30",
        "Французский бульдог"                => "1:30",
        "Хин Японский"                       => "",
        "Цвергшнауцер"                       => "1:30",
        "Цверг пинчер"                       => "2:00",
        "Чау-Чау"                            => "",
        "Черный терьер"                      => "1:00",
        "Чи-хуа-хуа гладкошерстная"          => "",
        "Чи-хуа-хуа длинношерстная"          => "",
        "Шарпей"                             => "1:00",
        "Шелти"                              => "1:30",
        "Ши-тцу"                             => "2:00",
        "Ши-пу"                              => "2:00",
        "Шпиц карликов. померанец (до23см)"  => "3:00",
        "Шпиц малый  (до 29см)"              => "3:30",
        "Шпиц Вольф"                         => "2:00",
        "Эрдельтерьер"                       => "2:00",
        "Эстонская гончая"                   => "4:00",
        "Южнорусская овчарка"                => "",
        "Ягд терьер"                         => "1:30",
    ];

    $arNames = array_keys($array);
    $Products = new Iblock\Products();
    $res = $Products->createQuery()
        ->setSelect(["NAME", "ID", $Products::PROP_BREED_CODE])
        ->exec();
    $Breeds = new Iblock\Breed();
    $resBreed = $Breeds->createQuery()
        ->setSelect(["NAME", "ID"])
        ->exec();

    $arResBreed = [];

    while ($arBreed = $resBreed->fetch()) {
        foreach ($arNames as $key => $name) {
            if (strcasecmp($name, $arBreed["NAME"]) === 0) {
                $arResBreed[$arBreed["ID"]] = $name;
            }
        }
    }

    \Starlabs\Tools\Helpers\p::init($arResBreed);

    $arBreedId = array_keys($arResBreed);

    $arRes = [];

    while ($arProduct = $res->fetch()) {
        if (in_array(
                $arProduct["IBLOCK_ELEMENTS_ELEMENT_PRODUCTS_BREED_IBLOCK_GENERIC_VALUE"],
                $arBreedId
            ) && $array[$arResBreed[$arProduct["IBLOCK_ELEMENTS_ELEMENT_PRODUCTS_BREED_IBLOCK_GENERIC_VALUE"]]]) {
            $arResult[$arProduct["IBLOCK_ELEMENTS_ELEMENT_PRODUCTS_BREED_IBLOCK_GENERIC_VALUE"]]["TIME"] = $array[$arResBreed[$arProduct["IBLOCK_ELEMENTS_ELEMENT_PRODUCTS_BREED_IBLOCK_GENERIC_VALUE"]]];
            $arResult[$arProduct["IBLOCK_ELEMENTS_ELEMENT_PRODUCTS_BREED_IBLOCK_GENERIC_VALUE"]]["PRODUCT_ID"][] = $arProduct["ID"];
        }
    }

    $Skill = new SmartProcess\MasterSkills();
    foreach ($arResult as $breedId => $skillData) {
        $arFields = [
            "ASSIGNED_BY_ID"            => 1,
            $Skill->getUfMasterCode()   => 17,
            $Skill->getUfDurationCode() => $skillData["TIME"],
            $Skill->getUfServiceCode()  => $skillData["PRODUCT_ID"],
        ];
//        $Skill->add($arFields);
    }
} catch (\Throwable $e) {
    return ["error" => $e->getMessage()];
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>

