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

// Healthcheck for any monitoring software
$router->get('/healthz', 'DefaultController@healthz');

$router->group(['middleware' => ['cors']], function() use ($router) {
    // Set the CORS options that we will allow web requests from (This doesn't affect composer/console clients)
    $router->options('{path:.*}', 'DefaultController@cors');

    $router->group(['prefix' => 'package-group'], function() use ($router) {
        $router->get('/{name:[a-z]+}',  'PackageGroupController@findByName');
        $router->get('/{id:[0-9]+}',    'PackageGroupController@findById');
        $router->get('/',               'PackageGroupController@getList');
        $router->post('/',              'PackageGroupController@create');
        $router->put('/',               'PackageGroupController@update');
        $router->delete('/{id:[0-9]+}', 'PackageGroupController@deleteById');
    });

    $router->group(['prefix' => 'user'], function() use ($router) {
        $userRegEx = "[a-z0-9\-\_\@\.]+";

        // Perform an authorization attempt
        $router->post('/login',                 'LoginController@login');
        $router->get('/check',                  'LoginController@check');

        $router->get("/{name:$userRegEx}",      'UserController@findByUsername');
        $router->get('/{id:[0-9]+}',            'UserController@findById');
        $router->get('/',                       'UserController@getList');
        $router->post('/',                      'UserController@create');
        $router->put('/{id:[0-9]+}',            'UserController@update');
        $router->delete('/{id:[0-9]+}',         'UserController@deleteById');

        $router->post('/{id:[0-9]+}/token',     'AccessTokenController@add');
        $router->delete(
            '/{userId:[0-9]+}/token/{tokenId:[0-9]+}',
            'AccessTokenController@remove'
        );
    });

    $router->group(['prefix' => 'user-package-group'], function() use ($router) {
        $router->get('/user/{id:[0-9]+}',       'UserPackageGroupController@findByUserId');
        $router->get('/group/{id:[0-9]+}',      'UserPackageGroupController@findByPackageGroupId');
        $router->get('/',                       'UserPackageGroupController@getList');
        $router->post('/',                      'UserPackageGroupController@create');
        $router->delete(
            '/user/{userId:[0-9]+}/group/{groupId:[0-9]+}',
            'UserPackageGroupController@deleteMapping'
        );
        $router->delete('/user/{id:[0-9]+}',    'UserPackageGroupController@deleteByUserId');
        $router->delete('/group/{id:[0-9]+}',   'UserPackageGroupController@deleteByPackageGroupId');
    });
});
