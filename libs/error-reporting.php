<?php

function errorsOn() {
    println("Error reporting has been turned on");
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

/**
 * echo the tail of the error log
 */
function errorsTail() {
  println("<pre>");
  executeShell("tail error_log");
  println("</pre>");
}

/**
 * echo recent entries in the error log
 * @param int $numberOfEntries
 */
function errorLogRecentEntries($numberOfEntries = 5) {
  $errorLogFilename = 'error_log';

  if(!file_exists($errorLogFilename)) {
    println("Could not find filename: " . $errorLogFilename);
    return;
  }

  try {
    $errorMessages = file_get_contents('error_log');
  } catch(Exception $e) {
      println("Could not open log file. Possible reasons:");
      println("File size: " . fileGetSize($errorLogFilename));
      return;
  }

  $errorMessagesArray = explode("\n", $errorMessages);
  $i = 0;
  foreach($errorMessagesArray as $line) {
      $i++;
      printLn($line);

      if ($i >= $numberOfEntries) {
          break;
      };
  };
};

function registerDefaultErrorHandler($errNo, $errStr, $errFile, $errLine) {
    if (error_reporting() == 0) { // check for @ an ignore
        return;
    }
    $msg = "PHPUTILS ERROR HANDLER: $errStr in $errFile on line $errLine";
    if ($errNo == E_NOTICE || $errNo == E_WARNING) {
        throw new ErrorException($msg, $errNo);
    } else {
        echo $msg;
    }

    set_error_handler('errorHandler');
}

