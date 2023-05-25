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

$sveta = 14;
$natasha = 13;
$olya = 7;
$nastya = 10;
$nastyaCH = 18;
$ksu = 4;
$anya = 9;
$alina = 17;
$natk = 6;
$polina = 8;
$rosana = 5;
$liza = 20;
$katya = 19;
$lena = 3;
$vika = 15;
$alinaMaster = 16;
$maria = 21;
$natashaSmirnova = 27;
$natalia = 28;

$pobedaId = 33;
$galactId = 34;
$solnId = 35;
$karlId = 36;
$zoiId = 37;

$pobeda = [
    '01.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
    ],
    '02.06.2022' =>	[
        [
            "master" => $natasha,
            "assistant" => $olya
        ],
        [
            "master" => $nastya,
            "assistant" => ''
        ],
    ],
    '03.06.2022' =>	[
        [
            "master" => $natasha,
            "assistant" => $natashaSmirnova
        ],
        [
            "master" => $nastya,
            "assistant" => ''
        ],
    ],
    '04.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
        [
            "master" => $natasha,
            "assistant" => $olya
        ],
    ],
    '05.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
        [
            "master" => $nastyaCH,
            "assistant" => ''
        ],
    ],
    '06.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
    ],
    '07.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
    ],
    '08.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
    ],
    '09.06.2022' =>	[
        [
            "master" => $natasha,
            "assistant" => $olya
        ],
        [
            "master" => $nastya,
            "assistant" => ''
        ],
    ],
    '10.06.2022' =>	[
        [
            "master" => $natasha,
            "assistant" => $natashaSmirnova
        ],
        [
            "master" => $nastya,
            "assistant" => ''
        ],
    ],
    '11.06.2022' =>	[
        [
            "master" => $natasha,
            "assistant" => $olya
        ],
    ],
    '12.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
        [
            "master" => $nastyaCH,
            "assistant" => ''
        ],
    ],
    '13.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
    ],
    '14.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
    ],
    '15.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
    ],
    '16.06.2022' =>	[
        [
            "master" => $natasha,
            "assistant" => $olya
        ],
        [
            "master" => $nastya,
            "assistant" => ''
        ],
    ],
    '17.06.2022' =>	[
        [
            "master" => $natasha,
            "assistant" => $natashaSmirnova
        ],
        [
            "master" => $nastya,
            "assistant" => ''
        ],
    ],
    '18.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
        [
            "master" => $natasha,
            "assistant" => $olya
        ],
    ],
    '19.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
        [
            "master" => $nastyaCH,
            "assistant" => ''
        ],
    ],
    '20.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
    ],
    '21.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
    ],
    '22.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
    ],
    '23.06.2022' =>	[
        [
            "master" => $natasha,
            "assistant" => $olya
        ],
        [
            "master" => $nastya,
            "assistant" => ''
        ],
    ],
    '24.06.2022' =>	[
        [
            "master" => $natasha,
            "assistant" => $natashaSmirnova
        ],
        [
            "master" => $nastya,
            "assistant" => ''
        ],
    ],
    '25.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
        [
            "master" => $natasha,
            "assistant" => $olya
        ],
    ],
    '26.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
        [
            "master" => $nastyaCH,
            "assistant" => ''
        ],
    ],
    '27.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
    ],
    '28.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
    ],
    '29.06.2022' =>	[
        [
            "master" => $sveta,
            "assistant" => ''
        ],
    ],
    '30.06.2022' =>	[
        [
            "master" => $natasha,
            "assistant" => $olya
        ],
        [
            "master" => $nastya,
            "assistant" => ''
        ],
    ],
];

