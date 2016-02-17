<?php
/**
 * Created by PhpStorm.
 * User: Bryan Mayor
 * Date: 2/18/16
 * Time: 1:46 AM
 */

function requireMultiple($includes, $debug = false)
{
    foreach ($includes as $include) {
        if ($debug)
            echo "Including '$include'...";

        require_once($include);

        if ($debug)
            echo "... included '$include'<br>";
    }
}
