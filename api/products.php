<?php
/**
 * REST interface
 *
 * Expects to be called with the following pattern:
 * GET BASE_URL/thingy       - Retrieves a list of thingys
 * GET BASE_URL/thingy/12    - Retrieves a specific thingy (#12)
 * POST BASE_URL/thingy      - Creates a new thingy
 * PUT BASE_URL/thingy/12    - Updates thingy #12
 * DELETE BASE_URL/thingy/12 - Deletes thingy #12
 */
include_once 'base.php';
require_once __DIR__ . '/silex/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

$app = new Application();

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$columns = array('id', 'inventory', 'needed', 'name');
$db = new Data($db, 'products', 'id', $columns);

$app->get('/products', function() use($db) {
    return json_encode($db->query());
});

$app->get('/products/{id}', function ($id) use ($db) {
    return json_encode($db->query('id', $id, 1));
});

$app->post('/products', function(Request $request) use($db) {
    $new = array(
        "name" => $request->get("name"),
        "needed" => $request->get("needed"),
        "inventory" => $request->get("inventory")
    );
    return json_encode($db->add($new));
});

$app->run();
