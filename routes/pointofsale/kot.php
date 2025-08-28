<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Kot;
use App\Http\Controllers\Pos;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PythonAuth;

// Open Outlet List Data KOT Entry
Route::get('kotentry', [Kot::class, 'kotentry'])
    ->name('kotentry.route');
// Fetch Max KRS NO
Route::post('getmaxkrsno', [Kot::class, 'getmaxkrsno'])->name('getmaxkrsno');
// Fetch Max NRS NO
Route::post('getmaxnrsno', [Kot::class, 'getmaxnrsno'])->name('getmaxnrsno');
// Fetch Sessionmast
Route::post('getsessionmast', [Kot::class, 'getsessionmast'])->name('getsessionmast');
// Fetch Pending Kot Data
Route::post('fetchpendingkot', [Kot::class, 'fetchpendingkot'])->name('fetchpendingkot');
// Fetch Previous Nc Kot Data
Route::post('fetchncpreviouskot', [Kot::class, 'fetchncpreviouskot'])->name('fetchncpreviouskot');
// Submit Kot Entry
Route::post('kotstore', [Kot::class, 'submitkotentry'])->name('kotstore');
// Send Print Data
Route::post('sendprintdata', [Kot::class, 'sendprintdata'])->name('sendprintdata');
// Fetch Print Data
Route::post('fetchprintdata', [PythonAuth::class, 'fetchprintdata'])->name('fetchprintdata');
// Fetch Print Data Bill
Route::post('fetchprintdatabill', [PythonAuth::class, 'fetchprintdatabill'])->name('fetchprintdatabill');
// Delete Print Data
Route::post('deleteprintdata', [PythonAuth::class, 'deleteprintdata'])->name('deleteprintdata');
// Delete Print Data BIll
Route::post('deleteprintdatabill', [PythonAuth::class, 'deleteprintdatabill'])->name('deleteprintdatabill');
Route::post('oldwaitername', [Kot::class, 'oldwaitername'])->name('oldwaitername');
// Open Outlet List Data KOT Transfer
Route::get('kottransfer', [Kot::class, 'kottransfer'])->name('kottransfer.route');
// Submit Kot Transfer
Route::post('kottransferstore', [Kot::class, 'kottransferstore'])->name('kottransferstore');
// Fetch Rest Room No
Route::post('vnoxhr', [Kot::class, 'vnoxhr'])->name('vnoxhr');
// Open Outlet List Data POS Bill Entry
Route::get('posbillentry', [Pos::class, 'posbillentry'])
    ->name('posbillentry.route');
// All Sale Bill XHR
Route::post('allbillxhrsale', [Pos::class, 'allbillxhrsale']);
// All Sale KOT XHR
Route::post('allbillxhrkot', [Pos::class, 'allbillxhrkot']);
// Fetch Pending Merged KOT
Route::post('fetchpendingmergekot', [Pos::class, 'fetchpendingmergekot']);
// Fetch Item Details By Vno 
Route::post('fetchitemdetailsbbyvno', [Kot::class, 'fetchitemdetailsbbyvno'])->name('fetchitemdetailsbbyvno');