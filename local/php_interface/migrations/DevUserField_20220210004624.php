<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;
use Starlabs\Project\Grooming\Deal;
use Starlabs\Project\Grooming\Cashback;
use Sprint\Migration\Exceptions\HelperException;
use Starlabs\Project\SmartProcess\Pets;

class DevUserField_20220210004624 extends Version
{
    protected $description = "Создание пользовательского поля Кешбек для питомцев и сделки";

    protected $moduleVersion = "4.0.2";

    public function up()
    {
        $this->checkModules();
        $helper = $this->getHelperManager();

        $helper->UserTypeEntity()->saveUserTypeEntity(
            [
                'ENTITY_ID'         => 'CRM_DEAL',
                'FIELD_NAME'        => Deal::FIELD_CASHBACK_CODE,
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
                        'en' => 'Затраченный кешбэк',
                        'ru' => 'Затраченный кешбэк',
                    ],
                'LIST_COLUMN_LABEL' =>
                    [
                        'en' => 'Затраченный кешбэк',
                        'ru' => 'Затраченный кешбэк',
                    ],
                'LIST_FILTER_LABEL' =>
                    [
                        'en' => 'Затраченный кешбэк',
                        'ru' => 'Затраченный кешбэк',
                    ],
            ]
        );
        $helper->UserTypeEntity()->saveUserTypeEntity(
            [
                'ENTITY_ID'         => 'CRM_CONTACT',
                'FIELD_NAME'        => (new Pets())->getUfCashbackCode(),
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
                        'en' => 'Баланс кешбэка',
                        'ru' => 'Баланс кешбэка',
                    ],
                'LIST_COLUMN_LABEL' =>
                    [
                        'en' => 'Баланс кешбэка',
                        'ru' => 'Баланс кешбэка',
                    ],
                'LIST_FILTER_LABEL' =>
                    [
                        'en' => 'Баланс кешбэка',
                        'ru' => 'Баланс кешбэка',
                    ],
            ]
        );
    }

    public function down()
    {
        $this->checkModules();
        $helper = $this->getHelperManager();

        $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('CRM_DEAL', Deal::FIELD_CASHBACK_CODE);
        $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('CRM_CONTACT', (new Pets())->getUfCashbackCode());
    }

    private function checkModules()
    {
        Loader::requireModule('crm');
        Loader::requireModule('starlabs.project');
    }
}
