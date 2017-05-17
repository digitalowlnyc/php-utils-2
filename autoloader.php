<?php
/**
 * Created by PhpStorm.
 * User: Bryan Mayor
 * Date: 2/18/16
 * Time: 2:01 AM
 */
require dirname(__DIR__) . '/php-utils/rootpath.php';
require "php-utils-settings.php";
require "libs/php-includes.php";

if(phputilsVerboseLoad()) {
    echo "Loading from: " . ROOT_PATH . "<br>";
    echo "Loading all...<br>";
}

require_once ROOT_PATH . "/libraries.php";

echo strYesNo(false);