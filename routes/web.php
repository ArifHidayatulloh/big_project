<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentUserController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkingListController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CostReviewController;
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

Route::get('/loginPage', [AuthController::class, 'showLoginForm']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::get('/', [AuthController::class, 'dashboard']);

    Route::post('/update_profile/{id}', [AuthController::class, 'edit_profile']);

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

    // Payment Schedule
    Route::controller(PaymentScheduleController::class)->prefix('payment_schedule')->group(function(){
        Route::get('/', 'index');
        Route::get('/unpaid_recap', 'unpaid');
        Route::get('/paid_recap', 'paid');
        Route::post('/store', 'store');
        Route::get('/edit_modal/{id}', 'getEditModal');
        Route::post('/update/{paymentSchedule}', 'update');
        Route::get('/destroy/{paymentSchedule}', 'destroy');
        Route::post('/edit/{id}', 'edit');
        Route::post('/rollback/{id}', 'rollback');
    });
    // End of Payment Schedule


    // Cost review controller
    Route::controller(CostReviewController::class)->prefix('cost-review')->group(function(){
        Route::get('/','index');
        Route::get('/consolidated','show_consolidated');
        Route::post('/store-cost-review','store_cost_review');
        Route::post('/update-cost-review/{id}','update_cost_review');
        Route::get('/destroy-cost-review/{id}','destroy_cost_review');
        Route::get('/{id}','show');
        Route::get('/{id}/period','show_period');
    });


    // End of cost review

    // Category
    Route::controller(CostReviewController::class)->prefix('category')->group(function(){
        Route::get('/','index_category');
        Route::post('/store-category','store_category');
        Route::post('/store-subcategory','store_subcategory');
        Route::post('/store-group','store_description_group');
        Route::post('/update-category/{id}','update_category');
        Route::post('/update-subcategory/{id}','update_subcategory');
        Route::post('/update-group/{id}','update_description_group');
        Route::get('/destroy-category/{id}','destroy_category');
        Route::get('/destroy-subcategory/{id}','destroy_subcategory');
        Route::get('/destroy-group/{id}','destroy_description_group');
    });
    // End of Category

    // Description
    Route::controller(CostReviewController::class)->prefix('description')->group(function(){
        Route::get('/{id}','index_description');
        Route::post('/store-description','store_description');
        Route::post('/update-description/{id}','update_description');
        Route::get('/destroy-description/{id}','destroy_description');
    });
    // End of Description

    // Monthly budget
    Route::controller(CostReviewController::class)->prefix('budget')->group(function(){
        Route::get('/{id}','index_monthly_budget');
        Route::post('/store','store_budget');
        Route::get('/edit/{id}/{month}/{year}', 'edit_budget');
        Route::post('/update','update_budget');
    });
    // End of monthly budget

    Route::controller(CostReviewController::class)->prefix('actual')->group(function(){
        Route::get('/{id}', 'index_actual');
        Route::post('/store','store_actual');
        Route::post('/update/{id}', 'update_actual');
        Route::get('/destroy/{id}', 'destroy_actual');
    });

    // Export
    Route::controller(ExportController::class)->prefix('export')->group(function (){
        Route::get('/working_list', 'excel_working_list');
        Route::get('/payment_supplier', 'payment_supplier');
        Route::get('/cost-review','cost_review');
        Route::get('/cost-review-consolidated','cost_review_consolidate');
    });
    // End of Export
});



