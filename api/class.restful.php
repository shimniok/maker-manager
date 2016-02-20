<?php
/**
 * REST helper class
 *
 * Given a name ($name), implements REST API, calling back to supplied functions.
 *
 * Expects to be called with the following pattern (given: $name="thingy"):
 *
 * GET BASE_URL/thingy       - Retrieves a list of thingys
 * GET BASE_URL/thingy/12    - Retrieves a specific thingy (#12)
 * POST BASE_URL/thingy      - Creates a new thingy
 * PUT BASE_URL/thingy/9     - Updates thingy #9
 * DELETE BASE_URL/thingy/2  - Deletes thingy #2
 *
 * PHP version 5
 *
 * @author Michael Shimniok
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */

Class Restful
{
  protected $_name;
  protected $_errormsg;
  protected $_get_handler;

  /**
   * @param string $name - identifying name used in URI path
   */
  public function __construct($name="")
	{
    $this->_name = $name;
  }

  public function handleRequest($db) {
    // Extract any arguments from the URI
    $argv = preg_split('/\//',
      preg_replace( '/^.*\/'.$this->_name.'\/{0,1}/', '', $_SERVER['REQUEST_URI']),
      -1, PREG_SPLIT_NO_EMPTY);
    $argc = count($argv);

    if ($argc < 0 || $argc > 1) {
    	$this->_errormsg = "arg count is wrong (argc=$argc) - ".$_SERVER['REQUEST_URI'];
    	return false;
    } elseif ($argc == 1) {
      $id = $argv[0];
    } else {
      $id = null;
    }

    header('Content-Type: application/json');

    $response = array();

    switch($_SERVER['REQUEST_METHOD']){
  		case 'GET':
        if ($argc == 1) {
  				// Retrieve one
  				$response = $db->loadRow('id', $argv[0]);
  				writeLog("GET id=".$argv[0]);
  			} else {
  				// Retrieve all
  				$response = $db->load();
  				writeLog("GET all");
  			}
  			break;
  		case 'POST':

  			break;
  		case 'DELETE':

  			break;
  		case 'PUT':

  			break;
  	}//switch

    echo json_encode($response);

  }//handleRequest()

}//Class Restful

?>