<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;
use Starlabs\Project\Grooming\Deal;
use Starlabs\Project\Grooming\Cashback;
use Sprint\Migration\Exceptions\HelperException;

class UserField_20220328004624 extends Version
{
    protected $description = "Создание пользовательского поля Сумма к оплате для сделки";

    protected $moduleVersion = "4.0.2";

    public function up()
    {
        $this->checkModules();
        $helper = $this->getHelperManager();

        $helper->UserTypeEntity()->saveUserTypeEntity(
            [
                'ENTITY_ID'         => 'CRM_DEAL',
                'FIELD_NAME'        => Deal::FIELD_AMOUNT_CODE,
                'USER_TYPE_ID'      => 'double',
                'XML_ID'            => '',
                'SORT'              => '100',
                'MULTIPLE'          => 'N',
                'MANDATORY'         => 'N',
                'SHOW_FILTER'       => 'E',
                'SHOW_IN_LIST'      => 'Y',
                'EDIT_IN_LIST'      => 'Y',
                'IS_SEARCHABLE'     => 'N',
                'SETTINGS'          =>
                    [
                        'PRECISION'     => 3,
                        'SIZE'          => 20,
                        'MIN_VALUE'     => 0.0,
                        'MAX_VALUE'     => 0.0,
                        'DEFAULT_VALUE' => '',
                    ],
                'EDIT_FORM_LABEL'   =>
                    [
                        'en' => 'Сумма к оплате',
                        'ru' => 'Сумма к оплате',
                    ],
                'LIST_COLUMN_LABEL' =>
                    [
                        'en' => 'Сумма к оплате',
                        'ru' => 'Сумма к оплате',
                    ],
                'LIST_FILTER_LABEL' =>
                    [
                        'en' => 'Сумма к оплате',
                        'ru' => 'Сумма к оплате',
                    ],
            ]
        );
    }

    public function down()
    {
        $this->checkModules();
        $helper = $this->getHelperManager();

        $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('CRM_DEAL', Deal::FIELD_AMOUNT_CODE);
    }

    private function checkModules()
    {
        Loader::requireModule('crm');
        Loader::requireModule('starlabs.project');
    }
}
