<?php 

function printSession() {
    session_start();
    echo "Session contents:<br>";
    foreach($_SESSION as $key=>$val)
    {
    	echo "$key: $val<br>";
    }
}


function phpClearSession($sessionName = null) {
  // Source: http://php.net/manual/en/function.session-destroy.php
  
  if($sessionName !== null)
       session_name($sessionName);
  
  // Initialize the session.
  // If you are using session_name("something"), don't forget it now!
  session_start();
  
  // Unset all of the session variables.
  $_SESSION = array();
  
  // If it's desired to kill the session, also delete the session cookie.
  // Note: This will destroy the session, and not just the session data!
  if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000,
          $params["path"], $params["domain"],
          $params["secure"], $params["httponly"]
      );
  }
  
  // Finally, destroy the session.
  session_destroy();
  println("Cleared session");
}
