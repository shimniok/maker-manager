<?php
/**
 * REST helper class
 *
 * Given a name ($_name), implements REST API, calling back to supplied functions.
 *
 * Expects to be called with the following pattern (given: $_name="thingy"):
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
  protected $_db;
  protected $_errmsg;

  /**
   * @param string $_name - identifying name used in URI path
   */
  public function __construct($myname="", $mydb=null)
	{
    $this->name = $myname;
    $this->db = $mydb;
  }

  public function handleRequest() {
    // Extract any arguments from the URI, splitting on API name, e.g.,
    // api/thingy/1 splits on 'thingy', with 1 as the argument
    $argv = preg_split('/\//',
      preg_replace( '/^.*\/'.$this->name.'\/{0,1}/', '', $_SERVER['REQUEST_URI']),
      -1, PREG_SPLIT_NO_EMPTY);
    $argc = count($argv);

    if ($argc < 0 || $argc > 1) {
    	$this->errormsg = "arg count is wrong (argc=$argc) - ".$_SERVER['REQUEST_URI'];
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
  				$response = $this->db->loadRow('id', $argv[0]);
  				writeLog("GET id=".$argv[0]);
  			} else {
  				// Retrieve all
          $response = $this->db->load();
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
