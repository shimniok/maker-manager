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
class Data
{
    /**
	 * The database object
	 *
	 * @var object
	 */
	protected $_db;
	protected $_data;		// cache database data in memory for later lookup (?)
	protected $_table;		// name of the table in the database
	protected $_pkey;		// primary key column name
	protected $_columns;	// the column names in the database
	protected $_select;		// sql statement for selects
	protected $_selone;		// sql statement for single row select
	protected $_insert;		// sql statement for inserts
	protected $_update;		// sql statement for updates
	protected $_delete;		// sql statement for deletes
	protected $_message;	// last message

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
	public function __construct($db=NULL, $table, $pkey, $columns)
	{
		if(is_object($db)) {
			$this->_db = $db;
		} else {
			$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
			$this->_db = new PDO($dsn, DB_USER, DB_PASS);
		}

		$this->_table = $table;
		$this->_columns = $columns;
		#$this->_columns[] = $pkey; /* ensure we are also returning id in queries */
		$this->_pkey = $pkey;

		// SELECT SQL statement
		$this->_select =  "SELECT * FROM ".$this->_table;

		// SINGLE SELECT SQL statement
		$this->_selone = $this->_select ." WHERE id=:val";

		$this->_message="";
	}

	/**
	 * Writes to error log with common format
	 *
	 * @param string $msg - single line message, don't include \n
	 */
	public function writeLog($msg)
	{
		$date = date("m/d/Y h:i:s A (T)");
		error_log("$date Data: $msg\n", 3, LOG_FILE);
	}

	/**
	 * get the last error message, if any
	 *
	 * @return string, the most recent error message
	 */
	public function getMessage()
	{
		return $this->_message;
	}

	/**
	 * Adds a single row to the table
	 *
	 * @param assoc array $data the row data to be added with column => value pairs
	 * @return id if successful, -1 if failed
	 */
	public function add($data)
	{
		$this->_message = '';
		$status = -1;

        try {
            // Construct UPDATE SQL statement
            $newData = array();	// make a new array of valid columns and values
            $cols = array(); 	// list of columns for INSERT
            $values = array();  // list of parameterized values for INSERT

            foreach ($data as $col => $value) {
                if (in_array($col, $this->_columns)) { // key is valid column
                    $cols[] = "$col";
                    $values[] = ":$col";
                    $newData[$col] = $value;
                }
            }

            $this->_insert = "INSERT INTO ".$this->_table." (".
                implode(', ', $cols).') VALUES ('.
                implode(', ', $values).')';

            $stmt = $this->_db->prepare($this->_insert);

            $msg = "add(): ".$this->_insert;

            // BIND A LIST OF PARAMETERS
            foreach ($newData as $col => $value) {
                $stmt->bindValue(":$col", $value); // TODO: what to do about param type? PDO::PARAM_STR
                $msg .= " :$col=$value";
            }

            if ($stmt->execute()) {
                $status = $this->_db->lastInsertId();
            } else {
                $err = $stmt->errorInfo();
                $msg .= " ".$err[0]." ".$err[1]." ".$err[2];
                $status = -1;
            }
            $this->writeLog($msg);
            $stmt->closeCursor();
        } catch(PDOException $e) {
          $this->_message = $e->getMessage();
          $this->writeLog($e->getMessage());
          $status = -1;
        }
	    return $status;
	}

	/**
	 * Updates a database row with the data specified
	 *
	 * @param int id primary key of the row to update
	 * @param assoc array $data col => value pairs to update in the table row
	 * @return 1 if successful, 0 if not
	 */
	public function update($id, $data)
	{
		$this->_message = '';
		$status = 0;

		// Construct UPDATE SQL statement
		$newData = array();	// make a new array of valid columns and values
		$list = array(); 	// list of parameterized items for SET
		foreach ($data as $col => $value) {
			if (in_array($col, $this->_columns)) { // key is valid column
				$list[] = "$col=:$col";
				$newData[$col] = $value;
			}
		}
		$this->_update = "UPDATE ".$this->_table." SET ".
			implode(', ', $list).
			" WHERE ".$this->_pkey."=:".$this->_pkey;

		$msg = "update(): ".$this->_update;

		try {
            $stmt = $this->_db->prepare($this->_update);
            // BIND A LIST OF PARAMETERS
            $stmt->bindParam(":".$this->_pkey, $id, PDO::PARAM_INT);
            foreach ($newData as $col => $value) {
                $stmt->bindValue(":$col", $value); // TODO: what to do about param type?
                $msg .= " :$col=$value";
            }
            if ($stmt->execute()) {
                $status = 1;
            } else {
                $err = $stmt->errorInfo();
                $msg .= " ".$err[0]." ".$err[1]." ".$err[2];
            }
            $this->writeLog($msg);
            $stmt->closeCursor();
        } catch(PDOException $e) {
            $this->_message = $e->getMessage();
            $this->writeLog($e->getMessage());
        }
        return $status;
	}

