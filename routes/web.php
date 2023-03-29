<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AvailabilityExceptionsController;
use App\Http\Controllers\BranchesController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\ChannelsController;
use App\Http\Controllers\DashUsersController;
use App\Http\Controllers\DoctorsAvailabilityController;
use App\Http\Controllers\FeedbacksController;
use App\Http\Controllers\FollowupsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\RoomsController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\SessionTypesController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\VisaController;
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
Route::get('sessions/details/{id}', [SessionsController::class, 'details']);
Route::post('sessions/edit', [SessionsController::class, 'edit']);
Route::get('sessions/show/{state}', [SessionsController::class, 'index']);
Route::get('sessions/settle/balance/{id}', [SessionsController::class, 'settleBalance']);
Route::get('sessions/settle/packages/{id}', [SessionsController::class, 'settlePackages']);
Route::post('sessions/add/payment', [SessionsController::class, 'acceptPayment']);
Route::post('sessions/insert', [SessionsController::class, 'insert']);
Route::post('sessions/update/services', [SessionsController::class, 'manageServices']);
Route::post('sessions/set/discount', [SessionsController::class, 'setDiscount']);
Route::post('sessions/set/doctor', [SessionsController::class, 'setDoctor']);
Route::get('sessions/set/pending/{id}', [SessionsController::class, 'setSessionPending']);
Route::get('sessions/set/new/{id}', [SessionsController::class, 'setSessionNew']);
Route::get('sessions/set/done/{id}/{date?}', [SessionsController::class, 'setSessionDone']);
Route::get('sessions/set/cancelled/{id}', [SessionsController::class, 'setSessionCancelled']);
Route::get('sessions/set/confirm/{id}', [SessionsController::class, 'confirmSession']);
Route::post('sessions/api/get/services', [SessionsController::class, 'getServices']);
Route::post('sessions/api/get/duration', [SessionsController::class, 'getServicesDuration']);
Route::get('sessions/query', [SessionsController::class, 'prepareQuery']);
Route::post('sessions/query', [SessionsController::class, 'loadQuery']);
Route::get('sessions/delete/{id}', [SessionsController::class, 'delete']);
Route::post('sessions/api', [SessionsController::class, 'getSessionsAPI']);

//reports
Route::get('reports/doctors', [ReportsController::class, 'prepareDoctorQuery']);
Route::post('reports/doctors', [ReportsController::class, 'loadDoctorData']);
Route::get('reports/doctors/services', [ReportsController::class, 'prepareDoctorServicesQuery']);
Route::post('reports/doctors/services', [ReportsController::class, 'loadDoctorServicesData']);
Route::get('reports/patients', [ReportsController::class, 'preparePatients']);
Route::post('reports/patients', [ReportsController::class, 'loadPatients']);
Route::get('reports/cash', [CashController::class, 'query']);
Route::post('reports/cash', [CashController::class, 'loadQuery']);
Route::get('reports/visa', [VisaController::class, 'query']);
Route::post('reports/visa', [VisaController::class, 'loadQuery']);
Route::get('reports/revenue', [ReportsController::class, 'prepareRevenue']);
Route::post('reports/revenue', [ReportsController::class, 'loadRevenue']);
Route::get('reports/devices', [ReportsController::class, 'prepareDevicesRevenue']);
Route::post('reports/devices', [ReportsController::class, 'loadDevicesRevenue']);
Route::get('reports/missing', [ReportsController::class, 'prepareMissingPatients']);
Route::post('reports/missing', [ReportsController::class, 'loadMissingPatients']);
Route::get('reports/fromwhere', [ReportsController::class, 'preparePatientsByBranch']);
Route::post('reports/fromwhere', [ReportsController::class, 'loadPatientsByBranch']);
Route::get('reports/toppayers', [ReportsController::class, 'prepareTopPayers']);
Route::post('reports/toppayers', [ReportsController::class, 'loadTopPayers']);
Route::get('reports/newpatients', [ReportsController::class, 'prepareNewPatients']);
Route::post('reports/newpatients', [ReportsController::class, 'loadNewPatients']);

