<?php

namespace Starlabs\Project\SmartProcess;

class MasterSkills extends Prototype
{
    private $UfMasterCode;
    private $UfDurationCode;
    private $UfServiceCode;
    private $UfCommentCode;
    const SMART_PROCESS_NAME = "Kompetentsiimasterov";
    const DURATION_FIELD_CODE = "DURATION";
    const MASTER_FIELD_CODE = "MASTER";
    const SERVICE_FIELD_CODE = "SERVICE";
    const COMMENT_FIELD_CODE = "COMMENT";

    public function getUfDurationCode()
    {
        if (!$this->UfDurationCode) {
            $this->UfDurationCode = $this->getFullFieldCode(self::DURATION_FIELD_CODE);
        }
        return $this->UfDurationCode;
    }

    public function getDurationFromMaskString(string $maskDuration):int
    {
        $arDuration = explode(':', $maskDuration);
        $hours = $arDuration[0];
        $minutes = $arDuration[1];
        return $hours * 60 + $minutes;
    }
    public function getUfMasterCode()
    {
        if (!$this->UfMasterCode) {
            $this->UfMasterCode = $this->getFullFieldCode(self::MASTER_FIELD_CODE);
        }
        return $this->UfMasterCode;
    }
    public function getUfCommentCode()
    {
        if (!$this->UfCommentCode) {
            $this->UfCommentCode = $this->getFullFieldCode(self::COMMENT_FIELD_CODE);
        }
        return $this->UfCommentCode;
    }
    public function getUfServiceCode()
    {
        if (!$this->UfServiceCode) {
            $this->UfServiceCode = $this->getFullFieldCode(self::SERVICE_FIELD_CODE);
        }
        return $this->UfServiceCode;
    }

    protected function getSmartProcessName(): string
    {
        return self::SMART_PROCESS_NAME;
    }

    protected function getPropertiesCode(): array
    {
        return [
            $this->getUfDurationCode(),
            $this->getUfMasterCode(),
            $this->getUfServiceCode(),
        ];
    }
}