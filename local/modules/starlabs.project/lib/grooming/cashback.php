<?php

namespace Starlabs\Project\Grooming;

use Bitrix\Crm\ContactTable;
use Bitrix\Crm\DealTable;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\Type\DateTime;
use Starlabs\Project\Iblock\Breed;
use Starlabs\Project\Iblock\Products;
use Starlabs\Project\SmartProcess\Pets;
use Starlabs\Tools\Helpers\p;

class Cashback
{
    private $Contact;
    private $CurrentDeal;
    public $Pet;
    private $PetsObject;
    private $dealId;
    public $contactId;

    const SALE_ID = 1;
    const SALE_NAME = 'Кешбэк';
    const CASHBACK_DISCOUNT_PERCENT = 0.10;

    public function __construct($dealId, $dealFields = [])
    {
        $this->CurrentDeal = DealTable::query()
            ->setSelect(["ID", Deal::FIELD_PET_CODE, "CONTACT_ID", "OPPORTUNITY", Deal::FIELD_CASHBACK_CODE, Deal::FIELD_TIME_START_CODE, Deal::FIELD_AMOUNT_CODE, Deal::FIELD_USE_CASHBACK_CODE])
            ->setFilter(["ID" => $dealId])
            ->exec()->fetchObject();


        if ($petId = $this->CurrentDeal->get(Deal::FIELD_PET_CODE)) {
            $this->PetsObject = new Pets();
            $this->Pet = $this->PetsObject->getFactory()->getItem($petId);
        }

        //TODO: получить контакт и питомцев с помощью Связей
        $this->contactId = $this->CurrentDeal->get("CONTACT_ID");
        $this->dealId = $dealId;
    }

    /**
     * @return mixed
     */
    public function getContactId()
    {
        return $this->contactId;
    }

    /**
     * Зачисление кешбека на счет питомца по сделке
     * @return false|void
     * @throws \Exception
     */
    public function earnCashback()
    {
        if (!$this->Pet || !$this->checkEarnCondition()) {
            return false;
        }

        $summ = $this->getSaleAmount();
        $percent = $this->calculateMaxPercentForCashback();
        if ($percent > 0 && $summ > 0) {
            $cashback = round($summ * $percent);
            $cashbackField = $this->PetsObject->getUfCashbackCode();
            $currentCashback = $this->Pet->get($cashbackField);
            $this->Pet->set($cashbackField, $currentCashback + $cashback)->save();
            /**
             * TODO: сделать операцию по событию
             */
            $arOperation = [
                "DEAL_ID" => $this->dealId,
                "CONTACT_ID" => $this->contactId,
                "PET_ID" => $this->Pet->getId(),
                "VALUE" => $cashback
            ];

            CashbackOperationsTable::add($arOperation);
            $entryID = \Bitrix\Crm\Timeline\CommentEntry::create(
                array(
                    'TEXT' => 'По [URL=https://b24.abcgrooming.ru/crm/deal/details/' . $this->dealId . '/]сделке #' . $this->dealId . '[/URL] начислено ' . $cashback . ' кэшбек баллов.',
                    'SETTINGS' => ["HAS_FILES" => "N"],
                    'AUTHOR_ID' => 1,
                    'BINDINGS' => array(array('ENTITY_TYPE_ID' => \CCrmOwnerType::Contact, 'ENTITY_ID' => $this->contactId))
                )
            );
        }
    }

    /**
     * Получить кешбек, который можно списать по данной сделке
     * @param $opportunity
     * @return \Bitrix\Main\ORM\Objectify\Collection|bool|float|int|mixed
     */
    public function getAvailableCashback($opportunity = 0)
    {
        if (!$this->Pet) {
            return false;
        }

        //TODO: прикрутить проверку на год
        if (!$opportunity) {
            $opportunity = $this->CurrentDeal->get("OPPORTUNITY");
        }
        $cashbackBalance = $this->getCashbackBalance();
        if (!$cashbackBalance) {
            return false;
        }
        $maxCashback = self::CASHBACK_DISCOUNT_PERCENT * $opportunity;
        if ($maxCashback > $cashbackBalance) {
            $writeOffCashback = $cashbackBalance;
        } else {
            $writeOffCashback = $maxCashback;
        }

        return round($writeOffCashback);
    }

    public function getCashbackBalance()
    {
        return $this->Pet->get($this->PetsObject->getUfCashbackCode());
    }

