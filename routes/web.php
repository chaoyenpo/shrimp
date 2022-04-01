<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//activity
Route::get('activities', 'Web\ActivityController@index');
Route::get('activities/rule/{page}', 'Web\ActivityController@rule');
Route::get('activities/game/{type}', 'Web\ActivityController@game');
Route::get('activities/game/{type}/{status}', 'Web\ActivityController@game');
Route::get('activities/leaderboard/{type}', 'Web\ActivityController@leaderboard');


$router->group(['middleware' => ['login']], function() use ($router)
{
	Route::get('/', 'Web\ShrimpFarmEventController@index');
	Route::get('migrate', 'Web\HomeController@index');
	Route::resource('shrimpFarm', 'Web\ShrimpFarmController', ['except'  => ['show','destory']]);
	Route::delete('shrimpFarm/Evaluation/reset', 'Web\ShrimpFarmController@resetEvaluation');
	Route::get('shrimpFarmEvent/create/{id}', 'Web\ShrimpFarmEventController@create');
	Route::resource('shrimpFarmEvent', 'Web\ShrimpFarmEventController', ['except'  => ['show','destory']]);
	Route::resource('fishingTackleShop', 'Web\FishingTackleShopController', ['except'  => ['show','destory']]);
	Route::resource('ad', 'Web\AdController', ['except'  => ['show','destory']]);
	Route::resource('illustration', 'Web\IllustrationController', ['except'  => ['show']]);
	Route::resource('game', 'Web\GameController');
	Route::resource('full-tw-game', 'Web\FullTwGameController');
	
	Route::get('game/{id}/pushPending', 'Web\GameController@pushPending');
	Route::get('game/{id}/personnel', 'Web\GameController@editPersonnel');
	Route::post('game/{id}/personnel', 'Web\GameController@updatePersonnel');

	Route::resource('point/add', 'Web\AddPointController');
	Route::resource('point/sub', 'Web\SubPointController');
});

Route::get('full-tw-game-view', 'Web\FullTwGameController@view');

Route::get('ShrimpKing/privacy', 'Web\HomeController@privacy');
Route::get('work/target.php', 'Web\HomeController@download');
Route::get('login', 'Web\LoginController@index');
Route::post('login', 'Web\LoginController@auth');
