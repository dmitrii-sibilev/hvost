<?php
define("NOT_CHECK_PERMISSIONS", true);
define("NEED_AUTH", false);
use Bitrix\Crm\ContactTable;
use Bitrix\Crm\DealTable;
use Bitrix\Crm\FieldMultiTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;
use Starlabs\Project\Grooming;
use Starlabs\Project\Grooming\CashbackOperationsTable;
use Starlabs\Project\Iblock\Products;
use Starlabs\Project\SmartProcess;
use Starlabs\Project\Iblock;
use Starlabs\Project\Helpers\Utils;
use Starlabs\Project\Personal\Assistant;
use Starlabs\Project\Personal\Master;
use Starlabs\Project\SmartProcess\Pets;
use Starlabs\Project\WorkSchedule\Model\WorkScheduleTable;
use Starlabs\Project\WorkSchedule\ScheduleList;
use Starlabs\Tools\Helpers\Log;
use Starlabs\Project\Grooming\Deal;
use \Bitrix\Main\ORM\Query\Filter\ConditionTree;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
\Bitrix\Main\Loader::includeModule('crm');
try {
    /*//TODO: Логика по прихорашиванию кешбека
    $DealQuery = DealTable::query()
        ->setSelect(["ID", "OPPORTUNITY", "CONTACT_ID", Deal::FIELD_CASHBACK_CODE, Deal::FIELD_USE_CASHBACK_CODE, Deal::FIELD_AMOUNT_CODE, "STAGE_ID", Deal::FIELD_PET_CODE, "STAGE_SEMANTIC_ID"])
        ->setFilter([">" . Deal::FIELD_TIME_START_CODE => "05.05.2022", "<" . Deal::FIELD_TIME_START_CODE => "14.06.2022", "!STAGE_SEMANTIC_ID" => "F"])
        ->exec();
    $arPetCash = [];
    $PetsObject = new Pets();
    while ($arDeal = $DealQuery->fetchObject()) {
        /*if ($petId = $arDeal->get(Deal::FIELD_PET_CODE)) {
            $Pet = $PetsObject->getFactory()->getItem($petId);
        }*/

        /*$rowsSum = 0;
        foreach ($productRows as $row) {
            $rowsSum += $row["PRICE"];
        }
        $message = '';
        if ($rowsSum != $arDeal->get("OPPORTUNITY")) {
            $message = "Стоимость от администратора - " . $arDeal->get("OPPORTUNITY") . "руб. <br>";
        }* /
        $Cashback = new \Starlabs\Project\Grooming\Cashback($arDeal->get('ID'));
        /*if ($arDeal->get(Deal::FIELD_CASHBACK_CODE)) { TODO: скрипт по списанию кешбека
            $isAlreadyWriteOff = CashbackOperationsTable::query()
                ->setSelect(["ID", "DEAL_ID"])
                ->setFilter(["DEAL_ID" => $arDeal->getId(), "<VALUE" => 0])
                ->exec()->fetch();
            if (!$isAlreadyWriteOff) {
                $Cashback->Pet->set($PetsObject->getUfCashbackCode(), ($Cashback->Pet->get($PetsObject->getUfCashbackCode()) - $arDeal->get(Deal::FIELD_CASHBACK_CODE)))->save();
                $arOperation = [
                    "DEAL_ID" => $arDeal->getId(),
                    "CONTACT_ID" => $Cashback->contactId,
                    "PET_ID" => $Cashback->Pet->getId(),
                    "VALUE" => -$arDeal->get(Deal::FIELD_CASHBACK_CODE)
                ];

                CashbackOperationsTable::add($arOperation);
            } else {
                \Starlabs\Tools\Helpers\p::init([$arDeal["ID"], $isAlreadyWriteOff])->_die();
            }
        }* /

        if ($arDeal->getStageSemanticId() == "P") {
            $arDeal->setStageSemanticId("S");
        }
        if ($arDeal->getStageId() == Deal::getNewStatusId()) {
            $arDeal->setStageId(Deal::getWonStatusId());
        }

//        $arDeal->save();

        /*if (!$Cashback->Pet || !$Cashback->checkEarnCondition()) { TODO: скрипт начисления баллов
            continue;
        }
        $summ = $Cashback->getSaleAmount();
        $percent = $Cashback->calculateMaxPercentForCashback();
        if ($percent > 0 && $summ > 0) {
            $cashback = round($summ * $percent);
            $cashbackField = $PetsObject->getUfCashbackCode();
            $currentCashback = $Cashback->Pet->get($cashbackField);
            $Cashback->Pet->set($cashbackField, $currentCashback + $cashback)->save();
            /**
             * TODO: сделать операцию по событию
             * /
            $arOperation = [
                "DEAL_ID" => $arDeal->getId(),
                "CONTACT_ID" => $Cashback->contactId,
                "PET_ID" => $Cashback->Pet->getId(),
                "VALUE" => $cashback
            ];

            CashbackOperationsTable::add($arOperation);
        }*/


        /*$cashbackBalance = $Cashback->getAvailableCashback();
        $cashWriteOff = $Cashback->getAvailableCashback();

        if (count($productRows) > 1 && $arDeal->get(Deal::FIELD_USE_CASHBACK_CODE)) {
            $cashEarn = $Cashback->getSaleAmount() * $Cashback->calculateMaxPercentForCashback();
            \Starlabs\Tools\Helpers\p::init([$arDeal->getId(), $Cashback->getSaleAmount(), $Cashback->calculateMaxPercentForCashback(), $cashEarn])->_die();
        }*/

        /*$amount = $arDeal->get("OPPORTUNITY");
        $cash = 0;*/

        /*if ($arDeal->get("STAGE_ID") == Deal::getWonStatusId() && $arDeal->get(Deal::FIELD_USE_CASHBACK_CODE) > 0 && $arDeal->get(Deal::FIELD_CASHBACK_CODE) > 0 && $arDeal->get(Deal::FIELD_AMOUNT_CODE) > 0) {
            $message .= 'Сумма к оплате - ' . $arDeal->get(Deal::FIELD_AMOUNT_CODE) . "руб.<br>(1с учетом скидки по кешбэку - " . $arDeal->get(Deal::FIELD_CASHBACK_CODE) . 'руб.)';
            $arNothing[] = $arDeal->getId();
        } elseif ($arDeal->get(Deal::FIELD_USE_CASHBACK_CODE) > 0 && $cashbackBalance > 0) {
            $arNeed[] = $arDeal->getId();
            $amount = $arDeal->get("OPPORTUNITY") - $cashWriteOff;
            $cash = $cashWriteOff;
            /*$opportunity = $arDeal->get("OPPORTUNITY");

            $arDeal->set(Deal::FIELD_CASHBACK_CODE, $cashWriteOff);
            $arDeal->set(Deal::FIELD_AMOUNT_CODE, $opportunity - $cashWriteOff)->save();
            $message .= 'Сумма к оплате - ' . ($arDeal->get("OPPORTUNITY") - $cashbackBalance) . "руб.<br>(2с учетом скидки по кешбэку - " . $cashbackBalance . 'руб.)';
            $amount = $arDeal->get("OPPORTUNITY") - $cashbackBalance;
            $cash = $cashbackBalance;
            $cashbackPoints = $arPetCash[$arDeal->get(Deal::FIELD_PET_CODE)]["INIT"];
            if ($cashbackPoints) {
                $opportunity = $arDeal->get("OPPORTUNITY");

                $arDeal->set(Deal::FIELD_CASHBACK_CODE, $cashbackPoints);
                $arDeal->set(Deal::FIELD_AMOUNT_CODE, $opportunity - $cashbackPoints)->save();
                $Pet->set($PetsObject->getUfCashbackCode(), ($Pet->get($PetsObject->getUfCashbackCode()) - $cashbackPoints))->save();
                $arOperation = [
                    "DEAL_ID" => $arDeal["ID"],
                    "CONTACT_ID" => $arDeal["CONTACT_ID"],
                    "PET_ID" => $Pet->getId(),
                    "VALUE" => -$cashbackPoints
                ];

                CashbackOperationsTable::add($arOperation);
            }

            $cashback = round($Cashback->getSaleAmount() * $Cashback->calculateMaxPercentForCashback());
            if ($cashback) {
                $cashbackField = $PetsObject->getUfCashbackCode();
                $currentCashback = $Pet->get($cashbackField);
                $Pet->set($cashbackField, $currentCashback + $cashback)->save();
                /**
                 * TODO: сделать операцию по событию
                 * /
                $arOperation = [
                    "DEAL_ID" => $arDeal["ID"],
                    "CONTACT_ID" => $arDeal["CONTACT_ID"],
                    "PET_ID" => $Pet->getId(),
                    "VALUE" => $cashback
                ];

                CashbackOperationsTable::add($arOperation);
            }* /

        } else {
            $arWithout[] = $arDeal->getId();
            $message .= 'Сумма к оплате - ' . $arDeal->get("OPPORTUNITY") . 'руб.';
        }*/

        /*$arDeal->set(Deal::FIELD_AMOUNT_CODE, $amount);
        $arDeal->set(Deal::FIELD_CASHBACK_CODE, $cash)->save();* /

    }

    /*foreach ($arPetCash as $key => $arCash) {
        $points = $arCash["INIT"];
        foreach ($arCash["CHANGES"] as $sum) {
            $points += $sum;
        }
        $arPetCash[$key]["ITOG"] = $points;
    }*/

    $Query = \Bitrix\Tasks\Internals\Task\LogTable::query()
        ->setSelect(["USER_ID", "TASK_ID", "TO_VALUE"])
        ->setFilter(["USER_ID" => 22, "TO_VALUE" => 5, "FIELD" => "STATUS", ">CREATED_DATE" => "07.07.2022 11:45:26"])
        ->exec();

    while ($record = $Query->fetch()) {
        $arTasks[] = $record["TASK_ID"];
    }

    $tasksQuery = \Bitrix\Tasks\TaskTable::query()
        ->setSelect(["ID", "UF_CRM_TASK"])
        ->setFilter(["ID" => $arTasks])
        ->exec();

    $Deal = new CCrmDeal();
    $arFields = [
        "STAGE_ID" => Deal::getDeletedStatusId(),
        "STAGE_SEMANTIC_ID" => "F"
    ];
    while ($arTask = $tasksQuery->fetch()) {

        $dealId = explode('_', $arTask["UF_CRM_TASK"][0])[1];
        \Starlabs\Tools\Helpers\p::init($dealId);
//        $Deal->Update($dealId, $arFields);
    }


    die();


} catch (\Throwable $exception) {
    \Starlabs\Tools\Helpers\p::init($exception->getMessage());
    \Starlabs\Tools\Helpers\p::init($exception->getFile());
    \Starlabs\Tools\Helpers\p::init($exception->getLine());
}

