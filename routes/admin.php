<?php

Route::get('/login', 'Admin\LoginController@showLoginForm')->middleware('admin.guest');

Route::post('/logout', 'Admin\LoginController@logout');

Route::post('/login', 'Admin\LoginController@login')->name("adminLogin");

Route::group(['middleware' => 'auth.admin'], function () {

    Route::get('/', 'Admin\AdminController@index');

    Route::get('/transactions/usd-withdraw/export', 'Admin\AdminController@exportUsdTransactionsToExcel');

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


        Route::get('/user-transactions', 'Admin\AdminController@getUserTransactions');

        Route::get('/user-balances', 'Admin\AdminController@getUserBalances');

        Route::post('/user-balances/update', 'Admin\AdminController@updateUserBalance');

        Route::group(['prefix' => 'bounties', 'namespace' => 'Admin'], function () {
            Route::get('/' , 'AdminController@getBounties');
            Route::get('detail' , 'AdminController@getUserBountyHistory');
            Route::get('reports' , 'AdminController@getReport');
        });

        Route::get('/transactions', 'Admin\AdminController@getTransactions');

        Route::post('/user/update', 'Admin\AdminController@updateUser');

        Route::group(['prefix' => 'site-settings', 'namespace' => 'Admin'], function () {
            Route::get('/' , 'SiteSettingController@getSiteSettings');
            Route::post('update' , 'SiteSettingController@updateSettingSite');
        });

        Route::group(['prefix' => 'socical-networks', 'namespace' => 'Admin'], function () {
            Route::get('/', 'SiteSettingController@getSocialNetworks');
            Route::post('/', 'SiteSettingController@addSocialNetwork');
            Route::post('/update', 'SiteSettingController@updateSocialNetWork');
            Route::delete('/{id}', 'SiteSettingController@removeSocialNetwork');
        });

        Route::group(['prefix' => 'template-mail', 'namespace' => 'Admin'], function() {
            Route::get('/', 'AdminController@getTemplateMails');
            Route::get('/edit/{id}', 'AdminController@editTemplateMail');
            Route::post('/create', 'AdminController@createTemplateMail');
            Route::post('/update', 'AdminController@updateTemplateMail');
            Route::post('/delete/{id}', 'AdminController@deleteTemplateMail');
            Route::post('/{id}/send', 'AdminController@sendMails');
            Route::post('/{id}/history', 'AdminController@getMarketingMailsHistory');
            Route::get('/resend', 'AdminController@resendMail');
        });

        Route::get('user/{userId}/devices', 'API\UserAPIController@getDeviceRegister');

        Route::delete('user/{userId}/device/{id}', 'API\UserAPIController@deleteDevice');

        Route::group(['prefix' => 'setting', 'namespace' => 'Admin'], function () {
            Route::get('/bounty-type' , 'SettingController@getBountyType');
            Route::get('/platform' , 'SettingController@getPlatform');
            Route::get('/offer' , 'SettingController@getOffers');
            Route::post('/bounty-type/create' , 'SettingController@createNewBountyType');
            Route::put('/bounty-type/update', 'SettingController@updateBountyType');
            Route::post('/bounty-type/remove' , 'SettingController@removeBountyType');
            Route::post('/platform/create' , 'SettingController@createNewPlatform');
            Route::post('/platform/update', 'SettingController@updatePlatform');
            Route::post('/platform/remove' , 'SettingController@removePlatform');
            Route::post('/offer/create' , 'SettingController@createOffer');
            Route::post('/offer/update', 'SettingController@updateOffer');
            Route::post('/offer/remove' , 'SettingController@removeOffer');
        });
        Route::group(['prefix' => 'game', 'namespace' => 'Admin'], function() {
            Route::get('/', 'GameController@getGames');
            Route::get('/{id}/edit', 'GameController@editGame');
            Route::post('/create', 'GameController@createGame');
            Route::post('/update', 'GameController@updateGame');
            Route::post('/{id}/delete', 'GameController@deleteGame');
        });

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
