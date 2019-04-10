<?php

/* Require constants and libraries */
require_once __DIR__."/vendor/autoload.php";
require_once __DIR__."/config/Config.php";

/* Require files */
foreach (glob(__DIR__."/app/utils/*.php") as $util) {
    require_once $util;
}

echo OTPGenerator::get_otp()."\n";
$link = OTPGenerator::get_provisioning_link();

OTPGenerator::get_qr_code($link);