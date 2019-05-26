<?php

require_once __DIR__.'/../../../vendor/autoload.php';

if (isset($_ENV['HEROKU_APP']) && $_ENV['HEROKU_APP']) {
    require_once __DIR__."/../../config/env.php";
} else {
    require_once __DIR__."/../../config/config.php";
}

require_once __DIR__.'/../utils/Mailer.php';