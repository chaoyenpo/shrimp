<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Mobile'], function() {
    Route::group(['prefix' => 'member'], function() {
        Route::post('/', 'MemberController@store');
        Route::get('check', 'MemberController@check');
        Route::post('login', 'MemberController@login');
    });

    Route::group(['prefix' => 'ecpay'], function() {
        Route::post('notify', 'ECPayController@notifyUrl')->name('ecpay.notify');
        Route::post('return', 'ECPayController@returnUrl')->name('ecpay.return');
    });

    Route::group(['prefix' => 'shrimp_farm'], function() {
        Route::get('/', 'ShrimpFarmController@index');
        Route::get('{id}', 'ShrimpFarmController@show');
    });
    Route::get('ad', 'AdController@index');
    Route::get('illustration', 'IllustrationController@index');
    Route::group(['prefix' => 'booth', 'middleware' => ['BoothMiddleware']], function() {
        Route::get('/', 'BoothController@index');
    });

    Route::group(['prefix' => 'game'], function() {
        Route::get('profile', 'GameController@profile');
        Route::get('profile/{id}', 'GameController@showProfile');
        Route::get('rank/{type}', 'GameController@showRank');
        Route::get('{identifier}', 'GameController@show');
    });
});

Route::group(['namespace' => 'Mobile', 'middleware' => ['auth:api']], function() {
    Route::group(['prefix' => 'member'], function() {
        Route::post('logout', 'MemberController@logout');
        Route::get('point', 'MemberController@listPoint');
        Route::get('likeFarms', 'MemberController@listLikeFarms');
        Route::put('pushSwitcher', 'MemberController@updatePushSwitcher');
        Route::get('{id}', 'MemberController@show');
        Route::put('{id}', 'MemberController@update');
    });
    Route::put('admin/member', 'Admin\MemberController@changePower');

    Route::group(['prefix' => 'game'], function() {
        Route::post('{identifier}/forceCancel', 'GameController@forceCancel');
        Route::get('{identifier}/signup', 'GameController@showSignupForm');
        Route::post('{identifier}/signup', 'GameController@handleSignup');
        Route::post('{identifier}/autosignup', 'GameController@autosignup');
        Route::post('{identifier}/resetSignup', 'GameController@resetSignup');
        Route::post('{identifier}/hostquota', 'GameController@handleHostquota');
        Route::put('{identifier}/checkin', 'GameController@checkin');
        Route::post('{identifier}/random', 'GameController@random');
        Route::put('{identifier}/lockNumber', 'GameController@lockNumber');
        Route::put('{identifier}/point', 'GameController@updatePoint');
        Route::put('{identifier}/autopoint', 'GameController@autopoint');
        Route::put('{identifier}/rank', 'GameController@updateRank');
        Route::put('{identifier}/pk', 'GameController@updatePK');
        Route::put('{identifier}/integral', 'GameController@updateIntegral');
        Route::put('{identifier}/end', 'GameController@end');
    });

    Route::group(['prefix' => 'ecpay'], function() {
        Route::get('/', 'ECPayController@index');
        Route::post('/', 'ECPayController@pay');
        Route::post('notify', 'ECPayController@notifyUrl')->name('ecpay.notify');
        Route::post('return', 'ECPayController@returnUrl')->name('ecpay.return');
    });

    Route::group(['prefix' => 'shrimp_farm'], function() {
        Route::post('{id}/evaluate', 'ShrimpFarmController@evaluate');
        Route::post('{id}/like', 'ShrimpFarmController@like');
    });

    Route::group(['prefix' => 'booth', 'middleware' => ['BoothMiddleware']], function() {
        Route::get('seen', 'BoothController@getViews');
        Route::get('{id}', 'BoothController@show');
        Route::get('{id}/evaluations', 'BoothController@showEvaluations');
        Route::put('{id}', 'BoothController@update');
        Route::post('/', 'BoothController@store');
        Route::put('{id}/see/{order_id}', 'BoothController@confirm');
        Route::post('{id}/see', 'BoothController@see');
        Route::post('{id}/evaluate/{order_id}', 'BoothController@evaluate');
    });

    Route::group(['prefix' => 'ad'], function() {
        Route::put('{id}/url', 'AdController@updateUrl');
        Route::put('{id}/image', 'AdController@updateImage');
        Route::post('/', 'AdController@store');
    });

    Route::group(['prefix' => 'transfer'], function() {
        Route::post('/point', 'TransferPointController@transferPoint');
    });

    Route::group(['prefix' => 'recycler'], function() {
        Route::post('/shrimp', 'RecyclerController@recycleShrimp');
    });
});
