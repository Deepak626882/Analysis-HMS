<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserParam;
use App\Http\Controllers\MainController;

// Open Persmission Page
route::get('/permissions', [UserParam::class, 'PermisionManage']);
// Update Permissions
route::post('updatepermission', [MainController::class, 'PermissionUpdate'])->name('updatepermission');
// Get Checkbo List
route::post('/getcheckboxlist', [UserParam::class, 'loadcheckbox']);
// Submit Permission User Module 1st
Route::post('submipermusermodule', [UserParam::class, 'submipermusermodule'])->name('submipermusermodule');
// Validate Permission Check Box
Route::post('validatecheck', [UserParam::class, 'validatecheck'])->name('validatecheck');
// Fetch Main Menu Data
Route::get('getmainmenu', [UserParam::class, 'getmainmenu'])->name('getmainmenu');
// Fetch Menu 2
Route::post('fetchsubmenu', [UserParam::class, 'fetchsubmenu'])->name('fetchsubmenu');
// Fetch Last menu
Route::post('fetchlastmenu', [UserParam::class, 'fetchlastmenu'])->name('fetchlastmenu');
// Fetch Property User Permission
Route::get('userpermision', [UserParam::class, 'userpermision']);
// Fetch Menu List
Route::post('menulist', [UserParam::class, 'menulist'])->name('menulist');
// Submit User Permission Form
Route::post('userparamsubmit', [UserParam::class, 'userparamsubmit'])->name('userparamsubmit');
// User Pos Submit Point Of Sale
Route::post('updateposuserxhr', [UserParam::class, 'updateposuserxhr'])->name('updateposuserxhr');