<?php

namespace Starlabs\Project\SmartProcess;

use Starlabs\Project\Iblock\AnimalTypes;
use Starlabs\Project\Iblock\Breed;

class Pets extends Prototype
{
    private $UfBreedCode;
    private $UfPetType;
    private $UfBirthdayCode;
    private $UfCashbackCode;
    const SMART_PROCESS_NAME = "Pitomtsy";
    const BREED_FIELD_CODE = "BREED";
    const PET_TYPE_FIELD_CODE = "PET_TYPE";
    const BIRTHDAY_FIELD_CODE = "BIRTHDAY";
    const CASHBACK_FIELD_CODE = "CASHBACK";

    public function getUfBreedCode():string
    {
        if (!$this->UfBreedCode) {
            $this->UfBreedCode = $this->getFullFieldCode(self::BREED_FIELD_CODE);
        }
        return $this->UfBreedCode;
    }
    public function getUfPetTypeCode():string
    {
        if (!$this->UfPetType) {
            $this->UfPetType = $this->getFullFieldCode(self::PET_TYPE_FIELD_CODE);
        }
        return $this->UfPetType;
    }
    public function getUfBirthdayCode():string
    {
        if (!$this->UfBirthdayCode) {
            $this->UfBirthdayCode = $this->getFullFieldCode(self::BIRTHDAY_FIELD_CODE);
        }
        return $this->UfBirthdayCode;
    }
    public function getUfCashbackCode():string
    {
        if (!$this->UfCashbackCode) {
            $this->UfCashbackCode = $this->getFullFieldCode(self::CASHBACK_FIELD_CODE);
        }
        return $this->UfCashbackCode;
    }

    protected function getSmartProcessName(): string
    {
        return self::SMART_PROCESS_NAME;
    }

    protected function getPropertiesCode(): array
    {
        return [
            $this->getUfPetTypeCode(),
            $this->getUfBreedCode(),
            $this->getUfBirthdayCode()
        ];
    }

    /**
     * @param $petId
     * @return bool
     */
    public function isBigPet($petId)
    {
        $pet = $this->getFactory()->getItem($petId);
        $breedId = $pet->get($this->getUfBreedCode());
        $Breed = new Breed();
        return $Breed->isBig($breedId);
    }

    public function isCat($petId)
    {
        $pet = $this->getFactory()->getItem($petId);
        $petTypeId = $pet->get($this->getUfPetTypeCode());
        $Types = new AnimalTypes();
        return $Types->isCat($petTypeId);
    }
}