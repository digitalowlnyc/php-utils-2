<?php 

/**
 * Move/rename file or directory. 
 * 
 * NB: 'native' mode doesn't seem to work with non-empty directories.
 * Use 'shell' mode instead for this case.
 * 
 * @param $oldDir
 * @param $newDir
 * @param string $mode Native or using shell command
 * @throws Exception
 */
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

// Credit: http://php.net/manual/en/function.filesize.php#106569
function bytesToString($byteCount, $decimals = 2) {
    $sz = 'BKMGTP';
    $factor = floor((strlen($byteCount) - 1) / 3);
    return sprintf("%.{$decimals}f", $byteCount / pow(1024, $factor)) . @$sz[$factor];
}

function fileGetSize($filename, $abbreviate = true) {
    if($filename === null || strlen($filename) === 0)
        throw new Exception("Invalid input for fileGetSize: " . $filename);
    $sizeInBytes = filesize($filename);

    if($abbreviate)
        $size = bytesToString($sizeInBytes);
    else
        $size = (String)$sizeInBytes;

    return $size;

}