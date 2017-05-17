<?php
/**
 * Created by PhpStorm.
 * User: Bryan Mayor
 * Date: 6/29/16
 * Time: 5:29 PM
 */

function jsonPretty($val) {
    return json_encode($val, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}

/**
 * A table is a map of maps. The first map
 * has the keys for each entry. The second has
 * the row values.
 * @param $json
 */
function printJsonTable($json) {
    echo "<table border='1'>";

    reset($json);
    $firstKey = key($json);
    $firstRow = $json[$firstKey];

    $headers = array_keys($firstRow);
    array_unshift($headers, "key");

    printRow("th", $headers);

    $sumRow = rowSums($json);

    $rowCount = 0;
    foreach($json as $key=>$rowValues) {
        $rowCount += 1;
        array_unshift($rowValues, $key);
        printRow("td", $rowValues);
    }

    array_unshift($sumRow, $rowCount); // Key column
    printRow("td", $sumRow);

    echo "</table>";
}

function rowSums($jsonTable) {
    $totals = [];

    $rowCount = 0;
    foreach($jsonTable as $key=>$rowValues) {
        $rowCount++;

        foreach($rowValues as $colName=>$colVal) {
            if(!is_numeric($colVal)) {
                $totals[$colName] = "n/a";
                continue;
            }

            if($rowCount == 1) {
                $totals[$colName] = $colVal;
            } else {
                $totals[$colName] += $colVal;
            }
        }
    }

    return $totals;
}

function printRow($tag, $rowValues) {
    echo "<tr>";
    foreach($rowValues as $val) {
        echo "<$tag>$val</$tag>";
    }
    echo "</tr>";
}