<?php

require_once __DIR__.'/silex/vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

/**
 * Restful: Class for REST API implementation based on Silex and
 * class.data.php for backend
 *
 * expects constants DB_HOST, DB_NAME, DB_USER, DB_PASS
 *
 * PHP version 5
 *
 * @author Michael Shimniok
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Restful {
    protected $app; // Silex object
    protected $path; // base URL path for rest API
    protected $fullpath; // $path/{$pkey} - constructed from $path and $pkey
    protected $pkey; // primary key
    protected $columns; // table's column names
    protected $db; // data object
    protected $data; // incoming post data
    protected $json; // incoming json string

    /**
     * Constructor
     * 
     * @param type $this->db Data class object
     * @param type $path base bath of the API (e.g., /stuff, not /stuff/$pkey)
     * @param type $table table name
     * @param type $pkey column that is the primary key
     * @param type $columns array of column names
     */
    public function __construct($path, $table, $columns) {

        $this->table = $table;
        $this->columns = $columns;
        $this->path = $path;

        $this->db = new Data($this->db, $table, $columns);
        $this->app = new Application();
        $this->app['debug'] = true;
        $this->app->delete($path.'/{id}', array($this, 'remove'));
        $this->app->before(array($this, 'before'));
        $this->app->get($path.'/{id}', array($this, 'query'));
        $this->app->post($path, array($this, 'add'));
        $this->app->put($path.'/{id}', array($this, 'update'));
    }
    
    public function before(Request $request) {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $this->data = json_decode($request->getContent(), true);
            $this->json = $request->getContent();
            $request->request->replace(is_array($data) ? $this->data : array());
        }
    }

    public function query($id='') {
        $result = '';
        if ($id == '') {
            writeLog('REST API: GET /types');
            $result = json_encode($this->db->query());
        } else {
            writeLog('REST API: GET /types/'.$id);
            $result = json_encode($this->db->query('id', $id, 1));
        }
        return $result;
    }

    public function add(Request $request) {
        writeLog('REST API: POST '.$this->path.' request='.$this->json);
/*         
        $new = array(
            "name" => $request->get("name") 
        );
        return json_encode($this->db->add($new));
 * 
 */
    }
    
    public function update(Request $request, $id) {
        writeLog('REST API: PUT /types/'.$id.' request='.$request->getContent());

        /*
        $data = array(
            "id" => $request->get("id"),
            "name" => $request->get("name")
        );
        return json_encode($this->db->update($data));
         * 
         */
    }
    
    public function remove(Request $request, $id) {
        writeLog('REST API: POST /types/'.$id.' request='.$request->getContent());
/*
        $result = array('status' => $this->db->del($id));
        return json_encode($result);
 * 
 */
    }
        
    /**
     * writeLog writes the provided message to the logfile along with date and some other info.
     * Expects a global variables $me and constant LOG_FILE
     *
     * @param constant LOG_FILE - filename of log file
     * @param string $msg       - the message to write to the logfile
     */
    public function writeLog($msg) {
        $date = date("m/d/Y h:i:s A (T)");
        error_log("$date $msg\n", 3, LOG_FILE);
    }
    
    public function run() {
        return $this->app->run();
    }
}





