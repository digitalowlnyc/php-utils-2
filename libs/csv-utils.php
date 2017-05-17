<?php

/**
 * Creates a "Table" data structure
 *
 * @param $filename
 * @param bool|false $includeHeaders
 * @return array
 */

/**
 * Reference: http://php.net/manual/en/function.fgetcsv.php
 *
 *
 * @param $filename
 * @param int $maxLineLength - Must be greater than the longest line (in characters) to be
 * found in the CSV file (allowing for trailing line-end characters). It became optional
 * in PHP 5. Omitting this parameter (or setting it to 0 in PHP 5.1.0 and later)
 * the maximum line length is not limited, which is slightly slower.
 *
 * @return array
 */
function csvFileToArrayOfArray($filename, $maxLineLength = 0) {
    $rowsData = [];
    $rowCount = 1;

    if (($handle = fopen($filename, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, $maxLineLength, ",")) !== FALSE) {
            $num = count($data);

            $thisRowData = [];
            for ($c = 0; $c < $num; $c++) {
                $cellValue = $data[$c];
                array_push($thisRowData, $cellValue);
            }

            array_push($rowsData, $thisRowData);
            $rowCount++;
        }
        fclose($handle);
    }

    return [
        "data" => $rowsData,
        "rowCount" => $rowCount,
        "colCount" => $c
    ];

}

function csvFileToTable($filename, $includeHeaders = false) {
    $rowsData = [];
    $headers = [];
    $row = 1;

    if (($handle = fopen($filename, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $num = count($data);

            $thisRowData = [];
            for ($c = 0; $c < $num; $c++) {
                $cellValue = $data[$c];
                array_push($thisRowData, $cellValue);
            }

            if ($row === 1) {
                $headers = $thisRowData;
            } else {
                array_push($rowsData, $thisRowData);
            }
            $row++;
        }
        fclose($handle);
    }

    $allMappedData = [];
    if($includeHeaders)
        array_push($allMappedData, $headers);

    foreach ($rowsData as $row) {
        $mappedData = [];

        $currentCol = 0;
        foreach ($headers as $column) {
            $mappedData[$column] = $row[$currentCol];
            $currentCol++;
        }
        array_push($allMappedData, $mappedData);
    }

    return $allMappedData;
}

/**
 * Parse a CSV string to an array of array.
 * Column names are in first entries of array.
 *
 * @param $csvString
 * @return array
 */
function csvParseFromString($csvString) {
    $csv = trim($csvString);

    $parsedRows = str_getcsv($csv, "\n");
    $parsedCsv = [];
    foreach($parsedRows as $row) {
        if(trim($row) === "") {
            continue;
        }

        $parsedRow = str_getcsv($row);
        array_push($parsedCsv, $parsedRow);
    }
    return $parsedCsv;
}