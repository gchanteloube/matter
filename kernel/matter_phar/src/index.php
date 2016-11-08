<?php

require_once "phar://matter.phar/common.php";

$u = new Matter($argv);
$u->launch();

// Command line
// php unicorn.php build:app APP_NAME
// php unicorn.php build:view VIEW_NAME