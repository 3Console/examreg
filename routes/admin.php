<?php

Route::get('/login', 'Admin\LoginController@showLoginForm')->middleware('admin.guest');

Route::post('/logout', 'Admin\LoginController@logout');

Route::post('/login', 'Admin\LoginController@login')->name("adminLogin");

Route::group(['middleware' => 'auth.admin'], function () {

    Route::get('/', 'Admin\AdminController@index');

    Route::group(['prefix' => 'api'], function () {

        Route::group(['prefix' => 'administrators'], function () {
            Route::get('/', 'Admin\AdminController@getAdministrators');
            Route::get('/{id}', 'Admin\AdminController@getAdministratorById');
            Route::post('/create', 'Admin\AdminController@createNewOrUpdateAdministrator');
            Route::post('/update', 'Admin\AdminController@createNewOrUpdateAdministrator');
            Route::delete('delete', 'Admin\AdminController@deleteAdministrator');
        });

        // Route::post('/clear-cache', 'ToolController@clearCache');

        Route::get('/user', 'Admin\AdminController@getCurrentAdmin');

        Route::get('/users', 'API\UserAPIController@users');

        Route::get('/users2', 'Admin\AdminController@getUsers');

        Route::post('/user/upload', 'Admin\AdminController@uploadCSV');

        Route::post('/user/update', 'Admin\AdminController@updateUser');

        Route::group(['prefix' => 'semesters', 'namespace' => 'Admin'], function() {
            Route::get('/', 'SemesterController@getSemesters');
            Route::post('/create' , 'SemesterController@createSemester');
            Route::post('/update', 'SemesterController@updateSemester');
            Route::post('/remove' , 'SemesterController@removeSemester');
            Route::get('/{id}/detail', 'SemesterController@getSemester');
            Route::get('/class', 'SemesterController@getSemesterClass');
            Route::post('/create-class' , 'SemesterController@createSemesterClass');
            Route::post('/update-class', 'SemesterController@updateSemesterClass');
            Route::post('/remove-class' , 'SemesterController@removeSemesterClass');
        });

        Route::group(['prefix' => 'classes', 'namespace' => 'Admin'], function() {
            Route::get('/', 'ClassController@getClasses');
            Route::get('/all', 'ClassController@getAllClass');
            Route::post('/create' , 'ClassController@createClass');
            Route::post('/update', 'ClassController@updateClass');
            Route::post('/remove' , 'ClassController@removeClass');
            Route::get('/{id}/detail', 'ClassController@getUnitClass');
            Route::get('/user', 'ClassController@getUserClass');
            Route::post('/create-student' , 'ClassController@createStudent');
            Route::post('/update-student', 'ClassController@updateStudent');
            Route::post('/remove-student' , 'ClassController@removeStudent');
            Route::post('/upload', 'ClassController@uploadStudentCSV');
        });

        Route::group(['prefix' => 'schedules', 'namespace' => 'Admin'], function() {
            Route::get('/', 'ScheduleController@getSchedules');
            Route::post('/create' , 'ScheduleController@createSchedule');
            Route::post('/update', 'ScheduleController@updateSchedule');
            Route::post('/remove' , 'ScheduleController@removeSchedule');
            Route::get('/{id}/detail', 'ScheduleController@getSchedule');
            Route::get('/{id}/students', 'ScheduleController@getStudents');
        });

        Route::group(['prefix' => 'shifts', 'namespace' => 'Admin'], function() {
            Route::get('/', 'ShiftController@getShifts');
            Route::get('/all', 'ShiftController@getAllShift');
            Route::post('/create' , 'ShiftController@createShift');
            Route::post('/update', 'ShiftController@updateShift');
            Route::post('/remove' , 'ShiftController@removeShift');
        });

        Route::group(['prefix' => 'locations', 'namespace' => 'Admin'], function() {
            Route::get('/', 'LocationController@getLocations');
            Route::get('/all', 'LocationController@getAllLocation');
            Route::post('/create' , 'LocationController@createLocation');
            Route::post('/update', 'LocationController@updateLocation');
            Route::post('/remove' , 'LocationController@removeLocation');
        });
    });

    Route::get('/{view?}', 'Admin\AdminController@index')->where('view', '(.*)');
});
