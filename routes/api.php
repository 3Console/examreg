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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/oauth/token', [
    'uses' => 'API\Auth\LoginAPIController@issueToken',
    'middleware' => 'throttle:6000|6000,1',
]);

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('broadcasting/auth', ['uses' => '\Illuminate\Broadcasting\BroadcastController@authenticate']);

    Route::group(['prefix' => 'user'], function() {
        Route::get('/profile', 'API\UserAPIController@getProfile');
        Route::get('/schedules', 'API\UserAPIController@getUserSchedules');
        Route::get('/schedules/{id}/detail', 'API\UserAPIController@getScheduleDetail');
    });

    Route::group(['prefix' => 'exam-register'], function() {
        Route::get('/semesters', 'API\ExamRegisterAPIController@getSemesters');
        Route::get('/semesters/detail', 'API\ExamRegisterAPIController@getSemesterDetail');
    });
});
