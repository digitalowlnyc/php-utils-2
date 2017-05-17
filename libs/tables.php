<?php

/**
 * Prints a table (array of maps)
 * @param $table
 */
function makeHtmlTable($table,
                       $options = array(
                           "pad-row-length" => false,
                           "cell-callback" => null
                       )
) {
    $output = "<table border='1'>";
    $rowCount = 0;

    $shouldPadRowLength = isset($options["pad-row-length"]) ? $options["pad-row-length"] : false;
    if($shouldPadRowLength) {
        $maxRowLength = maxRowLength($table);
    } else {
        $maxRowLength = null;
    }

    $cellCallback = isset($options["cell-callback"]) ? $options["cell-callback"] : null;

    foreach($table as $row) {
        $rowCount++;

        $tag = ($rowCount === 1) ? "th" : "td";

        if($shouldPadRowLength && count($row) < $maxRowLength) {
            $row = array_pad($row, $maxRowLength, "");
        }

        $output .= "<tr>";
        foreach($row as $colValue) {
            if($cellCallback !== null && is_callable($cellCallback)) {
                $colValue = $cellCallback($colValue);
            }

            $output .= "<$tag>$colValue</$tag>";
        }
        $output .= "</tr>";
    }

    return $output;
}

function printTable($table, $options = array()) {
    echo makeHtmlTable($table, $options);
}

function getSizeTableNestedRow($row) {
    if(gettype($row) === "array") {
        return count($row);
    } else {
        return count(array_keys($row));
    }
}

function maxRowLength($table) {
    $max = 0;
    array_walk($table, function($nestedRow) use (&$max) {
        $max = max($max, getSizeTableNestedRow($nestedRow));
    });
    return $max;
}