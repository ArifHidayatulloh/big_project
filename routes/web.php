<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentUserController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkingListController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PaymentScheduleController;
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

Route::get('/', [AuthController::class, 'showLoginForm']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::get('/dashboard', [AuthController::class, 'dashboard']);

    // User
    Route::get('/search-user', [UserController::class, 'searchUser']);

    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::get('/', 'index');
        Route::get('/create', 'create');
        Route::post('/store', 'store');
        Route::get('/edit/{user}', 'edit');
        Route::post('/update/{user}', 'update');
        Route::get('/destroy/{user}', 'destroy');
    });
    // End of User

    // Department
    Route::controller(UnitController::class)->prefix('department')->group(function () {
        Route::get('/', 'index');
        Route::get('/create', 'create');
        Route::post('/store', 'store');
        Route::get('/edit/{unit}', 'edit');
        Route::post('/update/{unit}', 'update');
        Route::get('/destroy/{unit}', 'destroy');
    });
    // End of Department

    // Department User
    Route::controller(DepartmentUserController::class)->prefix('depuser')->group(function () {
        Route::get('/', 'index');
        Route::get('/create', 'create');
        Route::post('/store', 'store');
        Route::get('/edit/{depUser}', 'edit');
        Route::post('/update/{depUser}', 'update');
        Route::get('/destroy/{depUser}', 'destroy');
    });
    // End of Department User

    // Working List
    Route::controller(WorkingListController::class)->prefix('working-list')->group(function () {
        Route::get('/', 'index');
        Route::get('/create', 'create');
        Route::post('/store', 'store');
        Route::get('/{id}', 'show');
        Route::get('/edit/{id}', 'edit');
        Route::post('/update/{id}', 'update');
        Route::get('/destroy/{id}', 'destroy');
        Route::post('/requestActionPIC/{id}', 'request');
        Route::get('/updatePIC/{commentId}', 'updatePIC');
        Route::post('/storeUpdatePIC/{commentId}', 'storeUpdate');
        Route::get('/editUpdatePIC/{id}', 'editUpdatePIC');
        Route::post('/storeUpdatePICNew/{id}', 'storeEditUpdate');
        Route::get('/deleteUpdatePIC/{id}', 'deleteUpdatePIC');

    });

    Route::controller(WorkingListController::class)->prefix('need_approval')->group(function(){
        Route::get('/', 'request_approve');
        Route::get('/{id}', 'request_detail');
        Route::post('/approve/{id}', 'approve');
        Route::post('/reject/{id}', 'reject');
    });
    // End of Working List

    // Control Budget
    Route::controller(BudgetController::class)->prefix('control-budget')->group(function () {
        Route::get('/', 'index');
        Route::post('/store_cost_review', 'store_cost_review');
        Route::post('/update_cost_review/{id}', 'update_cost_review');
        Route::get('/destroy_cost_review/{id}', 'destroy_cost_review');
        Route::get('/{id}','show');
        Route::post('/storeCategory', 'storeCategory');
        Route::post('/storeSubcategory', 'storeSubcategory');
        Route::post('/storeDescription', 'storeDescription');
        Route::get('/planned_budget/{costReview}', 'planned_budget');
        Route::post('/budget_plan_add','plan_budget');
        Route::get('/review_cost/{costReviewId}', 'review_cost');
        Route::get('/individual_update_page/{month}/{year}', 'individualUpdatePage');
    });
    // End of Control Budget

    // Payment Schedule
    Route::controller(PaymentScheduleController::class)->prefix('payment_schedule')->group(function(){
        Route::get('/', 'index');
    });
    // End of Payment Schedule

    // Export
    Route::controller(ExportController::class)->prefix('export')->group(function (){
        Route::get('/working_list', 'excel_working_list');
    });
    // End of Export
});



