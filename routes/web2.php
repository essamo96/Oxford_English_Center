<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::group(['namespace' => 'App\Http\Controllers\Admin', 'prefix' => 'admin', 'middleware' => ['guest:admin']], function () {
    Route::get('login', ['as' => 'login', 'uses' => 'LoginController@getIndex']);
    Route::post('login', ['as' => 'login.post', 'uses' => 'LoginController@postIndex']);
});
Route::get('/admin', function () {
    return redirect('admin/dashboard');
});
Route::group(['namespace' => 'App\Http\Controllers\Admin', 'prefix' => 'admin', 'middleware' => ['auth:admin']], function () {

    // Route::get('lang/{lang}', ['as' => 'dashboard.lang', 'uses' => 'DashboardController@getLang']);
    Route::get('dashboard', ['as' => 'dashboard.view', 'middleware' => ['permission:admin.dashboard.view'], 'uses' => 'DashboardController@getIndex']);
    Route::get('main_dashboard', ['as' => 'main_dashboard.view', 'middleware' => ['permission:admin.main_dashboard.view'], 'uses' => 'DashboardController@getIndex']);
    Route::get('users_menu/', ['as' => 'users_menu.view', 'uses' => 'DashboardController@getIndex']);
    Route::get('system_settings/', ['as' => 'system_settings.view', 'uses' => 'DashboardController@getIndex']);
    Route::get('logout', ['as' => 'app.logout', 'uses' => 'LoginController@getLogout']);



    //Users Route
    require __DIR__ . '/admins.php';

    //Roles Route
    require __DIR__ . '/roles.php';

    //Settings Route
    require __DIR__ . '/settings.php';

    //Pages Route
    require __DIR__ . '/pages.php';

    //permissions Route
    require __DIR__ . '/permissions.php';

    //permissions_group Route
    require __DIR__ . '/permissions_group.php';

   
});
