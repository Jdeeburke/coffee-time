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
    return view('index');
});

$app->get('/subscribe', function() use($app) {
	return view('subscribe');
});

$app->get('/new', 'CoffeeController@startBrew');
$app->get('/latest', 'CoffeeController@getLatestBrew');
$app->get('/notify', 'CoffeeController@notify');
$app->post('/subscribe', 'SubscriptionController@subscribe');
$app->post('/slack/latest', 'CoffeeController@slackLatest');
$app->post('/slack/notification', 'CoffeeController@slackAddNotification');