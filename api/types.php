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
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app['debug'] = true;

$app->before(function (Symfony\Component\HttpFoundation\Request $request) {
  if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
    $data = json_decode($request->getContent(), true);
    $request->request->replace(is_array($data) ? $data : array());
  }
});

$columns = array('id', 'name');
$db = new Data($db, 'types', 'id', $columns);

$app->get('/types', function() use($db, $columns) {
  return json_encode($db->query());
});

$app->get('/types/{id}', function($id) use ($db, $columns) {
  return json_encode($db->get('id', $id, 1));
});

$app->post('/types', function(Silex\Application $app, Symfony\Component\HttpFoundation\Request $request) use($db) {
  $data = $request->request->all();
  $id = $db->add($data);
  $data['id'] = "$id";
  return json_encode($data);
});

$app->delete('/types/{id}', function($id) use($db){
  return json_encode($db->del($id));
});

$app->run();

?>
