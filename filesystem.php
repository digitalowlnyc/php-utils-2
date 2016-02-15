<?php 

function renameDirectory($oldDir, $newDir) {
    $result = rename($oldDir, $newDir);

    if($result === false) {
        println("Could not rename $oldDir to $newDir");
    } else {
        println("Renamed $oldDir to $newDir");
    }
}
