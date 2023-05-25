<?php
namespace Starlabs\Project\Properties;

class BooleanProp {
    //описание свойства
    public static function GetUserTypeDescription(){
        return array(
            "PROPERTY_TYPE"		=>"S",
            "USER_TYPE"		=> "StarlabsBoolean",
            "DESCRIPTION"		=>"Да / Нет",
            "GetPropertyFieldHtml"	=>array(__CLASS__, "GetPropertyFieldHtml"),
            "PrepareSettings"	=>array(__CLASS__, "PrepareSettings"),
            "GetSettingsHTML"		=>array(__CLASS__, "GetSettingsHTML"),
            "GetPublicViewHTML"		=>array(__CLASS__, "GetPublicViewHTML"),
            "GetPublicEditHTML"		=>array(__CLASS__, "GetPublicEditHTML"),
        );
    }

    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName){
        $html = '<input type="hidden" name="' . $strHTMLControlName["VALUE"] . '" value="N">';
        $html .= '<input type="checkbox" name="' . $strHTMLControlName["VALUE"] . '" ' . ($value['VALUE'] == 'Y' ? 'checked="checked"' : '') . ' value="Y">';


        return $html;
    }

    public static function GetPublicEditHTML($arProperty, $value, $strHTMLControlName){
        $html = '<input type="hidden" name="' . $strHTMLControlName["VALUE"] . '" value="N">';
        $html .= '<input type="checkbox" name="' . $strHTMLControlName["VALUE"] . '" ' . ($value['VALUE'] == 'Y' ? 'checked="checked"' : '') . ' value="Y">';

        return $html;

    }
    function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
    {
        $html = '';
        $html = $value["VALUE"] == 'Y' ? 'Да' : 'Нет';

        return $html;
    }
}