<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

use Bitrix\Intranet\Site\Sections\AutomationSection;
use Bitrix\Main\Loader;

$GLOBALS['APPLICATION']->setPageProperty('topMenuSectionDir', SITE_DIR . 'automation/');

if (!Loader::includeModule('intranet'))
{
	return;
}


$rootItem = AutomationSection::getRootMenuItem();
$menuItems = [
	[
		$rootItem[0],
		$rootItem[1],
	]
];

$items = AutomationSection::getItems();
foreach ($items as $item)
{
	if ($item['available'])
	{
		$menuItems[] = [
			$item['title'] ?? '',
			$item['url'] ?? '',
			$item['extraUrls'] ?? [],
			$item['menuData'] ?? [],
			'',
		];
	}
}

$aMenuLinks = array_merge($menuItems, $aMenuLinks);