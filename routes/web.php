<?php

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

Route::get('/', function () {
    return view('main-layout.index');
});

Route::get('/template', function () {
    return view('main-layout.index');
});

Route::get('observation-form', [ObservationController::class, 'form'])->name('form');
Route::get('observation-success', [ObservationController::class, 'success'])->name('success');

Route::prefix('observation')->name('observation.')->group(function () {
    Route::get('datatables', [ObservationController::class, 'datatables'])->name('user-datatables');
    Route::get('setting', [ObservationDateController::class, 'datatables'])->name('datatables');
    Route::get('get/date/{date}', [ObservationDateController::class, 'date'])->name('byDate');
    Route::resource('/', ObservationController::class)->parameters(['' => 'observation']);
    Route::resource('/date', ObservationDateController::class)->parameters(['' => 'observationDate']);
});
