<?php

use Illuminate\Contracts\Database\Eloquent\SerializesCastableAttributes;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ChannelPublic;
use App\Http\Controllers\ChannelPush;
use App\Http\Controllers\Pointofsale;
use App\Http\Controllers\Reporting;
use App\Http\Controllers\Fetch;
use App\Http\Controllers\Pos;
use App\Http\Controllers\Printing;
use App\Http\Controllers\PythonAuth;
use App\Http\Controllers\Reservation;
use League\Flysystem\Local\FallbackMimeTypeDetector;

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


Route::get('/rooms', [ChannelPush::class, 'showrooms']);
Route::post('/channelroomsubmit', [ChannelPush::class, 'updateinventory'])->name('channelroomsubmit');
Route::get('/rates', [ChannelPush::class, 'showrates']);
Route::post('fecthplanbyroom', [Fetch::class, 'fecthplanbyroom'])->name('fecthplanbyroom');
Route::post('/channelratesubmit', [ChannelPush::class, 'channelratesubmit'])->name('channelratesubmit');
Route::post('retcodefetch', [Fetch::class, 'retcodefetch'])->name('retcodefetch');
Route::post('channelupdate', [Fetch::class, 'channelupdate'])->name('channelupdate');
route::get('/derivedpricing', [ChannelPush::class, 'derivedpricing']);
Route::post('channelderivedsubmit', [ChannelPush::class, 'channelderivedsubmit'])->name('channelderivedsubmit');
Route::post('channelupdatederived', [Fetch::class, 'channelupdatederived'])->name('channelupdatederived');
Route::get('channelenviro', [ChannelPush::class, 'channelenviro']);
Route::post('channelenvirosubmit', [ChannelPush::class, 'channelenvirosubmit'])->name('channelenvirosubmit');
Route::post('/eglobetohms/{apiKey}/booking', [ChannelPublic::class, 'eglobetohms'])->name('eglobetohms');
// Particular Booking Fetch
Route::get('bookingfetch', [ChannelPush::class, 'bookingfetch'])->name('bookingfetch');
