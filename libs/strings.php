<?php
/**
 * Created by PhpStorm.
 * User: Bryan Mayor
 * Date: 4/30/16
 * Time: 7:58 PM
 */

function trimSlashes($string) {
    return trimCharacters($string, ["/", "\\"]);
}

/**
 * NB: Doesn't currently handle combination of chars at the beginning due to order
 * that substr is applied.
 * @param $string
 * @param $charArray
 */
function trimCharacters($string, $charArray) {
    foreach($charArray as $char) {
        if(strpos($string, $char) === 0) {
            $string = substr($string, 1);
        }
        if(strpos($string, $char) === (strlen($string) - 1)) {
            $string = substr($string, 0, strlen($string) - 2);
        }
    }
    return $string;
}

function surround($str, $ends = '"') {
    return $ends . $str . $ends;
}

function endsWith($haystack, $needle) {
    $lastOccurance = strrpos($haystack, $needle);
    if($lastOccurance === (strlen($haystack) - strlen($needle))) {
        return $lastOccurance;
    } else {
        return false;
    }
}

function isEmptyString($str) {
    $str = str_replace(' ', '', $str);
    return $str === '';
}

function isNullString($str) {
    $str = str_replace(' ', '', $str);
    return strtolower($str) === 'null';
}

function isNullLike($val) {
    if($val === null) {
        return true;
    }

    if(!is_string($val)) {
        throw new Exception('isNullLike: Expecting string value, found: ' . describeVar($val));
    }

    if(isNullString($val)) {
        return true;
    }

    return false;
}

/**
 * Trim whitespaces and turn null-like strings to empty string
 * @param $str
 * @return string
 */
function normalizedString($str) {
    $str = trim($str);
    return stringOrEmpty($str);
}

/**
 * Turn null-like strings or all-whitespace string to empty string
 *
 * @param $str
 * @return string
 * @throws Exception
 */
function stringOrEmpty($str) {
    if(isNullLike($str) || isEmptyString($str)) {
        return '';
    }

    return $str;
}