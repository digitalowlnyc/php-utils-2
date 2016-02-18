<?php
/**
 * Created by PhpStorm.
 * User: Bryan Mayor
 * Date: 2/18/16
 * Time: 2:01 AM
 */

require_once(dirname(__DIR__) . '/phputils/rootpath.php');

echo "Loading from: " . ROOT_PATH . "<br>";
echo "Loading all...<br>";
require_once(ROOT_PATH . "/libraries.php");
require_once(ROOT_PATH . "/classes.php");
echo "... done loading all<br>";