	/**
	 * Deletes the specified row
	 *
	 * @param int $id primary key of the row to delete
	 * @return 0 if successful, -1 if not
	 */
	public function del($id)
	{
		$this->_message = '';
		$status = 0;

		try {
			// DELETE SQL statement
			$this->_delete = "DELETE FROM ".$this->_table." WHERE ".$this->_pkey."=:".$this->_pkey;

            $stmt = $this->_db->prepare($this->_delete);
            // BIND A LIST OF PARAMETERS
            $stmt->bindParam(":".$this->_pkey, $id, PDO::PARAM_INT);

			$msg = "del(): ".$this->_delete." id=".$id."\n";

			if ($stmt->execute()) {
				$status = 0;
			} else {
				$err = $stmt->errorInfo();
				$msg .= " ".$err[0]." ".$err[1]." ".$err[2];
				$status = -1;
			}
			$this->writeLog($msg);
            $stmt->closeCursor();
        } catch(PDOException $e) {
			$this->_message = $e->getMessage();
    	    $this->writeLog($e->getMessage());
            $status = -1;
        }
        return $status;
	}

    /**
     * Queries and returns all rows
     *
     * @return all columns, all rows
     */
    public function query()
    {
		$this->_message = '';

		$rows = array();
 		try {
            $stmt = $this->_db->prepare($this->_select);
            if ($stmt->execute()) {
                while($row = $stmt->fetch()) {
                    array_push($rows, $row);
                }
            } else {
                $err = $stmt->errorInfo();
                $msg = "load(): ".$err[0]." ".$err[1]." ".$err[2];
                $this->writeLog($msg);
            }
            $stmt->closeCursor();
        } catch(PDOException $e) {
            $this->_message = $e->getMessage();
            $this->writeLog($e->getMessage());
        }
        return $rows;
	}


    /**
     * Queries and returns the first n matching rows
     *
     * @param $col is the column name for where clause
     * @param $val is the value for where clause
     * @paran $n is the number of matches to return (default: 0, return all)
     * @return an array of database rows as assoc. arrays (col => value)
     */
    public function get($col, $val, $n=0)
    {
		$this->_message = '';
		$rows = array();

 		try {
        	$stmt = $this->_db->prepare("SELECT * FROM ".$this->_table." WHERE ".$col."=:val");
            $stmt->bindValue(":val", $val, PDO::PARAM_STR);
            if ($stmt->execute()) {
                $row_count = 0;
				while($row = $stmt->fetch()) {
					array_push($rows, $row);
					$row_count++;
					if ($n && $row_count >= $n) {
					    break;
					}
				}
			} else {
				$err = $stmt->errorInfo();
				$msg = "loadList(): ".$err[0]." ".$err[1]." ".$err[2];
				$this->writeLog($msg);
			}
            $stmt->closeCursor();
        } catch(PDOException $e) {
			$this->_message = $e->getMessage();
            $this->writeLog($e->getMessage());
        }

        return $rows;
    }

   /**
    * call a stored procedure
    *
    * @param string $fctn name of function to call
    * @param array $params collection of parameters to pass to the call
    */
    public function call($fctn, $params)
    {
		$query = "CALL ".$fctn." (";
		$i = 0;
		foreach ($params as $p) {
			$query .= ":p".$i;
		}
		$query .= ")";

		$stmt = $this->_db->prepare($query);

		$i = 0;
		$msg = "call: $query - "; // error message
		foreach ($params as $p) {
			$stmt->bindParam(":p".$i, $p);
			$msg .= "[$p] ";
		}

		if ($stmt->execute()) {
			$this->writeLog($msg);
		} else {
			$err = $stmt->errorInfo();
            $this->writeLog($msg." ".$err[0]." ".$err[1]." ".$err[2]);
        }
	}

}

?>
