<?php
/**
 * Creator: Bryan Mayor
 * Company: Blue Nest Digital, LLC
 * License: (All rights reserved)
 */

function getTypeOrClass($v) {
    $type = gettype($v);
    if($type === 'object') {
        $type = get_class($v);
    }
    return $type;
}

function describeVar($var, $ends = "'") {
    if($var === null) {
        $val = '<null>';
    } else {
        $val = print_r($var, true);
    }

    return surround($val, $ends);
}