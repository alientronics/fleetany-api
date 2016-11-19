<?php

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

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->group([
        'prefix' => 'api/v1',
        'namespace' => 'App\Http\Controllers',
        'middleware' => ['cors', 'auth']
    ], function ($app) {

        $app->get('company', 'CompanyController@index');
  
        $app->get('company/{id}', 'CompanyController@getCompany');

        //$app->post('company','CompanyController@createCompany');
      
        $app->put('company/{id}', 'CompanyController@updateCompany');
      
        //$app->delete('company/{id}','CompanyController@deleteCompany');

        $app->get('user', 'UserController@index');
        $app->post('user', 'UserController@findOrCreateUser');

        $app->get('trip', 'TripController@index');
        $app->post('trip', 'TripController@create');

        $app->post('gps', 'GpsController@create');

        $app->post('tiresensor', 'TireSensorController@create');
    });
