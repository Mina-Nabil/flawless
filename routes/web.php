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

//Cash Account
Route::get("cash/home", 'CashController@home');
Route::post("cash/insert", 'CashController@insert');

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
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', 'HomeController@index')->name('home');
