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

$router->get('/', function () use ($router) {
    return response()->json(
		[
			'version' => '1.0',
			'name' => 'Unofficial Madison County Property Record Lookup',
			'official source' => 'https://property.madisoncounty.ny.gov/ImateWeb/search.aspx',
		]
	);
});

$router->group(['prefix' => 'api/v1'], function () use ($router) {
	// build admin routes
	$router->group(['prefix' => 'admin', 'middleware' => 'admin'], function () use ($router) {
		$router->get( '/populate', 'ReportController@get' );
		$router->get( '/reports', 'ReportController@process' );
	});

	// single property retrieval by ID
	$router->get(
		'/property/{id}',
		[ 'as' => 'singleProperty', 'uses' => 'PropertyController@show' ]
	);

	// multiple property retrievel by filter parameters
	$router->get(
		'/properties',
		[ 'as' => 'queryProperty', 'uses' => 'PropertyController@index' ]
	);
});
