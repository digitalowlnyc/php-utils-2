<?php

set_include_path("../");
include "../boolean.php";

// Usage: new HttpBasicAuthenticator($username, $pass)->handle();
class HttpBasicAuthenticator
{
    private $realm = "Auth";

    private $credentials = [
        //"username" => "password"
    ];
    private $ipAuthenticationList = [
        //"192.168.0.1" => true,
    ];
    private $blockUnlistedIps = false;
    const MAX_ATTEMPTS = 3;
    const BACKOFF_DURATION_SECONDS = 120;
    const REAUTHENTICATE_AFTER_MINUTES = 20;
    public function __construct($username, $password)
    {
        if($username && $password)
            $this->credentials[$username] = $password;
    }
    private function nextLoginAt($time) {
        return $time + (60 * static::REAUTHENTICATE_AFTER_MINUTES);
    }
    private function nextRetryAt($time) {
        return $time + (static::BACKOFF_DURATION_SECONDS);
    }
    private function checkCredentials($user, $password) {
        return (array_key_exists($user, $this->credentials) && $this->credentials[$user] === $password);
    }
    private function checkIpBasedCredentials($ip) {
        if(!array_key_exists($ip, $this->ipAuthenticationList))
        {
            if($this->blockUnlistedIps) {
                return false;
            } else {
                return null;
            }
        }
        return $this->ipAuthenticationList[$ip] === true;
    }

    public function getIpAddressData() {
        $usingCloudflare = false;
        if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $usingCloudflare = true;
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];
        }

        return ["ip" => $ip, "usingCloudflare" => $usingCloudflare];
    }

    public function handle()
    {
        session_start();
        if(!isset($_SESSION["count"]))
            $_SESSION["count"] = 0;

        $ipAddressData = $this->getIpAddressData();
        $ip = $ipAddressData["ip"];

        $ipAuthenticated = $this->checkIpBasedCredentials($ip);
        if($ipAuthenticated !== null) {
            if($ipAuthenticated == false) {
                die("You are not authorized: code 10001. Cloudflare: " . strYesNo($ipAddressData["usingCloudflare"]));
            }
            $authenticated = true;
        } else if(isset($_SERVER["PHP_AUTH_USER"])) {
            $user = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];
            $authenticated = $this->checkCredentials($user, $password);
        } else {
            $authenticated = false;
        }
        $now = time();
        if($authenticated) {
            $wasLoggedIn = isset($_SESSION["last_login"]);
            if(!$wasLoggedIn) {
                $_SESSION['last_login'] = time();
                $timeToReauthenticate = false;
            } else {
                $timeToReauthenticate = $this->nextLoginAt($_SESSION['last_login']) < $now;
            }
            if(!$timeToReauthenticate) {
                return true;
            } else {
                $this->resetLoginAttempt();
            }
        } else {
            if ($_SESSION["count"] >= static::MAX_ATTEMPTS) {
                $NEXT_RETRY_TIME = $this->nextRetryAt($_SESSION['last_attempt']);
                if ($NEXT_RETRY_TIME > $now) {
                    $nextRetryAt = date("r", $NEXT_RETRY_TIME);
                    $currentDate = date("r", $now);
                    die("Too many attempts, next try at: " . $nextRetryAt . " (currently $currentDate)");
                } else {
                    $this->resetLoginAttempt();
                }
            }
        }
        $this->sessionIncrementAttempts();
        $realmDescription = "Attempt #" . $_SESSION['count'] . "/" . static::MAX_ATTEMPTS . " from " . $ip;
        header("WWW-Authenticate: Basic realm='{$this->realm} $realmDescription");
        header('HTTP/1.0 401 Unauthorized');
        die("Authentication cancelled: not authorized");
    }
    private function sessionIncrementAttempts() {
        $_SESSION["count"]++;
        $_SESSION["last_attempt"] = time();
    }
    private function resetLoginAttempt() {
        $_SESSION["count"] = 0;
        unset($_SESSION["last_login"]);
        unset($_SESSION["last_attempt"]);
    }
}
