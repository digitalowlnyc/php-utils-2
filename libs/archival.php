<?php
/**
 * Creator: Bryan Mayor
 * Company: Blue Nest Digital, LLC
 * License: (All rights reserved)
 */

function archiveThisFile($fileName, $maxNumberOfBackups = 5) {

    $endsWith = endsWith($fileName, '/');

    if($endsWith !== false) {
        $fileName = substr($fileName, 0, $endsWith);
    }

    $index = 0;
    while(true) {
        $index += 1;
        if($index > $maxNumberOfBackups) {
            throw new Exception('Could not archive file, please remove some old copies: ' . $fileName);
        }

        $archiveFileName = $fileName . '.' . $index;
        if(file_exists($archiveFileName)) {
            continue;
        } else {
            if(!rename($fileName, $archiveFileName)) {
                throw new Exception('Could not rename file from ' . $fileName . ' to ' . $archiveFileName);
            }
            return;
        }
    }
}