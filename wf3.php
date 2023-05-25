<?php

use Bitrix\Crm\ContactTable;
use Bitrix\Main\UserPhoneAuthTable;
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

//$Rest = new \Starlabs\Project\Crest\CRest();
//$listItems = [];
//$arBreeds = [];
//$listTotal = \Starlabs\Project\CRest\CRestCurrent::call(
//    'crm.deal.list',
//    [
//        "filter" => [
//            ">UF_CRM_1486396749" => "01.06.2021 00:00:00",
//        ]
//    ]
//)['total'];
//for ($i = 0; $i < $listTotal / 50; $i++) {
//    $params['start'] = $i * 50;
//    $params["select"] = [
//        "*",
//        "UF_*",
//        "PHONE",
//        "EMAIL"
//    ];
//    $params["filter"] = [">UF_CRM_1486396749" => "01.06.2021 00:00:00"];
//    $Rest->addBatchCall('crm.deal.list', $params,
//        function ($result) use (&$listItems) {
//            $listItems = array_merge($listItems, $result['result']);
//        }
//    );
//}
//$Rest->processBatchCalls();

//$Rest = new \Starlabs\Project\Crest\CRest();
//$listItems = [];
//$arBreeds = [];
//$listTotal = \Starlabs\Project\CRest\CRestCurrent::call('crm.deal.list', ["filter" => ["=CATEGORY_ID" => 0]])['total'];
//for ($i = 0; $i < $listTotal / 50; $i++) {
//    $params['start'] = $i * 50;
//    $params["select"] = ["*", "UF_*", "PHONE", "EMAIL"];
//    $params["select"] = ["=CATEGORY_ID" => 0];
//    $Rest->addBatchCall('crm.deal.list', $params,
//        function ($result) use (&$listItems) {
//            $listItems = array_merge($listItems, $result['result']);
//        }
//    );
//}
//$Rest->processBatchCalls();

//$arr = [
//    "name" => 'string',
//    "name2" => 'string',
//];
//\Starlabs\Tools\Helpers\p::init(serialize($listItems));
//print_r($arr);
//var_dump($arr);
$today = new \Bitrix\Main\Type\DateTime();
$dealRes = \Bitrix\Crm\DealTable::query()
    ->setSelect(["ID", Deal::FIELD_TIME_START_CODE, Deal::FIELD_TIME_FINISH_CODE])
    ->setFilter([">" . Deal::FIELD_TIME_START_CODE => $today])
    ->exec();

while ($arDeal = $dealRes->fetch()) {
    $arDealData[$arDeal["ID"]] = $arDeal;
}

$Tasks = CTasks::GetList([],[">DEADLINE" => $today, "!UF_CRM_TASK" => false],["ID", "UF_CRM_TASK", "DEADLINE", "START_DATE_PLAN", "END_DATE_PLAN"]);
while ($arTask = $Tasks->Fetch()) {
    $dealId = str_replace('D_', '', $arTask["UF_CRM_TASK"][0]);
    $arTasksData[$dealId] = $arTask;
}
$count = 0;
foreach ($arTasksData as $dealId => $tasksDatum) {
    $taskTimeStart = new \Bitrix\Main\Type\DateTime($tasksDatum["START_DATE_PLAN"], 'd.m.Y H:i:s');
    $taskTimeFinish = new \Bitrix\Main\Type\DateTime($tasksDatum["END_DATE_PLAN"], 'd.m.Y H:i:s');
    $taskTimeDead = new \Bitrix\Main\Type\DateTime($tasksDatum["DEADLINE"], 'd.m.Y H:i:s');
    if ($taskTimeStart != $arDealData[$dealId][Deal::FIELD_TIME_START_CODE] ||
        $taskTimeDead != $arDealData[$dealId][Deal::FIELD_TIME_START_CODE] ||
        $taskTimeFinish != $arDealData[$dealId][Deal::FIELD_TIME_FINISH_CODE]
    ) {
        $taskFields = [
            "DEADLINE" => $arDealData[$dealId][Deal::FIELD_TIME_START_CODE]->format('d.m.Y+H:i:s'),
            "START_DATE_PLAN" => $arDealData[$dealId][Deal::FIELD_TIME_START_CODE]->format('d.m.Y+H:i:s'),
            "END_DATE_PLAN" => $arDealData[$dealId][Deal::FIELD_TIME_FINISH_CODE]->format('d.m.Y+H:i:s'),
        ];
        $count++;
        \Starlabs\Tools\Helpers\p::init([$taskFields, $tasksDatum]);
        $taskItem = new \CTaskItem($tasksDatum["ID"], 1);
        $taskItem->update($taskFields);
    }


}

\Starlabs\Tools\Helpers\p::init($count);

//\Starlabs\Tools\Helpers\p::init(count($arTasksList));
//\Starlabs\Tools\Helpers\p::init(count($arResult));
//\Starlabs\Tools\Helpers\p::init($arSchedule);
//\Starlabs\Tools\Helpers\p::init($arTasksList);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");?>