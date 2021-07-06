<?php
Route::group(['namespace' => 'Workable\RequestLogging\App\Controllers'], function(){
    Route::get('robots/counter',[
        'as' => 'api.robots.counter',
        'uses' => 'RobotsCounterController@index'
    ]);
});