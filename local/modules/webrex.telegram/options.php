<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Loader;

/**
 * @var string $mid module id from GET
 */

use Bitrix\Main\Localization\Loc;

global $APPLICATION, $USER, $USER_FIELD_MANAGER;

if (!$USER->IsAdmin()) {
    ShowError(Loc::getMessage('WEBREX_TELEGRAM_NO_ACCESS_ERROR'));
    return;
}

$moduleId = 'webrex.telegram';

Loader::includeModule($moduleId);

$tabs = [
    [
        'DIV' => 'settings',
        'TAB' => Loc::getMessage('WEBREX_TELEGRAM_TAB_SETTINGS_NAME'),
        'TITLE' => Loc::getMessage('WEBREX_TELEGRAM_TAB_SETTINGS_NAME')
    ],
];
$options = [
    'settings' => [
        [
            'BOT_TOKEN',
            Loc::getMessage('WEBREX_TELEGRAM_BOT_TOKEN'),
            '',
            ['text', '']
        ],
        [
            'WEBHOOK_SECRET_TOKEN',
            Loc::getMessage('WEBREX_TELEGRAM_WEBHOOK_SECRET_TOKEN'),
            '',
            ['text', '']
        ],
    ],
];


if (check_bitrix_sessid() && strlen($_POST['save']) > 0) {
    foreach ($options as $option) {
        __AdmSettingsSaveOptions($moduleId, $option);
    }
    LocalRedirect($APPLICATION->GetCurPageParam());
}

$tabControl = new CAdminTabControl('tabControl', $tabs);
$tabControl->Begin();


?>
<form method="POST"
      action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($mid) ?>&lang=<?= LANGUAGE_ID ?>">
    <? foreach ($tabs as $tabId => $tab) { ?>
        <? $tabControl->BeginNextTab(); ?>
        <? __AdmSettingsDrawList($moduleId, $options[$tab['DIV']]); ?>
    <? } ?>

    <? $tabControl->Buttons(array('btnApply' => false, 'btnCancel' => false, 'btnSaveAndAdd' => false)); ?>
    <?= bitrix_sessid_post(); ?>
    <? $tabControl->End(); ?>
</form>
