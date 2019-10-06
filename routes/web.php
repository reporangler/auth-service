<?php
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Router;
use RepoRangler\Entity\User;

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

// Healthcheck for any monitoring software
$router->get('/healthz', 'DefaultController@healthz');

$router->group(['middleware' => ['cors']], function() use ($router) {
    // Set the CORS options that we will allow web requests from (This doesn't affect composer/console clients)
    $router->options('{path:.*}', 'DefaultController@cors');

    $router->group(['prefix' => 'login'], function() use ($router) {
        $router->get('/api', ['middleware' => 'auth:login', function(Request $request) {
            return new JsonResponse($request->user('login'));
        }]);

        $router->get('/token', ['middleware' => 'auth:token', function(Request $request) {
            return new JsonResponse($request->user('token'));
        }]);
    });

    // All these endpoints require a token to access
    $router->group(['middleware' => 'auth:token'], function() use ($router){
        $router->group(['prefix' => 'user'], function() use ($router){
            $router->get('/{name:'.User::PATTERN.'}',   'UserController@findByUsername');
            $router->get('/{userId:[0-9]+}',            'UserController@findById');
            $router->get('/',                           'UserController@getList');
            $router->post('/',                          'UserController@create');
            $router->put('/{userId:[0-9]+}',            'UserController@update');
            $router->delete('/{userId:[0-9]+}',         'UserController@deleteById');

            $router->post('/{userId:[0-9]+}/package-group/',                    'UserController@createMapping');
            $router->delete('/{userId:[0-9]+}/package-group/{groupId:[0-9]+}',  'UserController@deleteMapping');
        });

        $router->group(['prefix' => 'access-token'], function() use ($router){
            $router->get('/{userId:[0-9]+}',                        'AccessTokenController@findByUserId');
            $router->post('/{userId:[0-9]+}',                       'AccessTokenController@add');
            $router->delete('/{userId:[0-9]+}/{tokenId:[0-9]+}',    'AccessTokenController@remove');
        });

        $router->group(['prefix' => 'package-group'], function() use ($router) {
            $router->get('/name/{name:[a-z\-\.]+}', 'PackageGroupController@findByName');
            $router->get('/id/{id:[0-9]+}',         'PackageGroupController@findById');
            $router->get('/',                       'PackageGroupController@getList');
            $router->post('/',                      'PackageGroupController@create');
            $router->put('/',                       'PackageGroupController@update');
            $router->delete('/{id:[0-9]+}',         'PackageGroupController@deleteById');
        });
    });
});
