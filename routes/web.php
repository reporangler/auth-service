<?php
use \Illuminate\Http\JsonResponse;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['middleware' => ['cors']], function() use ($router) {
    // Set the CORS options that we will allow web requests from (This doesn't affect composer/console clients)
    $router->options('{path:.*}', 'Controller@cors');

    // Healthcheck for any monitoring software
    $router->get('/healthz', 'Controller@healthz');
});
