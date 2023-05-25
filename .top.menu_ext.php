<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

if (SITE_TEMPLATE_ID !== "bitrix24")
{
	return;
}

use Bitrix\Intranet\Site\Sections\AutomationSection;
use \Bitrix\Landing\Rights;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

global $APPLICATION;

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/.top.menu_ext.php");

if (!function_exists("getLeftMenuItemLink"))
{
	function getLeftMenuItemLink($sectionId, $defaultLink = "")
	{
		$settings = CUserOptions::GetOption("UI", $sectionId);
		return
			is_array($settings) && isset($settings["firstPageLink"]) && mb_strlen($settings["firstPageLink"]) ?
				$settings["firstPageLink"] :
				$defaultLink;
	}
}

if (!function_exists("getItemLinkId"))
{
	function getItemLinkId($link)
	{
		$menuId = str_replace("/", "_", trim($link, "/"));
		return "top_menu_id_".$menuId;
	}
}

$userId = $GLOBALS["USER"]->GetID();

if (defined("BX_COMP_MANAGED_CACHE"))
{
	global $CACHE_MANAGER;
	$CACHE_MANAGER->registerTag("bitrix24_left_menu");
	$CACHE_MANAGER->registerTag("crm_change_role");
	$CACHE_MANAGER->registerTag("USER_NAME_".$userId);
}

global $USER;

$arMenuB24 = array(
	array(
		GetMessage("TOP_MENU_LIVE_FEED2"),
		file_exists($_SERVER["DOCUMENT_ROOT"].SITE_DIR."stream/") ? SITE_DIR."stream/" : SITE_DIR,
		array(),
		array(
			"name" => "live_feed",
			"counter_id" => "live-feed",
			"menu_item_id" => "menu_live_feed",
		),
		""
	)
);

if ($GLOBALS["USER"]->IsAuthorized() && Loader::includeModule("socialnetwork"))
{
	$arUserActiveFeatures = CSocNetFeatures::GetActiveFeatures(SONET_ENTITY_USER, $GLOBALS["USER"]->GetID());
	$arSocNetFeaturesSettings = CSocNetAllowed::GetAllowedFeatures();

	$allowedFeatures = array();
	foreach (array("tasks", "files", "photo", "blog", "calendar") as $feature)
	{
		$allowedFeatures[$feature] =
			array_key_exists($feature, $arSocNetFeaturesSettings) &&
			array_key_exists("allowed", $arSocNetFeaturesSettings[$feature]) &&
			in_array(SONET_ENTITY_USER, $arSocNetFeaturesSettings[$feature]["allowed"]) &&
			is_array($arUserActiveFeatures) &&
			in_array($feature, $arUserActiveFeatures)
		;
	}

	if ($allowedFeatures["tasks"])
	{
		$arMenuB24[] = [
			GetMessage("TOP_MENU_TASKS"),
			SITE_DIR . "tasks/menu/",
			[],
			[
				"name" => "tasks",
				"counter_id" => "tasks_total",
				"menu_item_id" => "menu_tasks",
				"real_link" => getLeftMenuItemLink(
					"tasks_panel_menu",
					SITE_DIR."company/personal/user/".$userId."/tasks/"
				),
				"sub_link" => SITE_DIR."company/personal/user/".$userId."/tasks/task/edit/0/",
				"top_menu_id" => "tasks_panel_menu",
			],
			"CBXFeatures::IsFeatureEnabled('Tasks')"
		];
	}

	if (
		$allowedFeatures["calendar"]
		&& CBXFeatures::IsFeatureEnabled('Calendar')
		|| CBXFeatures::IsFeatureEnabled('CompanyCalendar')
	)
	{
		$arMenuB24[] = array(
			GetMessage("TOP_MENU_CALENDAR"),
			SITE_DIR."calendar/",
			array(
				SITE_DIR."company/personal/user/".$userId."/calendar/",
				SITE_DIR."calendar/"
			),
			array(
				"real_link" => getLeftMenuItemLink(
					"top_menu_id_calendar",
					$allowedFeatures["calendar"] && CBXFeatures::IsFeatureEnabled('Calendar') ? SITE_DIR."company/personal/user/".$userId."/calendar/" : SITE_DIR."calendar/"
				),
				"menu_item_id" => "menu_calendar",
				"counter_id" => "calendar",
				"top_menu_id" => "top_menu_id_calendar",
				"sub_link" => SITE_DIR."company/personal/user/".$userId."/calendar/?EVENT_ID=NEW",
			),
			""
		);
	}

	if (
		Loader::includeModule("disk")
		&& (
			$allowedFeatures["files"]
			&& CBXFeatures::IsFeatureEnabled('PersonalFiles')
			|| CBXFeatures::IsFeatureEnabled('CommonDocuments')
		)
	)
	{
		$diskEnabled = \Bitrix\Main\Config\Option::get('disk', 'successfully_converted', false);
		$diskPath =
			$diskEnabled === "Y" ?
				SITE_DIR."company/personal/user/".$userId."/disk/path/" :
				SITE_DIR."company/personal/user/".$userId."/files/lib/"
		;

		$arMenuB24[] = array(
			GetMessage("TOP_MENU_DISK"),
			SITE_DIR."docs/",
			array(
				$diskPath,
				SITE_DIR."docs/",
				SITE_DIR."company/personal/user/".$userId."/disk/volume/",
				SITE_DIR."company/personal/user/".$userId."/disk/"
			),
			array(
				"real_link" => getLeftMenuItemLink(
					"top_menu_id_docs",
					CBXFeatures::IsFeatureEnabled('PersonalFiles') ? $diskPath : SITE_DIR."docs/"
				),
				"menu_item_id" => "menu_files",
				"top_menu_id" => "top_menu_id_docs",
			),
			""
		);
		if ($diskEnabled === "Y" && \Bitrix\Main\Config\Option::get('disk', 'documents_enabled', 'N') === 'Y')
		{
			$arMenuB24[] = array(
				GetMessage("TOP_MENU_DISK_DOCUMENTS"),
				SITE_DIR."company/personal/user/".$userId."/disk/documents/",
				[],
				array(
					"menu_item_id" => "menu_documents",
				),
				""
			);
		}
	}
}

