<?php

/**
 * Generic data handling class for simple table. Intended to be extended
 *
 * expects constants DB_HOST, DB_NAME, DB_USER, DB_PASS
 *
 * PHP version 5
 *
 * @author Michael Shimniok
 * @copyright 2012 Michael Shimniok
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Data {

    /**
     * The database object
     *
     * @var object
     */
    protected $_db;
    protected $_data;    // cache database data in memory for later lookup (?)
    protected $_table;   // name of the table in the database
    protected $_pkey;    // primary key column name
    protected $_columns; // the column names in the database
    protected $_select;  // sql statement for selects
    protected $_update;  // sql statement for updates
    protected $_delete;  // sql statement for deletes
    protected $_error;   // last error message
    protected $_status;  // error status

    // TODO: Make methods smaller  

    /**
     * Checks for a database object and creates one if none is found
     *
     * @param object $db		-- database
     * @param string $table		-- table name
     * @param string $pkey		-- primary key column name
     * @param array $columns	-- list of columns, put in order of desired order
     *
     * @return void
     */   
    public function __construct($db = NULL, $table, $pkey, $columns) {
        if (is_object($db)) {
            $this->_db = $db;
        } else {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
            $this->_db = new PDO($dsn, DB_USER, DB_PASS);
        }

        $this->_table = $table;
        $this->_columns = $columns;
        $this->_pkey = $pkey;

        // SELECT SQL statement
        $this->_select = "SELECT * FROM " . $this->_table;

        $this->_error = "";
    }

    /**
     * Writes to error log with common format
     *
     * @param string $msg - single line message, don't include \n
     */
    public function writeLog($msg) {
        $date = date("m/d/Y h:i:s A (T)");
        error_log("$date Data: $msg\n", 3, LOG_FILE);
    }

    /**
     * get the last error message, if any
     *
     * @return string, the most recent error message
     */
    public function getErrorMessage() {
        return $this->_error;
    }

    /**
     * Adds a single row to the table
     *
     * @param assoc array $data is the row data to be added with column => value pairs
     * @return assoc. array { "id" => id } if successful, empty array if failed
     */
    public function add($data) {
        $this->_error = '';

        // Construct UPDATE SQL statement
        $new = array();    // make a new array of valid columns and values
        $cols = array();   // list of columns for INSERT
        $values = array(); // list of parameterized values for INSERT

        foreach ($data as $col => $value) {
            if (in_array($col, $this->_columns)) { // key is valid column
                $cols[] = "$col";
                $values[] = ":$col";
                $new[$col] = $value;
            }
        }

        $insert = "INSERT INTO " . $this->_table . " (" .
                implode(', ', $cols) . ') VALUES (' .
                implode(', ', $values) . ')';

        try {
            $stmt = $this->_db->prepare($insert);
            // BIND A LIST OF PARAMETERS
            $msg = "add(): " . $insert; // for logging
            foreach ($new as $col => $value) {
                // TODO: what to do about param type? PDO::PARAM_STR
                $stmt->bindValue(":$col", $value); 
                $msg .= " :$col=$value"; // for logging
            }

            if ($stmt->execute()) {
                $new["id"] = $this->_db->lastInsertId();
            } else {
                $this->_error = implode(' ', $stmt->errorInfo());
                $new = array();
            }
            $this->writeLog($msg);
            $stmt->closeCursor();
        } catch (PDOException $e) {
            $this->_error = $e->getMessage();
        }
        
        if ($this->_error != '') {
            $this->writeLog($this->_error);
        }
        
        return $new;
    }

    /**
     * Updates a database row with the data specified
     *
     * @param int id primary key of the row to update
     * @param assoc array $data col => value pairs to update in the table row
     * @return true if successful, false if not
     */
    public function update($id, $data) {
        $this->_error = '';
        $status = false;

        // Construct UPDATE SQL statement
        $newData = array(); // make a new array of valid columns and values
        $list = array();  // list of parameterized items for SET
        foreach ($data as $col => $value) {
            if (in_array($col, $this->_columns)) { // key is valid column
                $list[] = "$col=:$col";
                $newData[$col] = $value;
            }
        }
        $this->_update = "UPDATE " . $this->_table . " SET " .
                implode(', ', $list) .
                " WHERE " . $this->_pkey . "=:" . $this->_pkey;

        $msg = "update(): " . $this->_update;

        try {
            $stmt = $this->_db->prepare($this->_update);
            // BIND A LIST OF PARAMETERS
            $stmt->bindParam(":" . $this->_pkey, $id, PDO::PARAM_INT);
            foreach ($newData as $col => $value) {
                $stmt->bindValue(":$col", $value); // TODO: what to do about param type?
                $msg .= " :$col=$value";
            }
            if ($stmt->execute()) {
                $status = true;
            } else {
                $this->_error = implode(' ', $stmt->errorInfo());
            }
            $this->writeLog($msg);
            $stmt->closeCursor();
        } catch (PDOException $e) {
            $this->_error = $e->getMessage();
        }
        
        if ($this->_error != '') {
            $this->writeLog($this->_error);
        }
        
        return $status;
    }

    /**
     * Deletes the specified row
     *
     * @param int $id primary key of the row to delete
     * @return true if successful, false if not
     */
    public function del($id) {
        $this->_error = '';
        $status = false;

        try {
            // DELETE SQL statement
            $this->_delete = "DELETE FROM " . $this->_table . " WHERE " . $this->_pkey . "=:" . $this->_pkey;

            $stmt = $this->_db->prepare($this->_delete);
            // BIND A LIST OF PARAMETERS
            $stmt->bindParam(":" . $this->_pkey, $id, PDO::PARAM_INT);

            $msg = "del(): " . $this->_delete . " id=" . $id . "\n";

            if ($stmt->execute()) {
                $status = true;
            } else {
                $this->_error = implode(' ', $stmt->errorInfo());
                $status = false;
            }
            $this->writeLog($msg);
            $stmt->closeCursor();
        } catch (PDOException $e) {
            $this->_error = $e->getMessage();
            $status = false;
        }
        
        if ($this->_error != '') {
            $this->writeLog($this->_error);
        }
        
        return $status;
    }

    /**
     * Queries and returns the first n matching rows. To query all, pass
     * no parameters.
     *
     * @param string $col is the column name for where clause
     * @param string $val is the value for where clause
     * @paran int $n is the number of matches to return (default: 0, all rows)
     * @return an array of database rows
     */
    public function query($col = '', $val = '', $n = 0) {
        $this->_error = '';
        $rows = array();

        // TODO: Convert to use $this->_select
        $stmt_str = $this->_select;
        
        if ($col != '' && $val != '') {
            $stmt_str .= " WHERE " . $col . "=:val";
        }

        try {
            $stmt = $this->_db->prepare($stmt_str);
            $stmt->bindValue(":val", $val, PDO::PARAM_STR);
            if ($stmt->execute()) {
                $row_count = 0;
                while ($row = $stmt->fetchObject()) {
                    array_push($rows, $row);
                    $row_count++;
                    if ($n > 0 && $row_count >= $n) {
                        break;
                    }
                }
            } else {
                $this->_error = implode(' ', $stmt->errorInfo());
            }
            $stmt->closeCursor();
        } catch (PDOException $e) {
            $this->_error = $e->getMessage();
        }
        
        if ($this->_error != '') {
            $this->writeLog($this->_error);
        }
        
        return $rows;
    }

}

?>
