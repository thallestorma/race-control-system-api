<?php

use Illuminate\Http\Request;

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


Route::get('/runners', 'RunnerController@findAll');
Route::post('/runners', 'RunnerController@insert');

Route::get('/races', 'RaceController@listGeneralResult');
Route::get('/races/list-result-by-age', 'RaceController@listResultByAge');

Route::post('/races', 'RaceController@insert');
Route::post('/races/add-runner', 'RaceController@addRunner');
Route::post('/races/add-results', 'RaceController@insertResults');