$galact = [
    '01.06.2022' =>	[
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
        [
            "master" => $natk,
            "assistant" => ''
        ],
    ],
    '02.06.2022' =>	[
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $natk,
            "assistant" => $alina
        ]
    ],
    '03.06.2022' =>	[
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
        [
            "master" => $natk,
            "assistant" => $natalia
        ],
    ],
    '04.06.2022' =>	[
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $liza,
            "assistant" => ''
        ]
    ],
    '05.06.2022' =>	[
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $liza,
            "assistant" => ''
        ]
    ],
    '06.06.2022' =>	[
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
        [
            "master" => $natk,
            "assistant" => $alina
        ],
    ],
    '07.06.2022' =>	[
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
        [
            "master" => $natk,
            "assistant" => $alina
        ],
    ],
    '08.06.2022' =>	[
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $natk,
            "assistant" => ''
        ]
    ],
    '09.06.2022' =>	[
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $natk,
            "assistant" => $alina
        ]
    ],
    '10.06.2022' =>	[
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $natk,
            "assistant" => $natalia
        ]
    ],
    '11.06.2022' =>	[
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $liza,
            "assistant" => ''
        ]
    ],
    '12.06.2022' =>	[
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $liza,
            "assistant" => ''
        ]
    ],
    '13.06.2022' =>	[
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
        [
            "master" => $natk,
            "assistant" => $alina
        ],
    ],
    '14.06.2022' =>	[
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
        [
            "master" => $natk,
            "assistant" => $alina
        ],
    ],
    '15.06.2022' =>	[
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
        [
            "master" => $natk,
            "assistant" => ''
        ],
    ],
    '16.06.2022' =>	[
        [
            "master" => $natk,
            "assistant" => $alina
        ],
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
    ],
    '17.06.2022' =>	[
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $natk,
            "assistant" => $natalia
        ]
    ],
    '18.06.2022' =>	[
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $liza,
            "assistant" => ''
        ]
    ],
    '19.06.2022' =>	[
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $liza,
            "assistant" => ''
        ]
    ],
    '20.06.2022' =>	[
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
        [
            "master" => $natk,
            "assistant" => $alina
        ],
    ],
    '21.06.2022' =>	[
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
        [
            "master" => $natk,
            "assistant" => $alina
        ],
    ],
    '22.06.2022' =>	[
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
        [
            "master" => $natk,
            "assistant" => ''
        ],
    ],
    '23.06.2022' =>	[
        [
            "master" => $natk,
            "assistant" => $alina
        ],
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
    ],
    '24.06.2022' =>	[
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $natk,
            "assistant" => $natalia
        ]
    ],
    '25.06.2022' =>	[
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $liza,
            "assistant" => ''
        ]
    ],
    '26.06.2022' =>	[
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $liza,
            "assistant" => ''
        ]
    ],
    '27.06.2022' =>	[
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
        [
            "master" => $natk,
            "assistant" => $alina
        ],
    ],
    '28.06.2022' =>	[
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
        [
            "master" => $natk,
            "assistant" => $alina
        ],
    ],
    '29.06.2022' =>	[
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
        [
            "master" => $natk,
            "assistant" => ''
        ],
    ],
    '30.06.2022' =>	[
        [
            "master" => $natk,
            "assistant" => $alina
        ],
        [
            "master" => $rosana,
            "assistant" => $polina
        ],
        [
            "master" => $ksu,
            "assistant" => $anya
        ],
    ],
//    '31.06.2022' =>	[
//        [
//            "master" => $ksu,
//            "assistant" => $anya
//        ],
//        [
//            "master" => $natk,
//            "assistant" => $alina
//        ]
//    ],
];

$tashk = [
    '01.06.2022' =>	[
        [
            "master" => $katya,
            "assistant" => ''
        ]
    ],
    '02.06.2022' =>	[
        [
            "master" => $katya,
            "assistant" => ''
        ]
    ],
    '03.06.2022' =>	[
        [
            "master" => $katya,
            "assistant" => ''
        ]
    ],
    '04.06.2022' =>	[
        [
            "master" => $katya,
            "assistant" => ''
        ]
    ],
    '05.06.2022' =>	[
        [
            "master" => $lena,
            "assistant" => $maria
        ]
    ],
    '06.06.2022' =>	[
        [
            "master" => $lena,
            "assistant" => $maria
        ]
    ],
//    '07.06.2022' =>	[
//        [
//            "master" => $katya,
//            "assistant" => ''
//        ]
//    ],
    '08.06.2022' =>	[
        [
            "master" => $katya,
            "assistant" => ''
        ]
    ],
    '09.06.2022' =>	[
        [
            "master" => $katya,
            "assistant" => ''
        ]
    ],
    '10.06.2022' =>	[
        [
            "master" => $katya,
            "assistant" => ''
        ]
    ],
    '11.06.2022' =>	[
        [
            "master" => $katya,
            "assistant" => ''
        ]
    ],
    '12.06.2022' =>	[
        [
            "master" => $lena,
            "assistant" => $maria
        ]
    ],
    '13.06.2022' =>	[
        [
            "master" => $lena,
            "assistant" => $maria
        ]
    ],
//    '14.06.2022' =>	[
//        [
//            "master" => $katya,
//            "assistant" => '',
//        ]
//    ],
    '15.06.2022' =>	[
        [
            "master" => $katya,
            "assistant" => ''
        ]
    ],
    '16.06.2022' =>	[
        [
            "master" => $katya,
            "assistant" => ''
        ]
    ],
    '17.06.2022' =>	[
        [
            "master" => $katya,
            "assistant" => ''
        ]
    ],
    '18.06.2022' =>	[
        [
            "master" => $katya,
            "assistant" => ''
        ]
    ],
    '19.06.2022' =>	[
        [
            "master" => $lena,
            "assistant" => $maria
        ]
    ],
    '20.06.2022' =>	[
        [
            "master" => $lena,
            "assistant" => $maria
        ]
    ],
//    '21.06.2022' =>	[
//        [
//            "master" => $katya,
//            "assistant" => ''
//        ]
//    ],
    '22.06.2022' =>	[
        [
            "master" => $katya,
            "assistant" => ''
        ]
    ],
    '23.06.2022' =>	[
        [
            "master" => $katya,
            "assistant" => ''
        ]
    ],
    '24.06.2022' =>	[
        [
            "master" => $katya,
            "assistant" => ''
        ]
    ],
    '25.06.2022' =>	[
        [
            "master" => $katya,
            "assistant" => ''
        ]
    ],
    '26.06.2022' =>	[
        [
            "master" => $lena,
            "assistant" => $maria
        ]
    ],
    '27.06.2022' =>	[
        [
            "master" => $lena,
            "assistant" => $maria
        ]
    ],
//    '28.06.2022' =>	[
//        [
//            "master" => $katya,
//            "assistant" => ''
//        ]
//    ],
    '29.06.2022' =>	[
        [
            "master" => $katya,
            "assistant" => ''
        ]
    ],
    '30.06.2022' =>	[
        [
            "master" => $katya,
            "assistant" => ''
        ]
    ],
];

