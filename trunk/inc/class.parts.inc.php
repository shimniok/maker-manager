<?php

/**
 * Handles interactions with parts
 *
 * PHP version 5
 *
 * @author Michael Shimniok
 * @copyright 2012 Michael Shimniok
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Parts
{
	 /**
	 * The database object
	 *
	 * @var object
	 */
	private $_db;
	private $_entries;

	/**
	 * Checks for a database object and creates one if none is found
	 *
	 * @param object $db
	 * @return void
	 */
	public function __construct($db=NULL)
	{
		if(is_object($db)) {
			$this->_db = $db;
		} else {
			$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
			$this->_db = new PDO($dsn, DB_USER, DB_PASS);
		}
	}
	
	/**
	 * Add new entry
	 * @param partNo
	 * @param footprint
	 * @param value
	 * @param voltage
	 * @param tolerance
	 * @param type
	 * @param subtype
	 */
	public function add( $inventory, $partNo, $footprint, $value, $voltage, $tolerance, $types_id, $subtypes_id )
	{
		$sql = "INSERT INTO parts (inventory, partNo, footprint, value, voltage, tolerance, types_id, subtypes_id)
				VALUE(:inventory, :partNo, :footprint, :value, :voltage, :tolerance, :types_id, :subtypes_id)";
		try {
			//$config = HTMLPurifier_Config::createDefault();
			//$purifier = new HTMLPurifier($config);
        	$stmt = $this->_db->prepare($sql);
            $stmt->bindValue(':inventory', $inventory, PDO::PARAM_STR);
            $stmt->bindValue(':partNo', $partNo, PDO::PARAM_STR);
            $stmt->bindValue(':footprint', $footprint, PDO::PARAM_STR);
            $stmt->bindValue(':value', $value, PDO::PARAM_STR);
            $stmt->bindValue(':voltage', $voltage, PDO::PARAM_STR);
            $stmt->bindValue(':tolerance', $tolerance, PDO::PARAM_STR);
            $stmt->bindValue(':types_id', $types_id, PDO::PARAM_INT);
            $stmt->bindValue(':subtypes_id', $subtypes_id, PDO::PARAM_INT);
			$stmt->execute();
            $stmt->closeCursor();

            return $this->_db->lastInsertId();
        } catch(PDOException $e) {
            return $e->getMessage();
        }
	}
	
	/** 
	 * updates just the inventory part of the record
	 */
	public function updateInventory($id, $inventory) 
	{
		// INPUT VALIDATION!		
		$sql="UPDATE parts
			  SET 	inventory=:inventory
			  WHERE id=:id";
		try {
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->bindParam(':inventory', $inventory, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
			return 0;
		} catch(PDOException $e) {
			return $e->getMessage();
		}
	}
	
	/**
	 * Updates the record
	 */
	public function update( $id, $inventory, $partNo, $footprint, $value, $voltage, $tolerance, $types_id, $subtypes_id )
	{
		// INPUT VALIDATION!		
		$sql="UPDATE parts
			  SET 	inventory=:inventory,
			  		partNo=:partNo,
					footprint=:footprint,
					value=:value,
					voltage=:voltage,
					tolerance=:tolerance,
					types_id=:types_id,
					subtypes_id=:subtypes_id
			  WHERE id=:id";
		try {
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->bindParam(':inventory', $inventory, PDO::PARAM_STR);
			$stmt->bindParam(':partNo', $partNo, PDO::PARAM_STR);
			$stmt->bindParam(':footprint', $footprint, PDO::PARAM_STR);
			$stmt->bindParam(':value', $value, PDO::PARAM_STR);
			$stmt->bindParam(':voltage', $voltage, PDO::PARAM_STR);
			$stmt->bindParam(':tolerance', $tolerance, PDO::PARAM_STR);
			$stmt->bindParam(':types_id', $types_id, PDO::PARAM_INT);
			$stmt->bindParam(':subtypes_id', $subtypes_id, PDO::PARAM_INT);
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
        // $id = strip_tags(urldecode(trim($id)), "");
        $sql = "DELETE FROM parts
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
        $sql = "SELECT id, inventory, partNo, footprint, value, voltage, tolerance, types_id, subtypes_id
                FROM parts
                ORDER BY types_id, subtypes_id, value, footprint";
        if($stmt = $this->_db->prepare($sql)) {
			$entries = array();
			$this->_entries = array();
            $stmt->execute();
			while($row = $stmt->fetch()) {  
				$entries[] = $this->_entries[$row['id']] = array(  
					'id' => $row['id'],
					'inventory' => $row['inventory'],
					'partNo' => $row['partNo'],
					'footprint' => $row['footprint'],
					'value' => $row['value'],
					'voltage' => $row['voltage'],
					'tolerance' => $row['tolerance'],
					'types_id' => $row['types_id'],
					'subtypes_id' => $row['subtypes_id']
				); 
				
			}  
            $stmt->closeCursor();
        }
 
        return $entries;
    }	
 
 	public function lookup($id) {
		// Input validation
		// Make sure this is an integer and valid entry
		if (isset($this->_entries[$id]))
			return $this->_entries[$id];
		else 
			return array();
	}
}
 
?>
