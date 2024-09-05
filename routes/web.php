<?php

use App\Http\Controllers\DepartmentUserController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
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

Route::get('/', function () {
    return view('dashboard');
});

Route::controller(UserController::class)->prefix('user')->group(function () {
    Route::get('/','index');
    Route::get('/create','create');
    Route::post('/store','store');
    Route::get('/edit/{user}','edit');
    Route::post('/update/{user}','update');
    Route::get('/destroy/{user}','destroy');
});

Route::controller(UnitController::class)->prefix('department')->group(function (){
    Route::get('/','index');
    Route::get('/create','create');
    Route::post('/store','store');
    Route::get('/edit/{unit}','edit');
    Route::post('/update/{unit}','update');
    Route::get('/destroy/{unit}','destroy');
});

Route::controller(DepartmentUserController::class)->prefix('depuser')->group(function () {
    Route::get('/','index');
    Route::get('/create','create');
    Route::post('/store','store');
    Route::get('/edit/{depUser}','edit');
    Route::post('/update/{depUser}','update');
    Route::get('/destroy/{depUser}','destroy');
});
