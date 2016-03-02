<?php

/**
 * Sets up the PHP session and database connection provides log function
 * and sets error reporting level.
 *
 * PHP version 5
 *
 * @author Michael Shimniok
 * @copyright 2012 Michael Shimniok
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */

// Set the error reporting level
error_reporting(E_ALL);
ini_set("log_errors", 1);
ini_set("display_errors", 1);

// Start a PHP session
session_start();

// Include site constants
include_once "config.php";

// Create a database object
try {
	$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
	$db = new PDO($dsn, DB_USER, DB_PASS);
} catch (PDOException $e) {
	echo 'Connection failed: ' . $e->getMessage();
	exit;
}

/**
 * writeLog writes the provided message to the logfile along with date and some other info.
 * Expects a global variables $me and constant LOG_FILE
 *
 * @param constant LOG_FILE - filename of log file
 * @param string $me        - php module identifier
 * @param string $msg       - the message to write to the logfile
 */
function writeLog($msg) {
	global $me;
	$date = date("m/d/Y h:i:s A (T)");
	error_log("$date $me $msg\n", 3, LOG_FILE);
}

include_once 'class.data.php';
?>
