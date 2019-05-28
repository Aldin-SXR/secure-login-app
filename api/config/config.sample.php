<?php

define("API_KEY", "");
define("API_SECRET", "");
define("HIBP_URL", "https://api.pwnedpasswords.com/range/");
define('SITE_KEY', '');
define('CAPTCHA_SECRET', '');
define('JWT_SECRET', '');

/** Durations config */
define('JWT_EXPIRY', '+60 minutes');
define('LOGIN_EXPIRY', '+45 seconds');
define('SMS_EXPIRY', '+30 seconds');
define('REMEMBER_ME_EXPIRY', '+2 minutes');
define('RECOVERY_TOKEN_EXPIRY', '+5 minutes');
define('FRONTEND_CLIENT', '');

/** Database config */
define('DB_HOST', '');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/** Mail configuration */
define('SMTP_SERVER', '');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');

/** YubiKey configuration */
define('YUBIKEY_CLIENT_ID', 0);
define('YUBIKEY_SECRET', '');