    /**
     * Списание кешбек-баллов со счета питомца и зачисление скидки в сделку
     * @return false|void
     * @throws \Exception
     */
    public function writeOffCashback()
    {
        $cashbackPoints = $this->getAvailableCashback();
        $opportunity = $this->CurrentDeal->get("OPPORTUNITY");
        if (!$cashbackPoints) {
            $this->CurrentDeal->set(Deal::FIELD_AMOUNT_CODE, $opportunity)->save();
            return false;
        }
        $isAlreadyWriteOff = CashbackOperationsTable::query()
            ->setSelect(["ID", "DEAL_ID"])
            ->setFilter(["DEAL_ID" => $this->dealId, "<VALUE" => 0])
            ->exec()->fetch();
        if ($isAlreadyWriteOff) {
            return false;
        }

        $this->CurrentDeal->set(Deal::FIELD_CASHBACK_CODE, $cashbackPoints);
        $this->CurrentDeal->set(Deal::FIELD_AMOUNT_CODE, $opportunity - $cashbackPoints)->save();

        $this->Pet->set($this->PetsObject->getUfCashbackCode(), ($this->Pet->get($this->PetsObject->getUfCashbackCode()) - $cashbackPoints))->save();
        $arOperation = [
            "DEAL_ID" => $this->dealId,
            "CONTACT_ID" => $this->contactId,
            "PET_ID" => $this->Pet->getId(),
            "VALUE" => -$cashbackPoints
        ];

        CashbackOperationsTable::add($arOperation);
        $entryID = \Bitrix\Crm\Timeline\CommentEntry::create(
            array(
                'TEXT' => 'По [URL=https://b24.abcgrooming.ru/crm/deal/details/' . $this->dealId . '/]сделке #' . $this->dealId . '[/URL] списано ' . $cashbackPoints . ' кэшбек баллов.',
                'SETTINGS' => ["HAS_FILES" => "N"],
                'AUTHOR_ID' => 1,
                'BINDINGS' => array(array('ENTITY_TYPE_ID' => \CCrmOwnerType::Contact, 'ENTITY_ID' => $this->contactId))
            )
        );
    }

    /** Проверка условий для начисления кешбека
     * @return bool
     */
    public function checkEarnCondition()
    {
        $Breed = new Breed();
        $isAlreadyEarned = CashbackOperationsTable::query()
            ->setSelect(["ID", "DEAL_ID"])
            ->setFilter(["DEAL_ID" => $this->dealId, ">VALUE" => 0])
            ->exec()->fetch();
        if ($isAlreadyEarned) {
            return false;
        }
        if ($Breed->isSmallDog($this->Pet->get($this->PetsObject->getUfBreedCode()))) {
            return true;
        }
        return false;
    }

    /** Метод для получения суммы сделки, от которой начисляется кешбек (без учета товаров доп услуг и кешбека)
     * @return float|mixed|void
     */
    public function getSaleAmount()
    {
        $arProducts = \CCrmDeal::LoadProductRows($this->dealId);
        $haveBasic = false;
        $haveHaircut = false;
        if (empty($arProducts) && $this->CurrentDeal->get("OPPORTUNITY") < 700) {
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
        $dealAmount = $this->CurrentDeal->get(Deal::FIELD_AMOUNT_CODE);
        if (!$dealAmount) {
            $dealAmount = $this->CurrentDeal->get("OPPORTUNITY");
        }
        $dealCashback = $this->CurrentDeal->get(Deal::FIELD_CASHBACK_CODE);
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

    /**
     * @return mixed
     */
/*    public function getCashbackBalance()
    {
        return $this->CashbackBalance;
    }*/

/*    public function calculateMaxCashbackByDealId()
    {
        $Pets = new Pets();
        $petId = $this->CurrentDeal->get(Deal::FIELD_PET_CODE);
        if ($Pets->isBigPet($petId) || $Pets->isCat($petId)) {
            return 0;
        }

        $percent = $this->calculateMaxPercentForCashback();
        return (int)($this->CurrentDeal->getOpportunity() * $percent);
    }*/

/*    public function getAvailableCashbackForDeal() {
        $maxCashback = $this->calculateMaxCashbackByDealId();
        $cashbackBalance = $this->getCashbackBalance();
        if ($cashbackBalance <= $maxCashback) {
            return $cashbackBalance;
        } else {
            return $maxCashback;
        }
    }*/

    public function calculateMaxPercentForCashback()
    {
        /**
         * @var DateTime $currentTime
         */
        $currentTime = $this->CurrentDeal->get(Deal::FIELD_TIME_START_CODE);
        $arDeal = DealTable::query()
            ->setSelect(["ID", Deal::FIELD_TIME_START_CODE])
            ->setFilter(['<' . Deal::FIELD_TIME_START_CODE => $currentTime, "CONTACT_ID" => $this->contactId, "CATEGORY_ID" => Deal::GROOMING_CATEGORY_ID, Deal::FIELD_PET_CODE => $this->Pet->getId()])
            ->setOrder([Deal::FIELD_TIME_START_CODE => "DESC"])
            ->setLimit(1)
            ->fetch();

        $percent = 0.10;

        if (!empty($arDeal)) {
            $diffDays = $currentTime->getDiff($arDeal[Deal::FIELD_TIME_START_CODE])->days;
            if ($diffDays > 90) {
                $percent = 0;
            } elseif ($diffDays >  60) {
                $percent = 0.05;
            } elseif ($diffDays > 30) {
                $percent = 0.07;
            }
        }

        return $percent;
    }

    /**
     * @return array|false
     */
    public function getCurrentDeal()
    {
        return $this->CurrentDeal;
    }
}