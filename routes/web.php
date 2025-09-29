<?php

use App\Http\Controllers\SuperAdmin\BackupController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AutoLoginController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\DemoRequestController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PythonAuth;
use App\Http\Controllers\BookingFollowUp;


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

Route::get('/', [HomeController::class, 'index']);
Route::get('loader', function () {
    return view('property.layouts.loader');
});
// Route::get('login', [HomeController::class, 'login']);
Route::post('/loginpy', [PythonAuth::class, 'login']);
Auth::routes();

Route::get('/storage-link', function () {
    $target_folder = storage_path('app/public');
    $link_folder = public_path('storage');
    if (!file_exists($link_folder)) {
        symlink($target_folder, $link_folder);
    }
});

// routes/web.php
Route::post('/auto-login', [AutoLoginController::class, 'loginUser'])->name('auto.login');

Route::get('application', [HomeController::class, 'application'])->name('application');
// Redirects to Admin Home
Route::get('superadmin', [MainController::class, 'index'])->name('superadmin')->middleware('superadmin');
// Logout Admin
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
// Company Register Page
Route::get('/companyreg', [MainController::class, 'companyregister']);
// Load State
Route::post('/getState', [MainController::class, 'getState']);
//Check Mobile
Route::post('/check_mobile', [MainController::class, 'check_mobile']);
//Check Email
Route::post('/check_email', [MainController::class, 'check_email']);
//Check Username
Route::post('/check_username', [MainController::class, 'check_username']);
// Check sn_num
Route::post('/check_sn_num', [MainController::class, 'check_Sno']);
// Submit Company Registration Form
Route::post('companystore', [MainController::class, 'store'])->name('companystore');
// Open Company List
Route::get('/companylist', [MainController::class, 'loadcompanylist']);
// Update property
Route::get('updatepropertyadmin', [MainController::class, 'openUpdateProperty'])->name('propertyid');
// Disable Property
Route::get('disablepropertyadmin', [MainController::class, 'Disablepropertyadmin'])->name('propertyid');
// Enable Property
Route::get('enablepropertyadmin', [MainController::class, 'enableproperty'])->name('propertyid');
// Company Update
Route::post('/', [MainController::class, 'companyupdate'])->name('company.update');
//City Form
Route::get('/cityform', [MainController::class, 'opencity']);
// Check City name
Route::post('/check_city_name', [MainController::class, 'check_city_name']);
//Check zipcode
Route::post('/check_zipcode', [MainController::class, 'check_zipcode']);
// Load State2
Route::post('/getStateadmin', [MainController::class, 'getStateadmin']);
// Submit City Form
Route::post('citystore', [MainController::class, 'submitcity'])->name('citystore');
// State Form
route::get('/stateform', [MainController::class, 'openstate']);
// Country Form
Route::post('/check_state_insert', [MainController::class, 'check_state_insert']);
//Check state code
Route::post('/check_state_code', [MainController::class, 'check_state_code']);
// Submit State Form
route::post('statestore', [MainController::class, 'submitstate'])->name('statestore');
// Check Country name
Route::post('/check_country', [MainController::class, 'check_country']);
//Check country_code
Route::post('/check_country_code', [MainController::class, 'check_country_code']);
// Country Form
route::get('/countryform', [MainController::class, 'opencountry']);
// Submit Country Form
route::post('countrystore', [MainController::class, 'submitcountry'])->name('countrystore');
// Property Login
Route::get('company', [MainController::class, 'loadProperty'])->name('company')->middleware('company');
// Update Country Form Open
route::get('updatecountryadmin', [MainController::class, 'updatecountry']);
// Update Country
route::post('update_countrystore', [MainController::class, 'update_countrystore'])->name('update_countrystore');
// Update state Form Open
route::get('updatestateformadmin', [MainController::class, 'updatestate']);
// Update state
route::post('statestoreupdate', [MainController::class, 'update_statestore'])->name('statestoreupdate');
// Update City Form Open
route::get('updatecityformadmin', [MainController::class, 'updatecity']);
// Update City
route::post('citystoreupdate', [MainController::class, 'citystoreupdate'])->name('citystoreupdate');
// Open Userlist
route::get('/userlist', [MainController::class, 'loaduserlist']);
// Disable User
Route::get('disableusermaster2', [MainController::class, 'disableusermaster'])->name('id');
// Enable User
Route::get('enableusermaster2', [MainController::class, 'enableusermaster'])->name('id');
// Open User Master Form
route::get('/usermasteradmin', [MainController::class, 'openusermaster']);
// Update User Master Form Open
route::get('updateusermaster2', [MainController::class, 'updateusermaster']);
// Update User Master
route::post('update_usermaster2', [MainController::class, 'update_usermasterstore'])->name('update_usermaster2');
// Submit User Master Form
route::post('usermasterstore2', [MainController::class, 'submitusermaster'])->name('usermasterstore2');
// Search Username
route::match(['get', 'post'], 'searchusername', [MainController::class, 'loadcompanylist'])->name('searchusername');
// Get Update Logs
Route::get('getUpdateLogs', [MainController::class, 'fetchUpdates']);
// Auto Charge Posting
Route::get('/autochargepost', [CronController::class, 'autoCharge']);
// routes/web.php
// Open Expiry Module
Route::get('expirymodule', [MainController::class, 'showUpdateForm']);
Route::post('/property/update-expiry', [PropertyController::class, 'updateExpiry'])->name('property.updateExpiry');
// Open Backup Page
Route::get('superadmin/backups', [BackupController::class, 'index'])->name('superadmin.backups');
// Download Backup Prepare
Route::get('superadmin/storagefdownload', [BackupController::class, 'downloadStorage'])->name('superadmin.storagefdownload');
// Download Created Backup 
Route::get('superadmin/download-temp-zip/{filename}', [BackupController::class, 'downloadTempZip']);
// Download Database
Route::POST('superadmin/database-backup', [BackupController::class, 'downloadDatabaseBackup'])->name('superadmin.database-backup');
// Verify Database
Route::post('superadmin/verify-database', [BackupController::class, 'verifyDatabase'])->name('superadmin.verify-database');
// About Us Open
Route::get('about', [HomeController::class, 'about'])->name('about');
// Front Office Services
Route::get('services/front-office', [HomeController::class, 'frontofficeservices'])->name('services.front-office');
// POS Services
Route::get('services/pointofsale', [HomeController::class, 'pointofsaleservices'])->name('services.pointofsale');
// Banquet Services
Route::get('services/banquet', [HomeController::class, 'banquetservices'])->name('services.banquet');
// Inventory Services
Route::get('services/inventory', [HomeController::class, 'inventoryservices'])->name('services.inventory');
// Reservation Services
Route::get('services/reservation', [HomeController::class, 'reservationservices'])->name('services.reservation');
// Open Contact Page
Route::get('contact', [HomeController::class, 'contact'])->name('contact');
// Submit Contact Form
Route::post('contactsubmit', [ContactController::class, 'store'])->name('contact.submit');
// Submit Demo Request
Route::post('/demo-request', [DemoRequestController::class, 'store'])->name('demo-request.store');
// Booking Follow Up
Route::post('/booking-followup', [BookingFollowUp::class, 'store'])->name('booking-followup.store');