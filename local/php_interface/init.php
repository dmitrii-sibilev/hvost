<?php
use Bitrix\Main\EventManager;
use CUtil;

//var_dump($_REQUEST );

if (stristr($_SERVER['REQUEST_URI'], 'crm/type/129/details') !== FALSE) {
EventManager::getInstance()->addEventHandler('main', 'OnEpilog', 'addCustomJsOnEpilog');
}


function addCustomJsOnEpilog()
{
    \CJSCore::Init(['masked_input']);
    $arJsConfig = array(
        'BODRII_REQUISITES' => [
            'js' => '/bitrix/js/bodrii_duration_mask/main.js',
            'rel' => [],
        ]
    );
    foreach ($arJsConfig as $ext => $arExt) {
        \CJSCore::RegisterExt($ext, $arExt);
    }
    CUtil::InitJSCore(['BODRII_REQUISITES']);
}
