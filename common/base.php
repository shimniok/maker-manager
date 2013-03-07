<?php
 
/**
 * Sets up the PHP session and database connection
 *
 * PHP version 5
 *
 * @author Michael Shimniok
 * @copyright 2012 Michael Shimniok
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */

    // Set the error reporting level
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
 
    // Start a PHP session
    session_start();
 
    // Include site constants
    include_once "inc/constants.inc.php";
 
    // Create a database object
    try {
        $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
        $db = new PDO($dsn, DB_USER, DB_PASS);
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
        exit;
    }
     
	// HTML purifier
	//require_once 'inc/HTMLPurifier.auto.php';
?>
