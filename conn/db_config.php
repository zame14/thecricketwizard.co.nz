<?php
session_start();
function db_connect() {

    // Define connection as a static variable, to avoid connecting more than once
    static $connection;

    // Try and connect to the database, if a connection has not been established yet
    if(!isset($connection)) {
        // Load configuration as an array. Use the actual location of your configuration file
        //$config = parse_ini_file('config.ini');
        //$connection = mysqli_connect('mysql7.openhost.net.nz','me03h_cricket_wi','Hq5a4&d5','me03hot13066com46198_cricket_wizard');
        $connection = mysqli_connect('localhost:3306','me03h_cricket_wi','Hq5a4&d5','me03hot13066com46198_cricket_wizard');
    }

    // If connection was not successful, handle the error
    if($connection === false) {
        // Handle error - notify administrator, log to a file, show an error screen, etc.
        return mysqli_connect_error();
    }
    return $connection;
}
function db_query($query) {
    // Connect to the database
    $connection = db_connect();
    // Query the database
    $result = mysqli_query($connection,$query);

    return $result;
}
function sqlQuery($query) {
    $rows = array();
    $result = db_query($query);

    // If query failed, return `false`
    if($result === false) {
        return false;
    }

    // If query was successful, retrieve all the rows into an array
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}
function db_error() {
    $connection = db_connect();
    return mysqli_error($connection);
}
function insert_id() {
    $connection = db_connect();
    return mysqli_insert_id($connection);
}
function dbOutput($value) {
    return htmlentities($value, ENT_QUOTES, 'UTF-8');
}
function dbInput($value) {
    $connection = db_connect();
    // Check for hackers who are trying SQL injection attempts
    $exploits = Array(
        'union select',
        'union all select',
        'information_schema',
    );
    foreach($exploits as $exploit) {
        if(strpos(strtolower($value), $exploit) !== false) {
            //hack
        }
    }
    return mysqli_real_escape_string($connection, trim($value));
}
function request($name) {
    $value = '';
    if(isset($_REQUEST[$name])) {
        $value = $_REQUEST[$name];
        if(is_string($value)) $value = trim($value);
    }
    return $value;
}