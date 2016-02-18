<?php

/**
 * Created by PhpStorm.
 * User: Bryan Mayor
 * Date: 2/18/16
 * Time: 1:45 AM
 */
require_once(dirname(__DIR__) . '/phputils/rootpath.php');

require_once("includes.php");

requireMultiple([
    "classes/FileReader.php",
    "classes/FileWriter.php",
], ROOT_PATH, true);
echo "Done loading phputils classes...<br>";