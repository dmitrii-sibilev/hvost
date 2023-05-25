<?php
use Starlabs\Project\Grooming\Deal;
//if(isset($_POST['save']) && !empty($arParams['ELEMENT_ID'])) {
    //TODO: доделать описание задачи при редактировании суммы
//    $taskId = \Starlabs\Project\Grooming\Tasks::getGroomingTaskIdByDealId($arParams['ELEMENT_ID']);
//    $taskItem = new \CTaskItem($taskId, 1);
//    $taskDescription = $taskItem->getData()["DESCRIPTION"];
//    $newDescription = preg_replace('/(Сумма -) (\d+)/', '$1 123', $res);
//}
if ($arResult['MODE'] != 'EDIT') {
    $arDeal = \Bitrix\Crm\DealTable::query()
        ->setFilter(["ID" => $arResult['ELEMENT_ID']])
        ->setSelect(["STAGE_ID", Deal::FIELD_USE_CASHBACK_CODE, Deal::FIELD_AMOUNT_CODE, Deal::FIELD_CASHBACK_CODE, "OPPORTUNITY", "CONTACT_ID"])
        ->fetch();

    $productRows = CCrmDeal::LoadProductRows($arResult['ELEMENT_ID']);
    $rowsSum = 0;
    foreach ($productRows as $row) {
        $rowsSum += $row["PRICE"];
    }
    $message = '';
    if ($rowsSum != $arDeal["OPPORTUNITY"]) {
        $message = "Стоимость от администратора - " . $arDeal["OPPORTUNITY"] . "руб. <br>";
    }

    $Cashback = new \Starlabs\Project\Grooming\Cashback($arResult['ELEMENT_ID']);
    $cashbackBalance = $Cashback->getAvailableCashback();
    if ($arDeal["STAGE_ID"] == Deal::getWonStatusId() && $arDeal[Deal::FIELD_USE_CASHBACK_CODE] > 0 && $arDeal[Deal::FIELD_CASHBACK_CODE] > 0) {
        $message .= 'Сумма к оплате - ' . $arDeal[Deal::FIELD_AMOUNT_CODE] . "руб.<br>(с учетом скидки по кешбэку - " . $arDeal[Deal::FIELD_CASHBACK_CODE] . 'руб.)';
    } elseif ($arDeal[Deal::FIELD_USE_CASHBACK_CODE] > 0 && $cashbackBalance > 0) {
        $message .= 'Сумма к оплате - ' . ($arDeal["OPPORTUNITY"] - $cashbackBalance) . "руб.<br>(с учетом скидки по кешбэку - " . $cashbackBalance . 'руб.)';
    } else {
        $message .= 'Сумма к оплате - ' . $arDeal["OPPORTUNITY"] . 'руб.';
    }
    $arResult["CASHBACK_MESSAGE"] = $message;

    $arContact = \Bitrix\Crm\ContactTable::query()
        ->setSelect(["ID", "COMMENTS"])
        ->setFilter(["ID" => $arDeal["CONTACT_ID"]])
        ->exec()->fetch();
    if ($arContact["COMMENTS"]) {
        $arResult["CONTACT_COMMENTS"] = $arContact["COMMENTS"];
    }
}


