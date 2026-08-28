<?php

use App\Http\Controllers\Api\EnrolmentApiController;
use App\Http\Controllers\Api\AcademicStructureApiController;
use App\Http\Controllers\Api\XenditCallBackApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/xendit/callback', [XenditCallBackApiController::class, 'handleCallback']);
Route::post('/enrolment/post', [EnrolmentApiController::class, 'post']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('token.public')->group(function () {
    Route::prefix('enrolment')->name('enrolment.')->group(function () {
        Route::get('/', [EnrolmentApiController::class,'index'])->name('api.enrolment.index');
    });
    Route::get('/academic-year', [AcademicStructureApiController::class, 'academicYears']);
    Route::get('/academic-year/active', [AcademicStructureApiController::class, 'activeAcademicYears']);
    Route::get('/branch', [AcademicStructureApiController::class, 'branches']);
    Route::get('/level', [AcademicStructureApiController::class, 'levels']);
    Route::get('/level/branch/{branchId}', [AcademicStructureApiController::class, 'levelByBranch']);
    Route::get('/grade', [AcademicStructureApiController::class, 'grades']);
    Route::get('/grade/level/{levelId}', [AcademicStructureApiController::class, 'gradeByLevel']);
});
