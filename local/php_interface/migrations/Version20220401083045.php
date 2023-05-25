<?php

namespace Sprint\Migration;


use Starlabs\Project\Grooming\Deal;

class Version20220401083045 extends Version
{
    protected $description = "Создание пользовательского поля Использовать Кешбек для сделки";

    protected $moduleVersion = "4.0.2";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->saveUserTypeEntity(array (
  'ENTITY_ID' => 'CRM_DEAL',
  'FIELD_NAME' => Deal::FIELD_USE_CASHBACK_CODE,
  'USER_TYPE_ID' => 'boolean',
  'XML_ID' => NULL,
  'SORT' => '100',
  'MULTIPLE' => 'N',
  'MANDATORY' => 'N',
  'SHOW_FILTER' => 'E',
  'SHOW_IN_LIST' => 'Y',
  'EDIT_IN_LIST' => 'Y',
  'IS_SEARCHABLE' => 'N',
  'SETTINGS' => 
  array (
    'DEFAULT_VALUE' => 0,
    'DISPLAY' => 'CHECKBOX',
    'LABEL' => 
    array (
      0 => NULL,
      1 => NULL,
    ),
    'LABEL_CHECKBOX' => 'Использовать Кешбек',
  ),
  'EDIT_FORM_LABEL' => 
  array (
    'en' => 'Использовать Кешбек',
    'ru' => 'Использовать Кешбек',
  ),
  'LIST_COLUMN_LABEL' => 
  array (
    'en' => 'Использовать Кешбек',
    'ru' => 'Использовать Кешбек',
  ),
  'LIST_FILTER_LABEL' => 
  array (
    'en' => 'Использовать Кешбек',
    'ru' => 'Использовать Кешбек',
  ),
  'ERROR_MESSAGE' => 
  array (
    'en' => NULL,
    'ru' => NULL,
  ),
  'HELP_MESSAGE' => 
  array (
    'en' => NULL,
    'ru' => NULL,
  ),
));
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->deleteUserTypeEntityIfExists('CRM_DEAL', Deal::FIELD_USE_CASHBACK_CODE);
    }
}
