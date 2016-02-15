<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function println($output) {
    echo "$output<br>";
}

function printDump($output) {
    echo "<pre>";
    var_dump($output);
    echo "</pre>";
}

function execReturnValueToText($intValue) {
    $text = null;
    switch($intValue) {
        case 0:
            $text = "Success";
            break;
        case 127:
            $text = "Command not found";
            break;
        default:
            $text = "Unknown return code: " . $intValue;
    }
    return $text;
}
function executeShells($commands) {
    $allCommands = implode("&&", $commands);
    executeShell($allCommands);
}
function executeShell($command) {
    $output = [];
    $returnVar = null;
    println("PHP is going to execute: " . $command);
    exec($command, $output, $returnVar);
    println("Output is:");
    println(implode("<br>", $output));
    println("Return value is: ");
    printDump(execReturnValueToText($returnVar));
}

function renameDirectory($oldDir, $newDir) {
    $result = rename($oldDir, $newDir);

    if($result === false) {
        println("Could not Rename $oldDir to $newDir");
    } else {
        println("Renamed $oldDir to $newDir");
    }
}

function moveOldRelease($releaseDirectory) {
    if(file_exists($releaseDirectory))
    {
        renameDirectory($releaseDirectory, $releaseDirectory . ".old");
    }
}

function installRelease($release) {

    $baseFilename = "php-utils-$release";
    $filename = $baseFilename . ".zip";

    if(file_exists($filename))
        moveOldRelease($filename);

    $url = "https://github.com/heliosbryan/php-utils/archive/$release.zip";
    $releaseDirectory = "phputils";

    executeShell("wget $url -O $filename");

    if(!file_exists($filename))
        die("File was not downloaded!");

    moveOldRelease($releaseDirectory);

    executeShell("unzip $filename");

    renameDirectory($baseFilename, $releaseDirectory);
}

installRelease("1");
