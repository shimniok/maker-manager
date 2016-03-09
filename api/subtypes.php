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
require_once __DIR__.'/silex/vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

$app = new Application();

$app['debug'] = true;

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$columns = array('id', 'name');
$db = new Data($db, 'subtypes', 'id', $columns);

$app->get('/subtypes', function() use($db) {
  return json_encode($db->query());
});

$app->get('/subtypes/{id}', function ($id) use ($db) {
  return json_encode($db->query('id', $id, 1));
});

$app->post('/subtypes', function(Request $request) use($db) {
    $new = array(
        "name" => $request->get("name")
    );
    return json_encode($db->add($new));
});

$app->run();