$soln = [
    '01.06.2022' =>	[
        [
            "master" => $vika,
            "assistant" => ''
        ]
    ],
    '02.06.2022' =>	[
        [
            "master" => $vika,
            "assistant" => ''
        ]
    ],
    '03.06.2022' =>	[
        [
            "master" => $vika,
            "assistant" => ''
        ]
    ],
    '04.06.2022' =>	[
        [
            "master" => $vika,
            "assistant" => ''
        ]
    ],
    '05.06.2022' =>	[
        [
            "master" => $natasha,
            "assistant" => $olya
        ]
    ],
    '06.06.2022' =>	[
        [
            "master" => $natasha,
            "assistant" => $olya
        ]
    ],
    '07.06.2022' =>	[
        [
            "master" => $vika,
            "assistant" => ''
        ]
    ],
    '08.06.2022' =>	[
        [
            "master" => $vika,
            "assistant" => $natashaSmirnova
        ]
    ],
    '09.06.2022' =>	[
        [
            "master" => $vika,
            "assistant" => ''
        ]
    ],
    '10.06.2022' =>	[
        [
            "master" => $vika,
            "assistant" => ''
        ]
    ],
    '11.06.2022' =>	[
        [
            "master" => $vika,
            "assistant" => ''
        ]
    ],
    '12.06.2022' =>	[
        [
            "master" => $natasha,
            "assistant" => $olya
        ]
    ],
    '13.06.2022' =>	[
        [
            "master" => $natasha,
            "assistant" => $olya
        ]
    ],
    '14.06.2022' =>	[
        [
            "master" => $vika,
            "assistant" => ''
        ]
    ],
    '15.06.2022' =>	[
        [
            "master" => $vika,
            "assistant" => $natashaSmirnova
        ]
    ],
    '16.06.2022' =>	[
        [
            "master" => $vika,
            "assistant" => ''
        ]
    ],
    '17.06.2022' =>	[
        [
            "master" => $vika,
            "assistant" => ''
        ]
    ],
    '18.06.2022' =>	[
        [
            "master" => $vika,
            "assistant" => ''
        ]
    ],
    '19.06.2022' =>	[
        [
            "master" => $natasha,
            "assistant" => $olya
        ]
    ],
    '20.06.2022' =>	[
        [
            "master" => $natasha,
            "assistant" => $olya
        ]
    ],
//    '21.06.2022' =>	[
//        [
//            "master" => $vika,
//            "assistant" => ''
//        ]
//    ],
//    '22.06.2022' =>	[
//        [
//            "master" => $vika,
//            "assistant" => ''
//        ]
//    ],
//    '23.06.2022' =>	[
//        [
//            "master" => $vika,
//            "assistant" => $olya
//        ]
//    ],
    '24.06.2022' =>	[
        [
            "master" => $vika,
            "assistant" => ''
        ]
    ],
    '25.06.2022' =>	[
        [
            "master" => $vika,
            "assistant" => ''
        ]
    ],
    '26.06.2022' =>	[
        [
            "master" => $natasha,
            "assistant" => $olya
        ]
    ],
//    '27.06.2022' =>	[
//        [
//            "master" => $vika,
//            "assistant" => ''
//        ]
//    ],
    '28.06.2022' =>	[
        [
            "master" => $vika,
            "assistant" => ''
        ]
    ],
    '29.06.2022' =>	[
        [
            "master" => $vika,
            "assistant" => $natashaSmirnova
        ]
    ],
    '30.06.2022' =>	[
        [
            "master" => $vika,
            "assistant" => ''
        ]
    ],
//    '31.06.2022' =>	[
//        [
//            "master" => $vika,
//            "assistant" => ''
//        ]
//    ],
];

