<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
//$APPLICATION->SetPageProperty("BodyClass", "page-one-column");
$APPLICATION->SetTitle("График работы мастеров");

$APPLICATION->IncludeComponent(
    "starlabs:workschedule",
    "",
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");