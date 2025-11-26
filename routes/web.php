<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankChargerController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\EnrolmentController;
use App\Http\Controllers\EnrolmentPriceController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ObservationController;
use App\Http\Controllers\ObservationDateController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\SchoolVisitController;
use App\Http\Controllers\SettingController;
use App\Models\Prospects;
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

    Route::get('schoolvisit-form', [SchoolVisitController::class, 'form'])->name('schoolvisit-form');
    Route::get('schoolvisit-success', [SchoolVisitController::class, 'success'])->name('success');
    Route::post('/school-visit-post', [SchoolVisitController::class, 'post']);
    Route::get('/school-visit/capacity/check', [SchoolVisitController::class, 'checkCapacity']);

    Route::get('observation-form', [ObservationController::class, 'form'])->name('observation-form');
    Route::get('observation-success', [ObservationController::class, 'success'])->name('success');
    Route::get('/level/get', [LevelController::class, 'get']);
    Route::get('/observation/get/date/division/{date}/{divisionId}', [ObservationDateController::class, 'dateAndDivision']);
    Route::post('/observation-post', [ObservationController::class, 'post']);
    
    Route::get('enrolment/external', [EnrolmentController::class, 'external'])->name('enrolment.external');
    Route::post('enrolment/external', [EnrolmentController::class, 'postExternal'])->name('enrolment.postExternal');

    Route::get('enrolment/internal', [EnrolmentController::class, 'internal'])->name('enrolment.internal');
    
    Route::get('/level/branch/{id}', [LevelController::class, 'getByBranch'])->name('getByBranch');
    Route::get('/holiday/check/{date}', [HolidayController::class, 'isHoliday']);
    Route::get('/prospect/code/{code}', [ProspectController::class, 'getByCode']);

    Route::get('/bank/single', [BankChargerController::class, 'getSingle']);
    


    Route::get('/price/branch/level/{branchId}/{levelId}', [EnrolmentPriceController::class, 'getRegistrationPrice']);

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

        Route::prefix('schoolvisit')->name('schoolvisit.')->group(function () {
            Route::get('datatables', [SchoolVisitController::class, 'datatables'])->name('list-schoolvisit');
            Route::get('setting', [SchoolVisitController::class, 'setting'])->name('schoolvisit-setting');
            Route::post('max-capacity', [SchoolVisitController::class, 'postMax'])->name('post-max');
            Route::resource('/', SchoolVisitController::class)->parameters(['' => 'schoolvisit']);
        });

        Route::prefix('enrolment')->name('enrolment.')->group(function () {
            Route::get('datatables', [EnrolmentController::class, 'datatables'])->name('list-enrolment');
            Route::get('setting', [EnrolmentController::class, 'setting'])->name('enrolment-setting');
            Route::post('max-capacity', [EnrolmentController::class, 'postMax'])->name('post-max');
            Route::resource('/', EnrolmentController::class)->parameters(['' => 'enrolment']);
        });

        Route::prefix('price')->name('price.')->group(function () {
            Route::resource('', EnrolmentPriceController::class)->parameters(['' => 'enrolment-price']);
        });

        Route::prefix('level')->name('level.')->group(function () {
            Route::get('datatables', [LevelController::class, 'datatables'])->name('datatables');
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

            Route::get('/form', [SettingController::class, 'index']);
            Route::post('/form/wa', [SettingController::class, 'postWA']);
            Route::put('/form/wa', [SettingController::class, 'putWA']);

            Route::get('/form/email', [SettingController::class, 'emailCreateForm']);
            Route::get('/form/email/{id}', [SettingController::class, 'emailEditForm']);
            Route::put('/form/email', [SettingController::class, 'putEmail']);
            Route::post('/form/email', [SettingController::class, 'postEmail']);
            // Route::resource('', DivisionController::class)->parameters(['' => 'division']);
        });
        Route::resource('holiday', HolidayController::class)->parameters(['' => 'holiday']);
    });
});
