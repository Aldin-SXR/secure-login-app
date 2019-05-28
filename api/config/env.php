<?php

define('API_KEY', $_ENV['API_KEY']);
define('API_SECRET', $_ENV['API_SECRET']);
define('HIBP_URL', $_ENV['HIBP_URL']);
define('SITE_KEY', $_ENV['SITE_KEY']);
define('CAPTCHA_SECRET', $_ENV['CAPTCHA_SECRET']);
define('JWT_SECRET', $_ENV['JWT_SECRET']);
define('FRONTEND_CLIENT', $_ENV['FRONTEND_CLIENT']);

/** Durations config */
define('JWT_EXPIRY', $_ENV['JWT_EXPIRY']);
define('LOGIN_EXPIRY', $_ENV['LOGIN_EXPIRY']);
define('SMS_EXPIRY', $_ENV['SMS_EXPIRY']);
define('REMEMBER_ME_EXPIRY', $_ENV['REMEMBER_ME_EXPIRY']);
define('RECOVERY_TOKEN_EXPIRY', $_ENV['RECOVERY_TOKEN_EXPIRY']);

/** Database config */
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);
define('DB_CHARSET', $_ENV['DB_CHARSET']);

/** Mail configuration */
define('SMTP_SERVER', $_ENV['SMTP_SERVER']);
define('SMTP_PORT', $_ENV['SMTP_PORT']);
define('SMTP_USER', $_ENV['SMTP_USER']);
define('SMTP_PASS', $_ENV['SMTP_PASS']);

/** YubiKey configuration */
define('YUBIKEY_CLIENT_ID', $_ENV['YUBIKEY_CLIENT_ID']);
define('YUBIKEY_SECRET', $_ENV['YUBIKEY_SECRET']);