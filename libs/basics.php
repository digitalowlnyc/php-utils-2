<?php

function valueOrDefault($array, $key, $default = null) {
    if($key === null) {
        throw new Exception("Key must be a string, not null");
    }
    if(!isset($array[$key])) {
        return $default;
    }
    return $array[$key];
}

function valueOrThrow($array, $key) {
    if($key === null) {
        throw new Exception("Key must be a string, not null");
    }
    if(!isset($array[$key])) {
        throw new Exception("Missing key: " . $key);
    }
    return $array[$key];
}