if (Loader::includeModule("crm") && CCrmPerms::IsAccessEnabled())
{
	$counterId = CCrmSaleHelper::isWithOrdersMode() ? 'crm_all' : 'crm_all_no_orders';
	$arMenuB24[] = [
		GetMessage("TOP_MENU_CRM"),
		SITE_DIR."crm/menu/",
		[SITE_DIR."crm/"],
		[
			"real_link" => \Bitrix\Crm\Settings\EntityViewSettings::getDefaultPageUrl(),
			"counter_id" => $counterId,
			"menu_item_id" => "menu_crm_favorite",
			"top_menu_id" => "crm_control_panel_menu"
		],
		""
	];
}
// NEW MENU
// else
// {
$arMenuB24[] = [
	GetMessage("TOP_MENU_CONTACT_CENTER"),
	SITE_DIR . "services/contact_center/",
	[],
	[
		"real_link" => getLeftMenuItemLink(
			"top_menu_id_contact_center",
			SITE_DIR . "services/contact_center/"
		),
		"menu_item_id" => "menu_contact_center",
		"top_menu_id" => "top_menu_id_contact_center",
	],
	"",
];
//}

// OLD MENU
if (CModule::IncludeModule('crm') && \Bitrix\Crm\Tracking\Manager::isAccessible())
{
	$arMenuB24[] = [
		Loc::getMessage('TOP_MENU_CRM_TRACKING'),
		SITE_DIR . 'crm/tracking/',
		[],
		[
			'menu_item_id' => 'menu_crm_tracking',
		],
		'',
	];
}

// OLD MENU
if (Loader::includeModule('report') && \Bitrix\Report\VisualConstructor\Helper\Analytic::isEnable())
{
	$arMenuB24[] = [
		Loc::getMessage('TOP_MENU_CRM_ANALYTICS'),
		SITE_DIR . 'report/analytics/',
		[],
		[
			'real_link' => getLeftMenuItemLink(
				'top_menu_id_analytics',
				SITE_DIR . 'report/analytics/'
			),
			'menu_item_id' => 'menu_analytics',
			'top_menu_id' => 'top_menu_id_analytics',
		],
	];
}

/* NEW MENU
if (Loader::includeModule("crm") && CCrmSaleHelper::isShopAccess())
{
	$includeCounter = CCrmSaleHelper::isWithOrdersMode();
	$parameters = [
		'real_link' => getLeftMenuItemLink(
			'store',
			'/shop/orders/menu/'
		),
		'menu_item_id' => 'menu_shop',
		'top_menu_id' => 'store',
	];
	if ($includeCounter)
	{
		$parameters['counter_id'] = 'shop_all';
	}

	$arMenuB24[] = [
		GetMessage("TOP_MENU_SITES_AND_STORES"),
		SITE_DIR . "shop/menu/",
		[
			SITE_DIR . "shop/",
			SITE_DIR . "sites/",
		],
		$parameters,
		"",
	];
}
else if (Loader::includeModule('landing') && Rights::hasAdditionalRight(Rights::ADDITIONAL_RIGHTS['menu24']))
{
	$arMenuB24[] = [
		GetMessage("TOP_MENU_SITES"),
		SITE_DIR . "sites/",
		[],
		[
			"menu_item_id" => "menu_sites",
		],
		""
	];
}*/

