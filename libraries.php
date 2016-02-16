<?php
$_phputils_settings = [
    "do_debug_load" => false
];

if($_phputils_settings["do_debug_load"]) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    echo "Including phputils...";
};
$includes = [
    "print.php",
    "errors.php",
    "exec.php",
    "download.php",
    "filesystem.php",
    "sessions.php",
];

foreach($includes as $include)
{
    if($_phputils_settings["do_debug_load"])
        echo "Including '$include'...";
    require_once($include);

    if($_phputils_settings["do_debug_load"])
        echo "... included '$include'<br>";
}

println("Done loading phputils...");
