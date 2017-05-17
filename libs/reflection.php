<?php
/**
 * Created by PhpStorm.
 * User: Bryan Mayor
 * Date: 4/30/16
 * Time: 3:03 PM
 */

function getArgsLambda() {
    var_dump(func_num_args());
    var_dump(func_get_args());
}

function getFunctionsDeclared($filename) {
    $arr = get_defined_functions();
    var_dump($arr[$filename]);
}