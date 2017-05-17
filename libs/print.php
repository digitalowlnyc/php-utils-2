<?php 

function varDumpPre($output) {
    echo "<pre>";
    var_dump($output);
    echo "</pre>";
}

function println($output, $lineBreak = "<br>") {
	echo $output . $lineBreak;
}

function printlines($output, $lineBreak = "<br>") {
    foreach($output as $line) {
        println($line, $lineBreak);
    }
}

function getLines($array, $lineBreak = "<br>") {
    return implode($lineBreak, $array);
}

function print_readable($data) {
    print "<pre>";
    print_r($data);
    print "</pre>";
}