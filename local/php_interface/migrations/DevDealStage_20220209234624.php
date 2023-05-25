<?php

namespace Sprint\Migration;

use Bitrix\Crm\PhaseSemantics;
use Bitrix\Crm\StatusTable;
use Bitrix\Main\Loader;
use Starlabs\Project\Grooming\Deal;
use Sprint\Migration\Exceptions\HelperException;

class DevDealStage_20220209234624 extends Version
{
    protected $description = "Создание стадии для сделок удаленных администратором";

    protected $moduleVersion = "4.0.2";

    public function up()
    {
        $this->checkModules();
        $arData = [
            'NAME' => 'Удалена из календаря',
            'STATUS_ID' => Deal::getDeletedStatusId(),
            'SORT' => 31,
            'SYSTEM' => 'N',
            'COLOR' => '#ff0000',
            'SEMANTICS' => PhaseSemantics::FAILURE,
            'CATEGORY_ID' => Deal::GROOMING_CATEGORY_ID,
            'ENTITY_ID' => Deal::getGroomingStagesEntityId()
        ];

        $Status = new \CCrmStatus(Deal::getGroomingStagesEntityId());
        $Status->Add($arData);
    }

    public function down()
    {
        $this->checkModules();
        $stagesEntityId = Deal::getGroomingStagesEntityId();
        $statusObject = StatusTable::query()
            ->setSelect(["ID"])
            ->setFilter(["ENTITY_ID" => $stagesEntityId, 'STATUS_ID' => Deal::getDeletedStatusId()])
            ->exec()->fetchObject();
        if (!$statusObject) {
            throw new HelperException('Стадия не найдена');
        }
        $deleteErrors = $statusObject->delete()->getErrorMessages();
        if ($deleteErrors) {
            throw new HelperException($deleteErrors);
        }
    }

    private function checkModules()
    {
        Loader::requireModule('crm');
        Loader::requireModule('starlabs.project');
    }
}
