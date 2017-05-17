<?php
/**
 * Creator: Bryan Mayor
 * Company: Blue Nest Digital, LLC
 * License: (All rights reserved)
 */

require_once 'json.php';

function flattenArray(&$arr, $flattenArrayFunction = 'jsonPretty') {
    if(!is_callable($flattenArrayFunction)) {
        throw new Exception('Flatten function is not callable');
    }

    array_walk($arr, function (&$val, $key) use($flattenArrayFunction)
    {
        if(is_array($val)) {
            $val = $flattenArrayFunction($val);
        }
    });

    return $arr;
}

function keyArrayBy($arr, $key) {
    $keyedArr = [];
    foreach($arr as $arrEntry) {
        if(!is_array($arrEntry)) {
            throw new Exception('Expecting array, received: ' . print_r($arr, true));
        }
        if(!isset($arrEntry[$key])) {
            throw new Exception('Expected key ' . $key . ' is not in keys ' . implode(', ', array_keys($arrEntry)));
        }
        $keyVal = $arrEntry[$key];
        $keyedArr['' . $keyVal] = $arrEntry;
    }
    return $keyedArr;
}

function implodeKeyValueArray($keyValArr, $kvSep, $lineSep = ', ') {
    $resultStr = '';
    foreach($keyValArr as $key => $val) {
        if($resultStr !== '') {
            $resultStr .= $lineSep;
        }
        $resultStr .= $key . $kvSep . $val;
    }

    return $resultStr;
}