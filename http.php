<?php

/**
 * Remember that this is not sanitized and should be commented out of your code when done using
 */
function debugRequest() {
    echo "Echoing for debug:<br>";
    echo "You should see 'DONE' line at the end if all is successful<br>";

    function show($arr, $label = "") {
        echo  "$label<br>";
        echo "<pre>";
        var_dump($arr);
        echo "</pre>";
        echo "<br>";
    }

// === Section 1
    show($_GET, "GET");
    show($_POST, "POST");
    show($_COOKIE, "COOKIE");
    show($_SERVER, "SERVER");
    show(getallheaders(), "Request headers");
    show(apache_response_headers(), "Response headers: apache_response_headers (array)");

// Section 1 ===

    if(isset($_COOKIE["thetestcookie"]))
        echo "thetestcookie:" . $_COOKIE["thetestcookie"] . "<br>";
    else
        echo "thetestcookie: cookie is not set<br>";


    echo "<br><br>Usage notes:<br>";
    $readme = "";
    $readme .= "- You can set a cookie called 'thetestcookie'";
    echo $readme;

    echo "<br>DONE<br>";
}
