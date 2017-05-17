<?php 

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
