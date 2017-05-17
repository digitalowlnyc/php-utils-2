<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function println($output) {
    echo "$output<br>";
}

function assertCommandExists($cmd, $verbose = false) {
    $returnVal = shell_exec("which $cmd");
    $exists = strlen($returnVal) != 0;
    if($exists) {
        if($verbose) {
            echo "Command '$cmd' exists, ok";
        }
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
        case 1:
            $text = "Failure";
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

function renameDirectory($oldDir, $newDir, $moveMode = "php") {
    if($moveMode === "php") {
        $result = rename($oldDir, $newDir);
        if($result === false) {
            println("Could not rename $oldDir to $newDir");
        } else {
            println("Renamed $oldDir to $newDir");
        }
    } else if($moveMode === "shell") {
        println("Using 'mv' command to move files'");
        executeShell("mv $oldDir $newDir");
        
        $newFileExists = file_exists($newDir);
        
        if($newFileExists === false) {
            println("Error moving file: new file exists?: $newFileExists");
        }
        
    } else {
        throw new Exception("Unsupported rename mode: $moveMode");
    }
}

// Credit: http://php.net/manual/en/function.rmdir.php#110489
function delTree($dir) {
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

function archiveReleases($releaseDirectory) {
    echo "Archiving...<br>";
    $archivedReleaseDirectory = $releaseDirectory . ".old";
    if(file_exists($archivedReleaseDirectory))
    {
        if(is_dir($archivedReleaseDirectory))
            delTree($archivedReleaseDirectory);
        else
            unlink($archivedReleaseDirectory);
    }
    if(file_exists($releaseDirectory)) {
        renameDirectory($releaseDirectory, $archivedReleaseDirectory, "shell");
    }
}

function getDirectoryNames($libName, $release) {
    return array(
        "release-dir" => "",
        "zip-dir" => "",
        "zip-file" => "",
        "lib-dir" => ""
    );
}

function installRelease($libName, $release, $gitAccount, $directory = ".") {
    // e.g. "/my_install_dir"
    chdir($directory);

    // e.g. "/my_install_dir/php-utils_master"
    $releaseVersionedFilename = $libName . "_" . $release;
    $releaseZipDirectory = "releases";

    // e.g. "/my_install_dir/php-utils_master.zip"
    $releaseVersionedZipFilename = $releaseVersionedFilename . ".zip";

    // Archive previous release zip file
    archiveReleases($releaseVersionedZipFilename);

    $url = "https://github.com/" . $gitAccount . "/" . $libName . "/archive/$release.zip";

    $releaseDirectory = $libName;

    $releaseZipStoragePath = $releaseZipDirectory . "/" . $releaseVersionedZipFilename;
    executeShell("wget $url -O $releaseZipStoragePath");

    if(!file_exists($releaseZipStoragePath))
        die("File was not downloaded properly!");

    // Archive the current release's directory
    archiveReleases($releaseDirectory);

    executeShell("unzip $releaseZipStoragePath -d unzipped");

    // e.g. "/my_install_dir/unzipped"
    renameDirectory("unzipped", $releaseDirectory, "shell");
    //renameDirectory($releaseVersionedFilename, $releaseDirectory, "shell");
}

foreach(array("mv", "wget", "zip") as $command) {
    assertCommandExists($command); // sanity check
}

$directory = "testinstalls";
installRelease("php-utils", "master", "heliosbryan", $directory);