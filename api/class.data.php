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
		$this->_columns[] = $pkey; /* ensure we are also returning id in queries */
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
	 * @msg - single line message, don't include \n
	 */
	public function writeLog($msg)
	{
		$date = date("m/d/Y h:i:s A (T)");
		error_log("$date Data: $msg\n", 3, LOG_FILE);
	}

	/**
	 * returns the last error message, if any
	 */
	public function getMessage()
	{
		return $this->_message;
	}

	/**
	 * Adds data to the table, returns id
	 */
	public function add($data)
	{
		// IMPROVE INPUT VALIDATION HERE

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
	 * Updates the database with the data specified
	 *
	 * @param id -- primary key of the record to update
	 * @param data -- array of columns=>values to update
	 */
	public function update($id, $data)
	{
		// IMPROVE INPUT VALIDATION HERE
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
	 * Deletes the specified entry
	 */
	public function del($id)
	{
		// IMPROVE INPUT VALIDATION HERE
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
   * Loads all parts
   *
   * @return all columns, all rows
   */
  public function load()
  {
		// IMPROVE INPUT VALIDATION HERE

		$this->_message = '';

		$rows = '';
 		try {
    	$stmt = $this->_db->prepare($this->_select);
      if ($stmt->execute()) {
				while($row = $stmt->fetch()) {
					$rows[$row["id"]] = $row;
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
   * Loads the first matching row
   *
   * @param $col is the column name for where clause
   * @param $val is the value for where clause
   * @return an array of database rows as arrays
   */
  public function loadRow($col, $val)
  {
		// IMPROVE INPUT VALIDATION HERE
		$this->_message = '';
		$row = array();
			try {
    	$stmt = $this->_db->prepare("SELECT * FROM ".$this->_table." WHERE ".$col."=:val");
      $stmt->bindParam(":val", $val, PDO::PARAM_STR);
      if ($stmt->execute()) {
				$row = $stmt->fetch(PDO::FETCH_ASSOC);	// do this once
			} else {
				$err = $stmt->errorInfo();
				$msg = "loadRow(): ".$err[0]." ".$err[1]." ".$err[2];
				$this->writeLog($msg);
			}
      $stmt->closeCursor();
    } catch(PDOException $e) {
			$this->_message = $e->getMessage();
      $this->writeLog($e->getMessage());
    }
    return $row;
	}


	/**
     * Loads all matching rows
     *
     * @param $col is the column name for where clause
     * @param $val is the value for where clause
     * @return an array of database rows as arrays
     */
    public function loadList($col, $val)
    {
		// IMPROVE INPUT VALIDATION HERE

		$this->_message = '';

		$entries = array();

 		try {
        	$stmt = $this->_db->prepare("SELECT * FROM ".$this->_table." WHERE ".$col."=:val");
            $stmt->bindValue(":val", $val, PDO::PARAM_STR);
            if ($stmt->execute()) {
				while($row = $stmt->fetch()) {
					$arr = array();
					foreach ($this->_columns as $c) {
						$arr[$c] = $row[$c];
					}
					$entries[ $row[$this->_pkey] ] = $arr;
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

        return $entries;
    }

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

    // REVISE THIS
    public function lookup($id)
    {
		if (isset($this->_name[$id]))
			return $this->_name[$id];
		else
			return "";
	}

}

?>
