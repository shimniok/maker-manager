<?php
 
/**
 * Handles interactions with subtypes
 *
 * PHP version 5
 *
 * @author Michael Shimniok
 * @copyright 2012 Michael Shimniok
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Subtypes
{
	 /**
	 * The database object
	 *
	 * @var object
	 */
	private $_db;
	private $_name;  // permit lookup of names by id

	/**
	 * Checks for a database object and creates one if none is found
	 *
	 * @param object $db
	 * @return void
	 */
	public function __construct($db=NULL)
	{
		if(is_object($db))
		{
			$this->_db = $db;
		}
		else
		{
			$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
			$this->_db = new PDO($dsn, DB_USER, DB_PASS);
		}
	}
	
	public function add($name)
	{
		// Use htmlpurifier library
		$name = strip_tags(urldecode(trim($name)), "");
 
        $sql = "INSERT INTO subtypes (name)
                VALUES (:name)";
        try {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
            return $this->_db->lastInsertId();
        } catch(PDOException $e) {
            return $e->getMessage();
        }

	}
	
	public function update($id, $name)
	{
		// INPUT VALIDATION!
		$sql="UPDATE subtypes
			  SET name=:name
			  WHERE id=:id";
		try {
			$stmt = $this->_db->prepare($sql);
			$stmt->bindValue(':name', $name, PDO::PARAM_INT);
			$stmt->bindValue(':id', $id, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
			return 0;
		} catch(PDOException $e) {
			return $e->getMessage();
		}
	}
	
	public function del($id)
	{
 		// Use htmlpurifier
 		// Ensure this is an integer only
        $id = strip_tags(urldecode(trim($id)), "");
 
        $sql = "DELETE FROM subtypes
        		WHERE id = :id";
        try {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
 
            return 0;
        } catch(PDOException $e) {
            return $e->getMessage();
        }
	}

	
    /**
     * Loads all parts
     *
     * @return Part No, Footprint, Value, Voltage, Tolerance, Type, Subtype
     */
    public function load()
    {
        $sql = "SELECT id, name
                FROM subtypes
                ORDER BY name";
        if($stmt = $this->_db->prepare($sql)) {
			$entries = array();
			$this->_name = array();
            $stmt->execute();
			while($row = $stmt->fetch()) {  
				$entries[] = array( 
					'id' => $row['id'],
					'name' => $row['name']
				);
				$this->_name[ $row['id'] ] = $row['name'];
			}  
            $stmt->closeCursor();
        }
 
        return $entries;
    }	
    
    public function lookup($id)
    {
		if (isset($this->_name[$id]))
			return $this->_name[$id];
		else
			return "";
	}
 
}
 
?>
