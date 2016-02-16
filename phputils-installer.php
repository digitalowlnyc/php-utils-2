<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function println($output) {
    echo "$output<br>";
}

function assertCommandExists($cmd) {
    $returnVal = shell_exec("which $cmd");
    $exists = strlen($returnVal) != 0;
    if($exists) {
        echo "Command '$cmd' exists, ok";
    } else {
        die("This installer requires access to '$cmd' command\n");
    }
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

function renameDirectory($oldDir, $newDir, $mode = "native") {
    if($mode === "native") {
        $result = rename($oldDir, $newDir);
        if($result === false) {
            println("Could not rename $oldDir to $newDir");
        } else {
            println("Renamed $oldDir to $newDir");
        }
    } else if($mode === "shell") {
        println("Using 'mv' command to move files'");
        executeShell("mv $oldDir $newDir");
        
        $newFileExists = file_exists($newDir);
        
        if($newFileExists === false) {
            println("Error moving file: new file exists?: $newFileExists");
        }
        
    } else {
        throw new Exception("Unsupported rename mode: $mode");
    }
}

function moveOldRelease($releaseDirectory) {
    if(file_exists($releaseDirectory))
    {
        renameDirectory($releaseDirectory, $releaseDirectory . ".old", "shell");
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

    if(file_exists($releaseDirectory))
        moveOldRelease($releaseDirectory);
    renameDirectory($baseFilename, $releaseDirectory, "shell");
}
assertCommandExists("mv"); // sanity check
assertCommandExists("wget");
assertCommandExists("zip");
installRelease("1");
