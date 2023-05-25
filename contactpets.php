<?php
use Bitrix\Crm\ContactTable;
use Bitrix\Main\UserPhoneAuthTable;
use Starlabs\Project\Grooming;
use Starlabs\Project\SmartProcess;
use Starlabs\Project\Iblock;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

//$newContactPets = [
//    20431,
//    16260,
//    20520
//];
//
//if (!empty($newContactPets)) {
//    $Contact = ContactTable::getById(5125 );
//    $Contact->fetchObject()->set(Grooming\Contact::FIELD_PET_CODE, $newContactPets)->save();
//}

$newDealPet = 20915;
if ($newDealPet) {
    $Deal = \Bitrix\Crm\DealTable::getById(764);
    $Deal->fetchObject()->set(Grooming\Deal::FIELD_PET_CODE, $newDealPet)->save();
}


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>