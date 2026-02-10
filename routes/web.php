<?php

use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\AdmissionDocumentController;
use App\Http\Controllers\AdmissionStatementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankChargerController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\EnrolmentController;
use App\Http\Controllers\EnrolmentPriceController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ObservationController;
use App\Http\Controllers\ObservationDateController;
use App\Http\Controllers\ParentsStudentController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\SchoolVisitController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SiswaEreportController;
use App\Http\Controllers\XenditCallBackController;
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
    
    Route::get('enrolment/form', [EnrolmentController::class, 'form'])->name('enrolment.form');
    Route::get('enrolment/student/{code}', [EnrolmentController::class, 'showByCode'])->name('enrolment.studentShowByCode');
    Route::post('enrolment/post', [EnrolmentController::class, 'post'])->name('enrolment.postForm');
    
    Route::get('document/student', [AdmissionController::class, 'studentForm'])->name('admission.studentForm');
    Route::post('document/applicant', [AdmissionController::class, 'postApplicant'])->name('admission.postApplicant');
    Route::get('document/applicant/{id}', [AdmissionController::class, 'getApplicant'])->name('admission.getApplicant');
    Route::get('document/code/{code}', [AdmissionController::class, 'showByCode'])->name('admission.showByCode');
    Route::get('document/check/{code}', [AdmissionController::class, 'checkStatus'])->name('admission.checkStatus');
    Route::get('document/parent/{child_id}/{role}', [AdmissionController::class, 'getParent'])->name('admission.getParent');
    Route::post('document/parent', [AdmissionController::class, 'postParent'])->name('admission.postParent');
    Route::post('document/health', [AdmissionController::class, 'postHealth'])->name('admission.postHealth');

    Route::get('document/file/{code}', [AdmissionDocumentController::class, 'code'])->name('admissionDocument.code');
    Route::get('document/file/id/{id}', [AdmissionDocumentController::class, 'byAdmissionId'])->name('admissionDocument.byAdmissionId');
    Route::post('document/file', [AdmissionDocumentController::class, 'store'])->name('admissionDocument.store');

    Route::get('document/statement/{code}', [AdmissionStatementController::class, 'index'])->name('admission.index');
    Route::post('document/statement', [AdmissionStatementController::class, 'store'])->name('admission.store');
    Route::post('document/statement/financial', [AdmissionStatementController::class, 'storeFinancial'])->name('admission.storeFinancial');
    Route::get('document/statement/financial/{id}', [AdmissionStatementController::class, 'getFinancial'])->name('admission.getFinancial');
    Route::post('document/statement/agreement', [AdmissionStatementController::class, 'postAgreement'])->name('admission.postAgreement');
    Route::get('document/statement/{id}/agreement/{role}', [AdmissionStatementController::class, 'getAgreement'])->name('admission.getAgreement');
    
    Route::get('document/success/{code}', [AdmissionController::class, 'success'])->name('admission.success');

    Route::get('/level/branch/{id}', [LevelController::class, 'getByBranch'])->name('getByBranch');
    Route::get('/holiday/check/{date}', [HolidayController::class, 'isHoliday']);
    Route::get('/prospect/code/{code}', [ProspectController::class, 'getByCode']);

    Route::get('/bank/single', [BankChargerController::class, 'getSingle']);

    Route::get('/mhis/portal/parent', [ParentsStudentController::class, 'getParentStudent']);
    Route::get('/ereport/siswa', [SiswaEreportController::class, 'getSiswaByNis']);
    


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
        Route::prefix('branch')->name('branch.')->group(function () {
            Route::get('get', [BranchController::class, 'get'])->name('get');
            Route::resource('', BranchController::class)->parameters(['' => 'branch']);
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
