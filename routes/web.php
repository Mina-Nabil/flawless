<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
//Sessions
Route::get('sessions/details/{id}', 'SessionsController@details');
Route::post('sessions/edit', 'SessionsController@edit');
Route::get('sessions/show/{state}', 'SessionsController@index');
Route::get('sessions/settle/balance/{id}', 'SessionsController@settleBalance');
Route::post('sessions/add/payment', 'SessionsController@acceptPayment');
Route::post('sessions/insert', 'SessionsController@insert');
Route::post('sessions/update/services', 'SessionsController@manageServices');
Route::post('sessions/set/discount', 'SessionsController@setDiscount');
Route::post('sessions/set/doctor', 'SessionsController@setDoctor');
Route::get('sessions/set/pending/{id}', 'SessionsController@setSessionPending');
Route::get('sessions/set/new/{id}', 'SessionsController@setSessionNew');
Route::get('sessions/set/done/{id}/{date?}', 'SessionsController@setSessionDone');
Route::get('sessions/set/cancelled/{id}', 'SessionsController@setSessionCancelled');
Route::post('sessions/api/get/services', 'SessionsController@getServices');
Route::get('sessions/query', "SessionsController@prepareQuery");
Route::post('sessions/query', "SessionsController@loadQuery");

//attendance
Route::get('attendance/home', 'AttendanceController@index');
Route::post('attendance/insert', 'AttendanceController@addAttendance');
Route::get('attendance/query', 'AttendanceController@prepareQuery');
Route::post('attendance/query', 'AttendanceController@loadQuery');
Route::post('attendance/set/state', 'AttendanceController@setAttendance');

//followups
Route::get('followups/home', 'FollowupsController@index');
Route::post('followups/insert', 'FollowupsController@insert');
Route::get('followups/query', 'FollowupsController@prepareQuery');
Route::post('followups/query', 'FollowupsController@loadQuery');
Route::post('followups/set/state', 'FollowupsController@setFollowup');

//feedbacks
Route::get('feedbacks/home', 'FeedbacksController@index');
Route::post('feedbacks/insert', 'FeedbacksController@insert');
Route::get('feedbacks/query', 'FeedbacksController@prepareQuery');
Route::post('feedbacks/query', 'FeedbacksController@loadQuery');
Route::post('feedbacks/set/state', 'FeedbacksController@setFeedback');


//Settings
Route::get('settings/devices', 'SettingsController@devices');
Route::get('settings/pricelists', 'SettingsController@pricelists');
Route::post('add/pricelist', 'SettingsController@addPricelist');
Route::post('edit/pricelist', 'SettingsController@editPricelist');
Route::post('delete/pricelist', 'SettingsController@deletePricelist');
Route::post('sync/pricelist/items', 'SettingsController@syncPricelist');
Route::post('get/pricelist/items', 'SettingsController@getPricelistItems');
Route::post('add/device', 'SettingsController@addDevice');
Route::post('edit/device', 'SettingsController@editDevice');
Route::post('delete/device', 'SettingsController@deleteDevice');
Route::post('add/area', 'SettingsController@addArea');
Route::post('edit/area', 'SettingsController@editArea');
Route::post('delete/area', 'SettingsController@deleteArea');

//Patients
Route::get('patients/home','PatientsController@home');
Route::get('patients/add', 'PatientsController@add');
Route::get('patients/profile/{id}', 'PatientsController@profile');
Route::post('patients/pay', 'PatientsController@pay');
Route::post('patients/insert', 'PatientsController@insert');
Route::post('patients/update', 'PatientsController@update');
Route::get('patients/get/json', 'PatientsController@getJSONPatients');

//Cash Account
Route::get("cash/home", 'CashController@home');
Route::post("cash/insert", 'CashController@insert');

//Visa Account
Route::get("visa/home", 'VisaController@home');
Route::post("visa/insert", 'VisaController@insert');

//Dashboard users
Route::get("dash/users/{userType}", 'DashUsersController@index');
Route::post("dash/users/insert", 'DashUsersController@insert');
Route::get("dash/users/edit/{id}", 'DashUsersController@edit');
Route::post("dash/users/update", 'DashUsersController@update');
Route::get("dash/users/toggle/{id}", 'DashUsersController@toggle');
Route::get("dash/users/delete/{id}", 'DashUsersController@delete');


Route::get('logout', 'HomeController@logout')->name('logout');
Route::get('/login', 'HomeController@login')->name('login');
Route::post('/login', 'HomeController@authenticate')->name('login');
Route::get('/home', 'SessionsController@index')->name('home');
Route::get('/', 'SessionsController@index')->name('home');