// OLD MENU
if (Loader::includeModule('crm') && CCrmSaleHelper::isShopAccess())
{
	if (Loader::includeModule('salescenter') && \Bitrix\SalesCenter\Driver::getInstance()->isEnabled())
	{
		$arMenuB24[] = [
			Loc::getMessage('MENU_SALESCENTER_SECTION'),
			'/saleshub/',
			[],
			[
				'real_link' => getLeftMenuItemLink(
					'top_menu_id_saleshub',
					'/saleshub/'
				),
				'menu_item_id' => 'menu-sale-center',
				'top_menu_id' => 'top_menu_id_saleshub',
				'is_beta' => true,
			],
			''
		];
	}

	$arMenuB24[] = [
		Loc::getMessage('TOP_MENU_SHOP'),
		'/shop/menu/',
		[
			'/shop/',
		],
		[
			'real_link' => getLeftMenuItemLink(
				'store',
				'/shop/orders/menu/'
			),
			'menu_item_id' => 'menu_shop',
			'top_menu_id' => 'store',
			'is_beta' => true,
			'counter_id' => CCrmSaleHelper::isWithOrdersMode() ? 'shop_all' : '',
		],
		''
	];
}

if (Loader::includeModule("sender") && \Bitrix\Sender\Security\Access::current()->canViewAnything())
{
	$arMenuB24[] = [
		GetMessage("TOP_MENU_MARKETING2"),
		SITE_DIR."marketing/",
		[],
		[
			"real_link" => getLeftMenuItemLink(
				"top_menu_id_marketing",
				SITE_DIR."marketing/"
			),
			"menu_item_id" => "menu_marketing",
			'top_menu_id' => 'top_menu_id_marketing',
		],
		""
	];
}

// OLD MENU
if (Loader::includeModule('landing'))
{
	if (Rights::hasAdditionalRight(Rights::ADDITIONAL_RIGHTS['menu24']))
	{
		$arMenuB24[] = [
			Loc::getMessage('TOP_MENU_SITES'),
			SITE_DIR . 'sites/',
			[],
			[
				'menu_item_id' => 'menu_sites',
			],
			'',
		];
	}
	if (Rights::hasAdditionalRight(Rights::ADDITIONAL_RIGHTS['menu24'], 'knowledge'))
	{
		$arMenuB24[] = [
			Loc::getMessage('TOP_MENU_KNOWLEDGE'),
			SITE_DIR . 'kb/',
			[],
			[
				'menu_item_id' => 'menu_knowledge',
				'is_beta' => true,
			],
			'',
		];
	}
}

// OLD MENU
if (CModule::IncludeModule('im'))
{
	$arMenuB24[] = [
		GetMessage('TOP_MENU_IM_MESSENGER'),
		SITE_DIR . 'online/',
		[],
		[
			'counter_id' => 'im-message',
			'menu_item_id' => 'menu_im_messenger',
			'can_be_first_item' => false,
		],
		'CBXFeatures::IsFeatureEnabled("WebMessenger")',
	];

	$arMenuB24[] = [
		Loc::getMessage('TOP_MENU_IM_CONFERENCE'),
		SITE_DIR . 'conference/',
		[],
		[
			'menu_item_id' => 'menu_conference',
			'top_menu_id' => 'top_menu_id_conference',
		],
		'',
	];
}

if (Loader::includeModule("intranet") && CIntranetUtils::IsExternalMailAvailable())
{
	$warningLink = $mailLink = \Bitrix\Main\Config\Option::get('intranet', 'path_mail_client', SITE_DIR . 'mail/');

	$arMenuB24[] = array(
		GetMessage("TOP_MENU_MAIL"),
		$mailLink,
		array(),
		array(
			"counter_id" => "mail_unseen",
			"warning_link" => $warningLink,
			"warning_title" => GetMessage("MENU_MAIL_CHANGE_SETTINGS"),
			"menu_item_id" => "menu_external_mail",
		),
		""
	);
}

