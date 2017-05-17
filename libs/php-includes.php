<?php

function requireMultiple($includes, $directory, $verbose = false)
{
    $loadedSuccessfully =  [];
    foreach($includes as $include) {
        $fileLocation = $directory . "/" . $include;

        if($verbose) {
            echo "Including '$fileLocation'...";
        }

        if(!file_exists($fileLocation)) {
            throw new Exception("requireMultiple(): Library file does not exist: '$include'");
        }

        if(!require_once($fileLocation)) {
            echo "Loaded:" . PHP_EOL;
            echo implode(PHP_EOL, $loadedSuccessfully);
            throw new Exception("Could not include/require: [" . $include . "]");
        }

        if($verbose) {
            echo "... done<br>";
        }

        $loadedSuccessfully[] = $include;
    }
}

function getRequiredFilesForPhpFile($file) {
    $contents = file_get_contents($file);
    if($contents === False) {
        throw new Exception("Could not open file $file");
    }
    $tokens = token_get_all($contents);
    $lines = explode("\n", $contents);

    $scripts = [];
    foreach ($tokens as $token) {
        if (is_array($token)) {
            if($token[0] === T_REQUIRE) {
                //echo token_name($token[0]) . "<br>";
                $lineNumber = $token[2];

                $thisLine = $lines[$lineNumber - 1];

                $matches = [];
                preg_match("~require (.+);~", $thisLine, $matches);
                echo $thisLine;
                $script = $matches[1];
                $script = str_replace("'", "", $script);
                $script = str_replace('"', "", $script);
                $scripts[] = $script;
            }
        }
    }

    return $scripts;
}

function printFileRelationships($phpFile) {

    $requiredScripts = getRequiredFilesForPhpFile($phpFile);
    foreach($requiredScripts as $scriptName) {
        if(!file_exists($scriptName)) {
            echo "Missing file: " . $phpFile . PHP_EOL;
            continue;
        }
        echo "->$scriptName";
        printFileRelationships($scriptName);
    }
}