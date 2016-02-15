<?php 

function download($url, $destinationFile, $mode = "wget") {
switch($mode) {
case "wget":
shellExecute("wget $url -O  $destinationFile");
break;
default:
throw new Exception("Unknown mode"):
}
}