$zoi = [
    '01.06.2022' =>	[
        [
            "master" => $alinaMaster,
            "assistant" => ''
        ]
    ],
    '02.06.2022' =>	[
        [
            "master" => $lena,
            "assistant" => $maria
        ]
    ],
    '03.06.2022' =>	[
        [
            "master" => $lena,
            "assistant" => $maria
        ]
    ],
    '04.06.2022' =>	[
        [
            "master" => $alinaMaster,
            "assistant" => ''
        ]
    ],
    '05.06.2022' =>	[
        [
            "master" => $alinaMaster,
            "assistant" => ''
        ]
    ],
    '06.06.2022' =>	[
        [
            "master" => $nastya,
            "assistant" => ''
        ]
    ],
    '07.06.2022' =>	[
        [
            "master" => $alinaMaster,
            "assistant" => ''
        ]
    ],
    '08.06.2022' =>	[
        [
            "master" => $alinaMaster,
            "assistant" => ''
        ]
    ],
    '09.06.2022' =>	[
        [
            "master" => $lena,
            "assistant" => $maria
        ]
    ],
    '10.06.2022' =>	[
        [
            "master" => $lena,
            "assistant" => $maria
        ]
    ],
    '11.06.2022' =>	[
        [
            "master" => $alinaMaster,
            "assistant" => ''
        ]
    ],
    '12.06.2022' =>	[
        [
            "master" => $alinaMaster,
            "assistant" => ''
        ]
    ],
    '13.06.2022' =>	[
        [
            "master" => $nastya,
            "assistant" => ''
        ]
    ],
    '14.06.2022' =>	[
        [
            "master" => $alinaMaster,
            "assistant" => ''
        ]
    ],
    '15.06.2022' =>	[
        [
            "master" => $alinaMaster,
            "assistant" => ''
        ]
    ],
    '16.06.2022' =>	[
        [
            "master" => $lena,
            "assistant" => $maria
        ]
    ],
    '17.06.2022' =>	[
        [
            "master" => $lena,
            "assistant" => $maria
        ]
    ],
//    '18.06.2022' =>	[
//        [
//            "master" => '',
//            "assistant" => ''
//        ]
//    ],
//    '19.06.2022' =>	[
//        [
//            "master" => $alinaMaster,
//            "assistant" => ''
//        ]
//    ],
    '20.06.2022' =>	[
        [
            "master" => $nastya,
            "assistant" => ''
        ]
    ],
//    '21.06.2022' =>	[
//        [
//            "master" => $lena,
//            "assistant" => $maria
//        ]
//    ],
//    '22.06.2022' =>	[
//        [
//            "master" => $lena,
//            "assistant" => $maria
//        ]
//    ],
    '23.06.2022' =>	[
        [
            "master" => $lena,
            "assistant" => $maria
        ]
    ],
    '24.06.2022' =>	[
        [
            "master" => $lena,
            "assistant" => $maria
        ]
    ],
//    '25.06.2022' =>	[
//        [
//            "master" => '',
//            "assistant" => ''
//        ]
//    ],
//    '26.06.2022' =>	[
//        [
//            "master" => $alinaMaster,
//            "assistant" => ''
//        ]
//    ],
    '27.06.2022' =>	[
        [
            "master" => $nastya,
            "assistant" => ''
        ]
    ],
//    '28.06.2022' =>	[
//        [
//            "master" => $lena,
//            "assistant" => $maria
//        ]
//    ],
//    '29.06.2022' =>	[
//        [
//            "master" => $lena,
//            "assistant" => $maria
//        ]
//    ],
    '30.06.2022' =>	[
        [
            "master" => $lena,
            "assistant" => $maria
        ]
    ],
//    '31.06.2022' =>	[
//        [
//            "master" => $lena,
//            "assistant" => $maria
//        ]
//    ],
];

$WorkSchedule = new Starlabs\Project\WorkSchedule\Model\WorkScheduleTable();
foreach ($zoi as $date => $arData) {
    foreach ($arData as $arRecord) {
//        $obj = $WorkSchedule::add(["fields" => [
//            "MASTER_ID" => $arRecord["master"],
//            "SALON_ID" => $zoiId,
//            "ASSISTANT_ID" => $arRecord["assistant"],
//            "WORK_DATE" => new \Bitrix\Main\Type\Date($date, 'd.m.Y')
//        ]]);
    }
}



require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");?>