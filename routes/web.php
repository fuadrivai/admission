<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ObservationController;
use App\Http\Controllers\ObservationDateController;
use App\Models\ObservationDate;
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


Route::group(['middleware' => 'prevent-back-history'], function () {
    Route::get('auth', [AuthController::class, 'index'])->middleware('guest')->name('login');
    Route::post('auth', [AuthController::class, 'authenticate']);
    Route::get('observation-form', [ObservationController::class, 'form'])->name('form');
    Route::get('observation-success', [ObservationController::class, 'success'])->name('success');
    Route::get('/level/get', [LevelController::class, 'get']);
    Route::get('/observation/get/date/division/{date}/{divisionId}', [ObservationDateController::class, 'dateAndDivision']);
    Route::post('/observation', [ObservationController::class, 'store']);

    Route::group(['middleware' => 'auth'], function () {
        Route::get('/', function () {
            return view('main-layout.index');
        });

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::prefix('observation')->name('observation.')->group(function () {
            Route::get('datatables', [ObservationController::class, 'datatables'])->name('user-datatables');
            Route::get('setting', [ObservationDateController::class, 'datatables'])->name('datatables');
            Route::post('setting/active/{id}', [ObservationDateController::class, 'active'])->name('active');
            Route::get('get/date/{date}', [ObservationDateController::class, 'date'])->name('byDate');
            Route::resource('/', ObservationController::class)->parameters(['' => 'observation']);
            Route::resource('/date', ObservationDateController::class)->parameters(['' => 'observationDate']);
        });

        Route::prefix('level')->name('level.')->group(function () {
            Route::get('datatables', [LevelController::class, 'datatables'])->name('datatables');
            // Route::get('get', [LevelController::class, 'get'])->name('get');
            Route::resource('', LevelController::class)->parameters(['' => 'level']);
        });
        Route::prefix('division')->name('division.')->group(function () {
            Route::get('datatables', [DivisionController::class, 'datatables'])->name('datatables');
            Route::get('get', [DivisionController::class, 'get'])->name('get');
            Route::resource('', DivisionController::class)->parameters(['' => 'division']);
        });
        Route::prefix('setting')->name('setting.')->group(function () {
            Route::get('/password/change', [AuthController::class, 'edit']);
            Route::post('/password/change', [AuthController::class, 'update']);
            // Route::resource('', DivisionController::class)->parameters(['' => 'division']);
        });
    });
});
