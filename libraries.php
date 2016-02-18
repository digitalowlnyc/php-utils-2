<?php
$_phputils_settings = [
    "do_debug_load" => false
];

require_once(dirname(__DIR__) . '/php-utils/rootpath.php');

include_once("includes.php");

if($_phputils_settings["do_debug_load"]) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    echo "Loading phputils libraries...";
};
$includes = [
    "print.php",
    "errors.php",
    "exec.php",
    "download.php",
    "filesystem.php",
    "sessions.php",
    "boolean.php",
];

requireMultiple($includes, ROOT_PATH, $_phputils_settings["do_debug_load"]);
println("Done loading phputils libraries...<br>");
