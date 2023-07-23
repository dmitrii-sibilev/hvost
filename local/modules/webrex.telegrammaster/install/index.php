<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;
use Webrex\Telegram\Helpers\Option;
use Webrex\Telegram\Request\Sender;

class webrex_telegrammaster extends CModule
{
  const MODULE_ID = 'webrex.telegrammaster';

  public $MODULE_ID = self::MODULE_ID;
  public $MODULE_VERSION;
  public $MODULE_VERSION_DATE;
  public $MODULE_NAME;
  public $MODULE_DESCRIPTION;

  function __construct()
  {
    $arModuleVersion = array();
    include(dirname(__FILE__) . '/version.php');
    $this->MODULE_VERSION = $arModuleVersion['VERSION'];
    $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

    $this->MODULE_NAME = Loc::getMessage('WEBREX_TELEGRAM_MASTER_MODULE_NAME');
    $this->MODULE_DESCRIPTION = Loc::getMessage('WEBREX_TELEGRAM_MASTER_MODULE_DESCRIPTION');

    $this->PARTNER_NAME = Loc::getMessage('WEBREX_TELEGRAM_MASTER_PARTNER_NAME');
    $this->PARTNER_URI = Loc::getMessage('WEBREX_TELEGRAM_MASTER_PARTNER_URI');
  }

  function DoInstall()
  {
    ModuleManager::registerModule(self::MODULE_ID);
    'CREATE TABLE `webrex_telegram_button` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CODE` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `TEXT` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `STAGE_ID` int(11) DEFAULT NULL,
  `SORT` int(11) DEFAULT 100,
  `URL` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`ID`)
);';
    'CREATE TABLE `webrex_telegram_stage` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CODE` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `NAME` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
);';
    'CREATE TABLE `webrex_telegram_chat` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `USERNAME` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `FIRST_NAME` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `CHAT_ID` bigint(20) DEFAULT NULL,
  `STAGE_ID` int(11) DEFAULT NULL,
  `PREVIOUS_STAGE_ID` int(11) DEFAULT NULL,
  `ACTIVE` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`ID`)
);';
    'CREATE TABLE `webrex_telegram_chat_message` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `MESSAGE_TIME` datetime DEFAULT NULL,
  `SENDER_TYPE` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `CHAT_ID` bigint(20) DEFAULT NULL,
  `CHAT_MESSAGE_ID` int(11) DEFAULT NULL,
  `MESSAGE_TEXT` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`ID`)
);';
    '';
    $this->InstallEvents();
    $this->InstallFiles();
  }

  function DoUninstall()
  {
    $this->UnInstallEvents();
    $this->UnInstallFiles();
    ModuleManager::unRegisterModule(self::MODULE_ID);
  }

  function InstallEvents()
  {
      return true;
  }

  function UnInstallEvents()
  {
      return true;
  }

  function InstallFiles($arParams = array()) {

    return true;
  }

  function UnInstallFiles(){

    return true;
  }
}