function getAmount($dealId, $arDeal)
{
    $arProducts = \CCrmDeal::LoadProductRows($dealId);
    $haveBasic = false;
    $haveHaircut = false;

    if (empty($arProducts) && ($arDeal["OPPORTUNITY"] < 700)) {
        return false;
    }
    foreach ($arProducts as $arProduct) {
        if (in_array($arProduct["PRODUCT_ID"], Products::BASIC_PRODUCTS) || mb_strrpos($arProduct["PRODUCT_NAME"], "Мытье с сушкой")) {
            $haveBasic = true;
        } else {
            $haveHaircut = true;
            $hairCutProduct = $arProduct;
        }
    }

    $dealAmount = $arDeal[Deal::FIELD_AMOUNT_CODE];
    if (!$dealAmount) {
        $dealAmount = $arDeal["OPPORTUNITY"];
    }

    $dealCashback = $arDeal[Deal::FIELD_CASHBACK_CODE];
    if ($haveBasic) {
        if (count($arProducts) > 1 && $haveHaircut) {
            if ($dealAmount > $hairCutProduct["PRICE"]) {
                return $hairCutProduct["PRICE"];
            } else {
                return $dealAmount;
            }
        }
    } else {
        return $dealAmount;
    }
}


//\Starlabs\Tools\Helpers\p::init($arDeals);
//\Starlabs\Tools\Helpers\p::init($arFail);
?>


<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>