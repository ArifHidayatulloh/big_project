<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ControlBudgetController;
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

Route::get('/', [AuthController::class, 'showLoginForm']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::get('/dashboard', function () {
        return view('dashboard');
    });

    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::get('/', 'index');
        Route::get('/create', 'create');
        Route::post('/store', 'store');
        Route::get('/edit/{user}', 'edit');
        Route::post('/update/{user}', 'update');
        Route::get('/destroy/{user}', 'destroy');
    });

    Route::controller(UnitController::class)->prefix('department')->group(function () {
        Route::get('/', 'index');
        Route::get('/create', 'create');
        Route::post('/store', 'store');
        Route::get('/edit/{unit}', 'edit');
        Route::post('/update/{unit}', 'update');
        Route::get('/destroy/{unit}', 'destroy');
    });

    Route::controller(DepartmentUserController::class)->prefix('depuser')->group(function () {
        Route::get('/', 'index');
        Route::get('/create', 'create');
        Route::post('/store', 'store');
        Route::get('/edit/{depUser}', 'edit');
        Route::post('/update/{depUser}', 'update');
        Route::get('/destroy/{depUser}', 'destroy');
    });

    Route::controller(ControlBudgetController::class)->prefix('control-budget')->group(function () {
        Route::get('/', 'index');
        Route::get('/unit/{unit}', 'show');
        Route::post('/storeCategory', 'storeCategory');
        Route::post('/storeSubcategory', 'storeSubcategory');
        Route::post('/storeDescription', 'storeDescription');

        // Route category action
        Route::post('/updateCategory/{category}', 'updateCategory');
        Route::get('/destroyCategory/{category}', 'destroyCategory');

        // Route subcategory action
        Route::post('/updateSubcategory/{subcategory}', 'updateSubcategory');
        Route::get('/destroySubcategory/{subcategory}', 'destroySubcategory');

        // Route description action
        Route::post('/updateDescription/{description}', 'updateDescription');
        Route::get('/destroyDescription/{description}', 'destroyDescription');

        // Montly Budget
        Route::get('/monthly-budget/{unit}', 'formMonthlyBudget');
        Route::post('/monthly-budget/store', 'storeMonthlyBudget');
        Route::get('/monthly-budget/{unit}/edit', 'editMonthlyBudget');
        // Route untuk update Monthly Budget
        Route::post('/monthly-budget/{unit}/update', 'updateMonthlyBudget');
        Route::get('/cost-overview/{unit}', 'costOverview');
        Route::get('/cost-review/{unit}', 'costReview');

        // Expenses
        Route::get('/expenses/{unit}', 'expenseShow');
        Route::get('/inputExpense/{unit}', 'inputExpensesForm');
        Route::post('/storeExpense/{unit}', 'storeExpenses');
    });
});