//attendance
Route::get('attendance/home', [AttendanceController::class, 'index']);
Route::get('schedule', [AttendanceController::class, 'schedule']);
Route::post('attendance/insert', [AttendanceController::class, 'addAttendance']);
Route::get('attendance/query', [AttendanceController::class, 'prepareQuery']);
Route::post('attendance/query', [AttendanceController::class, 'loadQuery']);
Route::post('attendance/set/state', [AttendanceController::class, 'setAttendance']);

//followups
Route::get('followups/home', [FollowupsController::class, 'index']);
Route::post('followups/insert', [FollowupsController::class, 'insert']);
Route::get('followups/query', [FollowupsController::class, 'prepareQuery']);
Route::post('followups/query', [FollowupsController::class, 'loadQuery']);
Route::post('followups/set/state', [FollowupsController::class, 'setFollowup']);

//feedbacks
Route::get('feedbacks/home', [FeedbacksController::class, 'index']);
Route::post('feedbacks/insert', [FeedbacksController::class, 'insert']);
Route::get('feedbacks/query', [FeedbacksController::class, 'prepareQuery']);
Route::post('feedbacks/query', [FeedbacksController::class, 'loadQuery']);
Route::post('feedbacks/set/state', [FeedbacksController::class, 'setFeedback']);

//Exceptions
Route::get('exceptions', [AvailabilityExceptionsController::class, 'exceptions']);
Route::post('exceptions', [AvailabilityExceptionsController::class, 'addException']);
Route::get('exceptions/delete/{id}', [AvailabilityExceptionsController::class, 'deleteException']);

//DoctorAvailability
Route::post('availabilities/doctors/check', [DoctorsAvailabilityController::class, 'getDoctorAvailability']);
Route::get('availabilities', [DoctorsAvailabilityController::class, 'availabilities']);
Route::post('availabilities', [DoctorsAvailabilityController::class, 'addDoctorAvailability']);
Route::get('availabilities/{id}', [DoctorsAvailabilityController::class, 'availability']);
Route::post('availabilities/update/{id}', [DoctorsAvailabilityController::class, 'updateDoctorAvailability']);
Route::get('availabilities/delete/{id}', [DoctorsAvailabilityController::class, 'deleteAvailability']);

//SessionTypes
Route::get('sessiontypes', [SessionTypesController::class, 'sessiontypes']);
Route::post('sessiontypes', [SessionTypesController::class, 'addSessionType']);
Route::get('sessiontypes/{id}', [SessionTypesController::class, 'sessiontype']);
Route::post('sessiontypes/update/{id}', [SessionTypesController::class, 'updateSessionType']);
Route::get('sessiontypes/toggle/{id}', [SessionTypesController::class, 'sessiontypeState']);

//Rooms
Route::get('rooms', [RoomsController::class, 'rooms']);
Route::post('rooms', [RoomsController::class, 'addRoom']);
Route::get('rooms/{id}', [RoomsController::class, 'room']);
Route::post('rooms/update/{id}', [RoomsController::class, 'updateRoom']);
Route::get('rooms/toggle/{id}', [RoomsController::class, 'roomState']);

//Settings
Route::get('settings/devices', [SettingsController::class, 'devices']);
Route::get('settings/pricelists', [SettingsController::class, 'pricelists']);
Route::post('add/pricelist', [SettingsController::class, 'addPricelist']);
Route::post('edit/pricelist', [SettingsController::class, 'editPricelist']);
Route::post('delete/pricelist', [SettingsController::class, 'deletePricelist']);
Route::post('sync/pricelist/items', [SettingsController::class, 'syncPricelist']);
Route::post('get/pricelist/items', [SettingsController::class, 'getPricelistItems']);
Route::post('add/device', [SettingsController::class, 'addDevice']);
Route::post('edit/device', [SettingsController::class, 'editDevice']);
Route::post('delete/device', [SettingsController::class, 'deleteDevice']);
Route::post('add/area', [SettingsController::class, 'addArea']);
Route::post('edit/area', [SettingsController::class, 'editArea']);
Route::post('delete/area', [SettingsController::class, 'deleteArea']);