if (Loader::includeModule("socialnetwork"))
{
	$canCreateGroup = \Bitrix\Socialnetwork\Helper\Workgroup::canCreate();

	$groupPath = SITE_DIR."workgroups/";
	$arMenuB24[] = [
		GetMessage("TOP_MENU_GROUPS"),
		$groupPath,
		[],
		[
			"real_link" => getLeftMenuItemLink(
				"sonetgroups_panel_menu",
				$groupPath
			),
			"menu_item_id"=>"menu_all_groups",
			"top_menu_id" => "sonetgroups_panel_menu"
		] + ($canCreateGroup ? ["sub_link" => SITE_DIR."company/personal/user/".$userId."/groups/create/"] : []),
		"CBXFeatures::IsFeatureEnabled('Workgroups')"
	];
}

// OLD MENU
if (Loader::includeModule('rpa') && \Bitrix\Rpa\Driver::getInstance()->isEnabled())
{
	$arMenuB24[] = [
		Loc::getMessage('MENU_RPA_SECTION'),
		'/rpa/',
		[],
		[
			'real_link' => getLeftMenuItemLink(
				'top_menu_id_rpa',
				'/rpa/'
			),
			'counter_id' => 'rpa_tasks',
			'menu_item_id' => 'menu_rpa',
			'top_menu_id' => 'top_menu_id_rpa',
			'is_beta' => true,
		],
		'',
	];
}

// OLD MENU
if (ModuleManager::isModuleInstalled('bizproc'))
{
	$arMenuB24[] = [
		Loc::getMessage('TOP_MENU_BIZPROC'),
		SITE_DIR . 'bizproc/',
		[
			SITE_DIR . 'company/personal/bizproc/',
			SITE_DIR . 'company/personal/processes/',
		],
		[
			'real_link' => getLeftMenuItemLink(
				'top_menu_id_bizproc',
				SITE_DIR . 'company/personal/bizproc/'
			),
			'counter_id' => 'bp_tasks',
			'menu_item_id' => 'menu_bizproc_sect',
			'top_menu_id' => 'top_menu_id_bizproc',
		],
		'CBXFeatures::IsFeatureEnabled("BizProc")',
	];
}

// OLD MENU
$arMenuB24[] = [
	Loc::getMessage('TOP_MENU_COMPANY'),
	SITE_DIR . 'company/',
	[],
	[
		'real_link' => getLeftMenuItemLink(
			'top_menu_id_company',
			SITE_DIR . 'company/vis_structure.php'
		),
		'menu_item_id' => 'menu_company',
		'top_menu_id' => 'top_menu_id_company',
	],
];

$aboutSectionExists = file_exists($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'about/');

/*
NEW MENU
$arMenuB24[] = [
	$aboutSectionExists ? Loc::getMessage('TOP_MENU_COMPANY') : Loc::getMessage('TOP_MENU_COMPANY_SECTION'),
	SITE_DIR . 'company/',
	[
		'/timeman/',
		'/kb/',
		'/conference/',
	],
	[
		'real_link' => getLeftMenuItemLink(
			'top_menu_id_company',
			SITE_DIR . 'company/vis_structure.php'
		),
		'menu_item_id' => 'menu_company',
		'top_menu_id' => 'top_menu_id_company',
		'class' => 'menu-company',
	],
];
*/

if ($aboutSectionExists)
{
	$arMenuB24[] = [
		Loc::getMessage('TOP_MENU_ABOUT'),
		SITE_DIR . 'about/',
		[SITE_DIR . 'about/'],
		[
			'real_link' => getLeftMenuItemLink(
				'top_menu_id_about',
				SITE_DIR . 'about/'
			),
			'menu_item_id' => 'menu_about_sect',
			'top_menu_id' => 'top_menu_id_about',
		],
		'',
	];
}

/* NEW MENU
if (Loader::includeModule('intranet') && AutomationSection::isAvailable())
{
	$arMenuB24[] = AutomationSection::getRootMenuItem();
}*/

/* NEW MENU
if ($aboutSectionExists)
{
	$arMenuB24[] = [
		Loc::getMessage('TOP_MENU_ABOUT'),
		SITE_DIR . 'about/',
		[],
		[
			'real_link' => getLeftMenuItemLink(
				'top_menu_id_about',
				SITE_DIR . 'about/'
			),
			'menu_item_id' => 'menu_about_sect',
			'top_menu_id' => 'top_menu_id_about',
		],
		'',
	];
}
*/

