<?php

if(phputilsVerboseLoad()) {
    echo "Loading phputils libraries...";
};

if(phputilsVerboseLoad()) {
    echo "Done loading base phputils libraries...<br>";
}

$includes = [
    "array.php",
    "strings.php",
    "archival.php",
    "strings.php",
    "reflection.php",
    "error-reporting.php",
    "exec.php",
    "download.php",
    "filesystem.php",
    "sessions.php",
    "boolean.php",
    "tables.php",
    "dom.php",
    "csv-utils.php",
    "print.php"
];

requireMultiple($includes, ROOT_PATH . "/libs", phputilsVerboseLoad());
if(phputilsVerboseLoad()) {
    echo "Done loading phputils libraries...<br>";
}
