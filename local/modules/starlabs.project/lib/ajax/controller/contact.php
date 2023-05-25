<?php
namespace Starlabs\Project\Ajax\Controller;

use Bitrix\Crm\ContactTable;
use Bitrix\Main\Loader;
use Starlabs\Tools\Ajax\Controller\Prototype;
use Starlabs\Tools\Helpers\Utils;
use Bitrix\Iblock\ElementTable;
use Starlabs\Project\SmartProcess\Pets;
use Bitrix\Crm\FieldMultiTable;
use Starlabs\Project\Grooming\Contact as StarlabsContact;

class Contact extends Prototype
{
	public function getContactsByPhoneAction()
    {
        $this->returnAsIs = true;
        $arRequest = Utils::getRequestBodyContent();
        $phone = $arRequest["phone"];
        $arResult = [];
        if ($phone) {
            Loader::includeModule('crm');
            $fieldEntity = FieldMultiTable::getEntity();
            $Query = ContactTable::query()
                ->setSelect(["ID", "NAME", "CONTACT_PHONE" => "FIELD.VALUE"])
                ->setFilter(["FIELD.ENTITY_ID" => "CONTACT", "FIELD.TYPE_ID" => "PHONE",])
                ->registerRuntimeField(
                    'FIELD',
                    [
                        'data_type' => $fieldEntity,
                        'reference' => ['=this.ID' => 'ref.ELEMENT_ID']
                    ]
                )
                ->whereLike("FIELD.VALUE", "%$phone%")
                ->setLimit(50)
                ->exec();

            while ($arContact = $Query->fetch()) {
                $arResult[$arContact["ID"]] = [
                    "phone" => $arContact["CONTACT_PHONE"],
                    "id" => $arContact["ID"],
                    "name" => $arContact["NAME"]
                ];
            }
        }
        return array_values($arResult);
    }

    public function getContactPetsAction()
    {
        $this->returnAsIs = true;
        $arRequest = \Starlabs\Tools\Helpers\Utils::getRequestBodyContent();
        Loader::includeModule('crm');
        $contactId = $arRequest["id"];
        $Query = ContactTable::query()
            ->setSelect([StarlabsContact::FIELD_PET_CODE])
            ->setFilter(["ID" => $contactId])
            ->exec();
        $arResult = [];
        if ($arContact = $Query->fetch()) {
            $Pets = new Pets();
            $PetsQuery = $Pets->createQuery()
                ->setFilter(["ID" => $arContact[StarlabsContact::FIELD_PET_CODE]])
                ->setSelect(["ID", "TITLE", $Pets->getUfBreedCode(), "BREED_NAME" => "BREED.NAME", "ANIMAL_TYPE_NAME" => "ANIMAL_TYPE.NAME", $Pets->getUfPetTypeCode()])
                ->registerRuntimeField(
                    'BREED',
                    [
                        'data_type' => ElementTable::getEntity(),
                        'reference' => ['=this.' . $Pets->getUfBreedCode() => 'ref.ID']
                    ]
                )
                ->registerRuntimeField(
                    'ANIMAL_TYPE',
                    [
                        'data_type' => ElementTable::getEntity(),
                        'reference' => ['=this.' . $Pets->getUfPetTypeCode() => 'ref.ID']
                    ]
                )
                ->exec();
            while ($arPet = $PetsQuery->fetch()) {
                $arResult[] = [
                    "id" => $arPet["ID"],
                    "name" => $arPet["TITLE"],
                    "breed" => $arPet["BREED_NAME"],
                    "breedid" => $arPet[$Pets->getUfBreedCode()],
                    "animaltype" => $arPet["ANIMAL_TYPE_NAME"],
                    "animaltypeid" => $arPet[$Pets->getUfPetTypeCode()]
                ];
            }
        }
        return $arResult;
    }
}