// OLD MENU
if (file_exists($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'timeman/'))
{
	$arMenuB24[] = [
		Loc::getMessage('TOP_MENU_TIMEMAN'),
		SITE_DIR . 'timeman/',
		[],
		[
			'real_link' => getLeftMenuItemLink(
				'top_menu_id_timeman',
				SITE_DIR . 'timeman/'
			),
			'menu_item_id' => 'menu_timeman_sect',
			'top_menu_id' => 'top_menu_id_timeman',
		],
		"CBXFeatures::IsFeatureEnabled('StaffAbsence') || CBXFeatures::IsFeatureEnabled('timeman') || CBXFeatures::IsFeatureEnabled('Meeting')",
	];
}

//merge with static items from top.menu
foreach ($aMenuLinks as $arItem)
{
	$menuLink = $arItem[1];

	if (preg_match("~/(workgroups|crm|marketplace|docs|timeman|bizproc|company|about|services)/$~i", $menuLink))
	{
		continue;
	}

	$menuId = getItemLinkId($menuLink);
	$arItem[3]["real_link"] = getLeftMenuItemLink($menuId, $menuLink);
	$arItem[3]["top_menu_id"] = $menuId;
	$arMenuB24[] = $arItem;
}

$arMenuB24[] = array(
	GetMessage("TOP_MENU_SERVICES"),
	SITE_DIR."services/",
	array(SITE_DIR."services/"),
	array(
		"real_link" => getLeftMenuItemLink(
			"top_menu_id_services",
			SITE_DIR."services/"
		),
		"menu_item_id"=>"menu_services_sect",
		"top_menu_id" => "top_menu_id_services"
	),
	""
);

$arMenuB24[] = array(
	GetMessage("TOP_MENU_MARKETPLACE_3"),
	SITE_DIR."marketplace/",
	array(SITE_DIR."marketplace/"),
	array(
		"real_link" => getLeftMenuItemLink(
			"top_menu_id_marketplace",
			SITE_DIR."marketplace/"
		),
		"menu_item_id"=>"menu_marketplace_sect",
		"top_menu_id" => "top_menu_id_marketplace"
	),
	"IsModuleInstalled('rest')"
);

// OLD MENU
if (Loader::includeModule('intranet'))
{
	$items = [
		AutomationSection::getAI(),
		AutomationSection::getOnec(),
	];

	foreach ($items as $item)
	{
		if ($item['available'])
		{
			$arMenuB24[] = [
				$item['title'] ?? '',
				$item['url'] ?? '',
				$item['extraUrls'] ?? [],
				$item['menuData'] ?? [],
				'',
			];
		}
	}
}

$arMenuB24[] = [
	Loc::getMessage('TOP_MENU_TELEPHONY'),
	SITE_DIR.'telephony/',
	[SITE_DIR.'telephony/'],
	[
		'real_link' => getLeftMenuItemLink(
			'top_menu_id_telephony',
			SITE_DIR.'telephony/'
		),
		'menu_item_id' => 'menu_telephony',
		'top_menu_id' => 'top_menu_id_telephony'
	],
	'CModule::IncludeModule("voximplant") && Bitrix\Voximplant\Security\Helper::isMainMenuEnabled()'
];


$arMenuB24[] = Array(
	GetMessage("TOP_MENU_CONFIGS"),
	SITE_DIR."configs/",
	Array(SITE_DIR."configs/"),
	Array(
		"real_link" => getLeftMenuItemLink(
			"top_menu_id_configs",
			SITE_DIR."configs/"
		),
		"menu_item_id" => "menu_configs_sect",
		"top_menu_id" => "top_menu_id_configs"
	),
	'$USER->IsAdmin()'
);

$manager = \Bitrix\Main\DI\ServiceLocator::getInstance()->get('intranet.customSection.manager');
$manager->appendSuperLeftMenuSections($arMenuB24);

$rsSite = CSite::GetList("sort", "asc", $arFilter = array("ACTIVE" => "Y"));
$exSiteId = COption::GetOptionString("extranet", "extranet_site");
while ($site = $rsSite->Fetch())
{
	if ($site["LID"] !== $exSiteId && $site["LID"] !== SITE_ID)
	{
		$url = ((CMain::IsHTTPS()) ? "https://" : "http://").$site["SERVER_NAME"].$site["DIR"];
		$arMenuB24[] = array(
			htmlspecialcharsbx($site["NAME"]),
			htmlspecialcharsbx($url),
			array(),
			array(),
			""
		);
	}
}

$aMenuLinks = $arMenuB24;
