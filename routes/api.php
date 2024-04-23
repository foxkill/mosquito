<?php

use App\Enums\TaskTokenEnum;
use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Middleware\Authenticate;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(Authenticate::using('sanctum'));

Route::group(['prefix' => 'v1', 'namespace' => '\App\Http\Controllers\Api\V1'], function () {
    // Route::apiResource('tasks', TaskController::class)->middleware(['auth:sanctum']);
    Route::get('tasks', [TaskController::class, 'index'])
        ->name('tasks.index')
        ->middleware(['auth:sanctum', TaskTokenEnum::List->toAbility()]);

    Route::post('tasks', [TaskController::class, 'store'])
        ->name('tasks.store')
        ->middleware(['auth:sanctum', TaskTokenEnum::Create->toAbility()]);

    Route::get('tasks/{task}', [TaskController::class, 'show'])
        ->name('tasks.show')
        ->middleware(['auth:sanctum', TaskTokenEnum::Read->toAbility()]);

    Route::put('tasks/{task}', [TaskController::class, 'update'])
        ->name('tasks.update')
        ->middleware(['auth:sanctum', TaskTokenEnum::Update->toAbility()]);

    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])
        ->name('tasks.destroy')
        ->middleware(['auth:sanctum',TaskTokenEnum::Delete->toAbility()]);
});

Route::post('/login', [AuthController::class, 'login']);