<?php

/**
 * User: Bryan Mayor
 * Company: Blue Nest Digital, LLC
 * License: (All rights reserved)
 * Description: A class for interacting with a MySQL database using the MySQLi driver.
 * Provides a builder class for creating new connections.
 * Supports querying using raw queries or prepared statements.
 */

class DatabaseManager {

    var $databaseAddress;
    var $username;
    var $password;
    var $database = null;

    var $connection;

    function __construct($databaseAddress, $username, $password, $database = null, $createConnection = false)
    {
        $this->databaseAddress = $databaseAddress;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;

        if($createConnection){
            $this->connect();
        }
    }

    function setDatabase($databaseName)
    {
        if(!$this->connection->select_db($databaseName)) {
            die("Could not set database to $databaseName: " . mysqli_error($this->connection));
        }
        $this->database = $databaseName;
    }

    function connect()
    {
        $connection = mysqli_connect($this->databaseAddress, $this->username, $this->password, $this->database);

        if ($connection->connect_errno) {
            die('Could not connect: ' . $connection->connect_error);
        };

        $this->connection = $connection;
    }

    function disconnect()
    {
        $this->connection->mysqli_close();
    }

    // Execute a query. Preferable to use doPreparedStatement for
    // security purposes.
    function doQuery($sql)
    {
        if(!$result = $this->connection->query($sql)) {
            die('Error running query: ' . mysqli_error($this->connection));
        };

        $resultArray = array();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $resultArray[] = $row;
        }


        return($resultArray);
    }

    // Execute prepared statement and return result as array of keyed-arrays
    function doPreparedStatement($preparedStatement, $bindParameters) {
        if ($stmt = $this->connection->prepare($preparedStatement)) {

            /* bind parameters for markers */
            foreach($bindParameters as $parm=>$val) {
                $stmt->bind_param($parm, $val);
            }

            /* execute query */
            $stmt->execute();
            $result = $stmt->get_result();

            $resultArray = array();
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $resultArray[] = $row;
            }

            /* close statement */
            $stmt->close();

            return $resultArray;
        } else {
            die('Error creating prepared statement: ' . mysqli_error($this->connection));
        }
    }
}

class DatabaseManagerBuilder {
    private $host = null;
    private $database = null;
    private $username = null;
    private $password = null;
    private $verbose = false;
    const requiredFileFields = array("username", "host", "password");

    static function create() {
        return new DatabaseManagerBuilder();
    }

    function fromFile($filename) {
        if(!file_exists($filename)) {
            throw new Exception("The expected db configuration file '" . $filename . "' does not exist. Please create a JSON format file with this name
                with keys for 'host', 'username', and 'password");
        }

        $contents = file_get_contents($filename);
        $config = json_decode($contents, true);
        foreach(self::requiredFileFields as $requiredField) {
            if(!isset($config[$requiredField])) {
                throw new Exception("File " . $filename . "is missing json key/value " . $requiredField);
            }
        }
        $this->username($config["username"])->host($config["host"])->password($config["password"]);
        if(isset($config["database"])) {
            $this->database($config["database"]);
        }

        return $this;
    }

    function connect() {
        if($this->verbose) {
            echo "DatabaseManagerBuilder: Connecting to host '" . $this->host . "' with username '" . $this->username . "'" . PHP_EOL;
        }
        $databaseManager =  new DatabaseManager($this->host, $this->username, $this->password, $this->database, true);
        return $databaseManager;
    }

    function username($val) {
        $this->username = $val;
        return $this;
    }

    function password($val) {
        $this->password = $val;
        return $this;
    }

    function host($val) {
        $this->host = $val;
        return $this;
    }

    function database($val) {
        $this->database = $val;
        return $this;
    }

    function verbose($val) {
        $this->verbose = $val;
        return $this;
    }
}

?>