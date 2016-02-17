<?php

/**
 * Sets up the PHP session and database connection provides log function
 * and sets error reporting level, and defines global variables:
 *
 *  $me   - basename of the current php file (used for REST API)
 *  $argv - array of arguments extracted from URI (REST API)
 *  $argc - count of URI arguments
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

// Get the API name based on the current php filename
$me = preg_replace('/\.php$/', '', basename(__FILE__));

// Extract any arguments from the URI
$argv = preg_split('/\//',
	preg_replace( '/^.*\/'.$me.'\/{0,1}/', '', $_SERVER['REQUEST_URI']),-1, PREG_SPLIT_NO_EMPTY);
$argc = count($argv);

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
