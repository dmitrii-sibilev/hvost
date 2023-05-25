<?php

use Bitrix\Main\IO\File;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

// NEW MENU
return;

$companyMenu = $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'company/.left.menu_ext.php';
if (File::isFileExists($companyMenu))
{
	include($companyMenu);
}