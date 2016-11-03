<?php

$srcRoot = "builder/src";
$buildRoot = "builder/build";

$phar = new Phar("unicorn.phar",
    FilesystemIterator::CURRENT_AS_FILEINFO |     	FilesystemIterator::KEY_AS_FILENAME, "unicorn.phar");
$phar["index.php"] = file_get_contents($srcRoot . "/index.php");
$phar["common.php"] = file_get_contents($srcRoot . "/common.php");
$phar->setStub($phar->createDefaultStub("index.php"));
$phar->buildFromDirectory($srcRoot . '/templates');

copy($srcRoot . "/config.ini", $buildRoot . "/config.ini");