<?php
 
/**
 * Handles interactions with BOM for products
 *
 * PHP version 5
 *
 * @author Michael Shimniok
 * @copyright 2012 Michael Shimniok
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class BOM
{
	 /**
	 * The database object
	 *
	 * @var object
	 */
	private $_db;
	private $_parts;
	private $_unique;

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
	 * Counts the total number of parts in the BOM for the most recent load
	 */
	public function partCount()
	{
		return $this->_parts;
	}

	/**
	 * Returns the number of unique parts from the BOM for the most recent load
	 */
	public function uniqueCount()
	{
		return $this->_unique;
	}

	public function add($prodid, $partid, $qty)
	{
		// INPUT VALIDATION!
		// Are we adding to an existing part/prod ??
		$sql="INSERT INTO product_part (products_id, parts_id, qty)
	 		  VALUES (:prodid, :partid, :qty)";
	 	try {
        	$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':prodid', $prodid, PDO::PARAM_INT);
			$stmt->bindParam(':partid', $partid, PDO::PARAM_INT);
			$stmt->bindParam(':qty', $qty, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            return $this->_db->lastInsertId();
        } catch(PDOException $e) {
			return $e->getMessage();
        }
	}

	/**
	 * Updates the quantity
	 */
	public function update($prodid, $partid, $qty)
	{
		// INPUT VALIDATION!
		
		// if qty is 0 then we need to delete the line item
		if ($qty == 0) {
			return $this->del($prodid, $partid);
		} else {
			$sql="UPDATE product_part
				  SET qty=:qty
				  WHERE products_id=:prodid AND parts_id=:partid";
			try {
				$stmt = $this->_db->prepare($sql);
				$stmt->bindParam(':prodid', $prodid, PDO::PARAM_INT);
				$stmt->bindParam(':partid', $partid, PDO::PARAM_INT);
				$stmt->bindParam(':qty', $qty, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();
				return 0;
			} catch(PDOException $e) {
				return $e->getMessage();
			}
		}
	}

	public function del($prodid, $partid)
	{
 		// Use htmlpurifier
 		// Ensure this is an integer only
        // $id = strip_tags(urldecode(trim($id)), "");
 
        $sql = "DELETE FROM product_part
        		WHERE products_id = :prodid AND parts_id = :partid";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':prodid', $prodid, PDO::PARAM_INT);
            $stmt->bindParam(':partid', $partid, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();

            return 0;
        }
        catch(PDOException $e)
        {
            return $e->getMessage();
        }
	}

   /**
     * Loads all BOM entries for a product
     *
     * This function outputs a table of parts
     */
    public function load($prodid)
    {
        $sql = "SELECT parts_id, products_id, qty
                FROM product_part
                WHERE products_id = :prodid";
        if($stmt = $this->_db->prepare($sql)) {
			$entries = array();
			$this->_unique = $this->_parts = 0;
			$stmt->bindParam(':prodid', $prodid);
            $stmt->execute();
            $order = 0;
            while($row = $stmt->fetch()) {
				$entries[] = array( 
					'parts_id' => $row['parts_id'],
					'products_id' => $row['products_id'],
					'qty' => $row['qty']
				);
				$this->_parts += $row['qty'];
				$this->_unique++;
            }
            $stmt->closeCursor();
        }
        return $entries;
    }	
	
}
 
?>
