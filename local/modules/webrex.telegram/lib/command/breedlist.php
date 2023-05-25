<?php

namespace Webrex\Telegram\Command;

use Webrex\Telegram\Bot\Stage;
use Webrex\Telegram\Bot\StageButton;
use Webrex\Telegram\Helpers\Log;

class BreedList extends Command
{
    protected function actionsBeforeProcess()
    {
        return;
    }
    protected function actionsAfterProcess()
    {
        return;
    }

    protected function process()
    {
        $arButtons = $this->prepareButtons(false);
        $this->chat->sendMessage('Породы и стрижки, по которым ты можешь обучаться: йоркширский терьер (классика и азиатский стиль), шпиц, ши-тцу, пудель (шоу скандинавский лев, пэт стрижка модерн и азиатский стиль), бишон фризе (шоу и пэт), мальтипу, мальтезе, бивер терьер, пекинес', $arButtons);
    }

    protected function getCurrentStageCode():string
    {
        return '';
    }

    protected function getCommandButtons()
    {
        return StageButton::getStageButtons($this->chat->getChatEntityObject()->getStageId());
    }

    protected function getNextStageCode(): string
    {
        return '';
    }
    
}