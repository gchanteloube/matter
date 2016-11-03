<?php

require_once "phar://unicorn.phar/common.php";

$u = new Unicorn($argv);
$u->launch();

// Command line
// php unicorn.php build:app APP_NAME
// php unicorn.php build:view VIEW_NAME