//Patients
Route::get('patients/home',[PatientsController::class, 'home']);
Route::get('patients/add', [PatientsController::class, 'add']);
Route::get('patients/profile/{id}', [PatientsController::class, 'profile']);
Route::post('patients/pay', [PatientsController::class, 'pay']);
Route::post('patients/setnote', [PatientsController::class, 'setNote']);
Route::get('patients/notes/delete/{id}', [PatientsController::class, 'deleteNote']);
Route::get('patients/notes/restore/{id}', [PatientsController::class, 'restoreNote']);
Route::post('patients/addbalance', [PatientsController::class, 'addBalance']);
Route::post('patients/add/package', [PatientsController::class, 'addPackage']);
Route::post('patients/insert', [PatientsController::class, 'insert']);
Route::post('patients/update', [PatientsController::class, 'update']);
Route::get('patients/get/json', [PatientsController::class, 'getJSONPatients']);

//Cash Account
Route::get("cash/home", [CashController::class, 'home']);
Route::post("cash/insert", [CashController::class, 'insert']);

//Visa Account
Route::get("visa/home", [VisaController::class, 'home']);
Route::post("visa/insert", [VisaController::class, 'insert']);

///Stock
Route::get("stock", [StockController::class, 'stock']);
Route::get("stock/transactions", [StockController::class, 'transactions']);
Route::get("stock/transaction/{code}", [StockController::class, 'transaction']);
Route::get("stock/entry", [StockController::class, 'entry']);
Route::post("stock/entry", [StockController::class, 'insertEntry']);

//Stock items
Route::get("stockitems/home", [StockController::class, 'items']);
Route::post("stockitems/insert", [StockController::class, 'insertItem']);
Route::get("stockitems/edit/{id}", [StockController::class, 'item']);
Route::get("stockitems/toggle/{id}", [StockController::class, 'toggleItem']);
Route::post("stockitems/update", [StockController::class, 'updateItem']);

//Stock items
Route::get("stocktypes/home", [StockController::class, 'stocktypes']);
Route::post("stocktypes/insert", [StockController::class, 'insertType']);
Route::get("stocktypes/edit/{id}", [StockController::class, 'stocktype']);
Route::post("stocktypes/update", [StockController::class, 'updateType']);

//Branches
Route::get("branches/home", [BranchesController::class, 'index']);
Route::post("branches/insert", [BranchesController::class, 'insert']);
Route::get("branches/edit/{id}", [BranchesController::class, 'edit']);
Route::post("branches/update", [BranchesController::class, 'update']);

//Locations
Route::get("locations/home", [LocationsController::class, 'index']);
Route::post("locations/insert", [LocationsController::class, 'insert']);
Route::get("locations/edit/{id}", [LocationsController::class, 'edit']);
Route::post("locations/update", [LocationsController::class, 'update']);
Route::get("locations/delete/{id}", [LocationsController::class, 'delete']);

//Channels
Route::get("channels/home", [ChannelsController::class, 'index']);
Route::post("channels/insert", [ChannelsController::class, 'insert']);
Route::get("channels/edit/{id}", [ChannelsController::class, 'edit']);
Route::post("channels/update", [ChannelsController::class, 'update']);
Route::get("channels/delete/{id}", [ChannelsController::class, 'delete']);

//Dashboard users
Route::get("dash/users/{userType}", [DashUsersController::class, 'index']);
Route::post("dash/users/insert", [DashUsersController::class, 'insert']);
Route::get("dash/users/edit/{id}", [DashUsersController::class, 'edit']);
Route::post("dash/users/update", [DashUsersController::class, 'update']);
Route::get("dash/users/toggle/{id}", [DashUsersController::class, 'toggle']);
Route::get("dash/users/delete/{id}", [DashUsersController::class, 'delete']);

Route::get("set/branch/{id}", [HomeController::class, "setBranch"]);

Route::post('payments/modal/add', [HomeController::class, 'addPayment']);
Route::post('message', [HomeController::class, 'sendMessage'])->name('sendMessage');
Route::get('calendar', [SessionsController::class, 'calendar'])->name('calendar');
Route::get('calendar/{room}', [SessionsController::class, 'calendar'])->name('calendar');
Route::post('search', [HomeController::class, 'search'])->name('search');
Route::get('logout', [HomeController::class, 'logout'])->name('logout');
Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::post('/login', [HomeController::class, 'authenticate'])->name('login');
Route::get('/home', [SessionsController::class, 'index'])->name('home');
Route::get('/', [SessionsController::class, 'index'])->name('home');
