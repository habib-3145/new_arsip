<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\KartuKeluargaController;
use App\Http\Controllers\IjazahController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication Route
Route::middleware(['auth', 'json'])->prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->withoutMiddleware('auth');
    Route::delete('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::prefix('setting')->group(function () {
    Route::get('', [SettingController::class, 'index']);
});

Route::middleware(['auth', 'verified', 'json'])->group(function () {
    Route::prefix('setting')->middleware('can:setting')->group(function () {
        Route::post('', [SettingController::class, 'update']);
    });

    Route::prefix('master')->group(function () {
        Route::middleware('can:master-user')->group(function () {
            Route::get('users', [UserController::class, 'get']);
            Route::post('users', [UserController::class, 'index']);
            Route::post('users/store', [UserController::class, 'store']);
            Route::apiResource('users', UserController::class)
                ->except(['index', 'store'])->scoped(['user' => 'uuid']);
        });

        Route::middleware('can:master-role')->group(function () {
            Route::get('roles', [RoleController::class, 'get'])->withoutMiddleware('can:master-role');
            Route::post('roles', [RoleController::class, 'index']);
            Route::post('roles/store', [RoleController::class, 'store']);
            Route::apiResource('roles', RoleController::class)
                ->except(['index', 'store']);
        });

        Route::middleware('can:master-dokumen')->group(function () {
            Route::get('dokumen/kartu-keluarga', [KartuKeluargaController::class, 'get']);
            Route::post('dokumen/kartu-keluarga', [KartuKeluargaController::class, 'index']);
            Route::post('dokumen/kartu-keluarga/store', [KartuKeluargaController::class, 'store']);
    
            Route::apiResource('dokumen/kartu-keluarga', KartuKeluargaController::class)
                ->except(['index', 'store']);

            Route::get('dokumen/ijazah', [IjazahController::class, 'get']);
            Route::post('dokumen/ijazah', [IjazahController::class, 'index']);
            Route::post('dokumen/ijazah/store', [IjazahController::class, 'store']);
            Route::apiResource('dokumen/ijazah', IjazahController::class)
                ->except(['index', 'store']);    
        });
    });
});





        // Siswa Routes

        Route::post('/siswas', [SiswaController::class, 'index']);
        Route::post('/siswa/data', [SiswaController::class, 'store']);
        Route::apiResource('siswas', SiswaController::class)
        ->except(['index', 'store']);
        Route::get('siswas', [SiswaController::class, 'get']);
        Route::get('/siswa/data/{id}', [SiswaController::class, 'show']);
        Route::delete('/siswa/{id}', [SiswaController::class, 'destroy']);


        // Classes Routes

        Route::post('/classes', [ClassesController::class, 'index']);
        Route::post('/classes/data', [ClassesController::class, 'store']);
        Route::get('classes', [ClassesController::class, 'get']);
        Route::delete('/classes/{id}', [ClassesController::class, 'destroy']);
