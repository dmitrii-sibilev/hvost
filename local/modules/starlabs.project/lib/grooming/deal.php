<?php

namespace Starlabs\Project\Grooming;

use Bitrix\Crm\DealTable;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\Type\DateTime;
use Starlabs\Tools\Helpers\p;

class Deal
{
    const FIELD_PET_CODE = "UF_CRM_1637880393";
    const FIELD_MASTER_CODE = "UF_CRM_1637880352";
    const FIELD_SALON_CODE = "UF_CRM_1637880244";
    const FIELD_MASTER_TIME_FINISH_CODE = "UF_CRM_1637880169732";
    const FIELD_MASTER_TIME_START_CODE = "UF_CRM_1637880144969";
    const FIELD_TIME_FINISH_CODE = "UF_CRM_1637880120393";
    const FIELD_TIME_START_CODE = "UF_CRM_1637880063";
    const FIELD_CASHBACK_CODE = "UF_CRM_CASHBACK";
    const FIELD_AMOUNT_CODE = "UF_CRM_AMOUNT";
    const FIELD_USE_CASHBACK_CODE = "UF_CRM_USE_CASHBACK";
    const GROOMING_CATEGORY_ID = 1;
    const SALON_WORK_HOUR_BEGIN = 10;
    const SALON_WORK_HOUR_FINISH = 20;
    const DELETED_STAGE_CODE = 'EVENT_DELETED_1';
    const WON_STAGE_CODE = 'WON';
    const NEW_STAGE_CODE = 'NEW';
    const NOT_COME_STAGE_CODE = 'APOLOGY';

    /**
     * @param string $dealStart DateTime format d.m.Y H:i:s
     * @param string $dealFinish DateTime format d.m.Y H:i:s
     * @param string $masterStart DateTime format d.m.Y H:i:s
     * @param string $masterFinish DateTime format d.m.Y H:i:s
     * @throws \Exception
     */
    public static function verifyDealTime(string $dealStart, string $dealFinish, string $masterStart, string $masterFinish, $masterId, $dealId = null)
    {
        $dealStart = new DateTime($dealStart, 'd.m.Y H:i:s');
        $dealFinish = new DateTime($dealFinish, 'd.m.Y H:i:s');
        $masterStart = new DateTime($masterStart, 'd.m.Y H:i:s');
        $masterFinish = new DateTime($masterFinish, 'd.m.Y H:i:s');
        $masterStartFix = clone $masterStart;
        $masterFinishFix = clone $masterFinish;

        $masterStartFix->add('+1 minute');
        $masterFinishFix->add('-1 minute');

        $MinStart = clone $dealStart;
        $MinStart->setTime(self::SALON_WORK_HOUR_BEGIN, 0);

        $MaxEnd = clone $dealStart;
        $MaxEnd->setTime(self::SALON_WORK_HOUR_FINISH, 0);

        if ($MinStart->getDiff($dealStart)->invert) {
            Throw new \Exception('Время начала записи должно быть не раньше 10 часов');
        }

        if (!$MaxEnd->getDiff($dealFinish)->invert && $MaxEnd != $dealFinish) {
            Throw new \Exception('Время окончания записи должно быть не позже 20 часов');
        }

        $FilterMain = new ConditionTree();
        $FilterFirst = new ConditionTree();
        $FilterSecond = new ConditionTree();
        $FilterThird = new ConditionTree();

        $FilterMain->logic(ConditionTree::LOGIC_OR);
        $FilterFirst->whereBetween(Deal::FIELD_MASTER_TIME_START_CODE, $masterStartFix, $masterFinishFix);
        $FilterSecond->whereBetween(Deal::FIELD_MASTER_TIME_FINISH_CODE, $masterStartFix, $masterFinishFix);
        $FilterThird->logic(ConditionTree::LOGIC_AND);
        $FilterThird->where(
            [
                [Deal::FIELD_MASTER_TIME_START_CODE, '<=', $masterStart],
                [Deal::FIELD_MASTER_TIME_FINISH_CODE, '>=', $masterFinish],
            ]
        );
        $FilterMain->addCondition($FilterFirst);
        $FilterMain->addCondition($FilterSecond);
        $FilterMain->addCondition($FilterThird);



        $CrossDealsQuery = DealTable::query()
            ->setSelect(["ID", "MASTER_NAME" => "MASTER.NAME", "MASTER_LAST_NAME" => "MASTER.LAST_NAME", Deal::FIELD_MASTER_CODE, Deal::FIELD_MASTER_TIME_FINISH_CODE, Deal::FIELD_MASTER_TIME_START_CODE])
            ->where($FilterMain)
            ->addFilter(Deal::FIELD_MASTER_CODE, $masterId)
            ->registerRuntimeField('MASTER', [
                'data_type' => \Bitrix\Main\UserTable::getEntity(),
                'reference' => ['=this.' . Deal::FIELD_MASTER_CODE => 'ref.ID']
            ]);
        if ($dealId) {
            $CrossDealsQuery->addFilter('!ID', $dealId);
        }
        $CrossDealsRes = $CrossDealsQuery->exec();
        if ($arCrossDeal = $CrossDealsRes->fetch()) {
//            Throw new \Exception('Мастер ' . $arCrossDeal["MASTER_NAME"] . ' ' . $arCrossDeal["MASTER_LAST_NAME"] . ' занят с ' . $arCrossDeal[Deal::FIELD_MASTER_TIME_START_CODE]->format('H:i') . ' по ' . $arCrossDeal[Deal::FIELD_MASTER_TIME_FINISH_CODE]->format('H:i'));
        }
    }

    public static function getGroomingStagesEntityId()
    {
        return 'DEAL_STAGE_' . Deal::GROOMING_CATEGORY_ID;//TODO: исправить на self
    }

    public static function getDeletedStatusId()
    {
        return 'C' . Deal::GROOMING_CATEGORY_ID . ':' . Deal::DELETED_STAGE_CODE;
    }

    public static function getNotComeStatusId()
    {
        return 'C' . Deal::GROOMING_CATEGORY_ID . ':' . Deal::NOT_COME_STAGE_CODE;
    }

    public static function getNewStatusId()
    {
        return 'C' . Deal::GROOMING_CATEGORY_ID . ':' . Deal::NEW_STAGE_CODE;
    }
    public static function getWonStatusId()
    {
        return 'C' . Deal::GROOMING_CATEGORY_ID . ':' . Deal::WON_STAGE_CODE;
    }
}