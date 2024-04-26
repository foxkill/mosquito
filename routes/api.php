<?php

use App\Http\Controllers\Api\V1\Projects\ProjectIndexController;
use App\Http\Controllers\Api\V1\Projects\ProjectShowController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Enums\Auth\Token\ProjectTokenEnum;
use Illuminate\Auth\Middleware\Authenticate;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Enums\TaskTokenEnum;
use App\Http\Controllers\Api\V1\Projects\ProjectDestroyController;
use App\Http\Controllers\Api\V1\Projects\ProjectStoreController;
use App\Http\Controllers\Api\V1\Projects\ProjectUpdateController;

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
    
    // Route::get('tasks/{task}/comments', [TaskCommentsController])
        // ->name('tasks.destroy')
        // ->middleware(['auth:sanctum',TaskTokenEnum::Delete->toAbility()]);
});

Route::group(['prefix' => 'v1/projects', 'namespace' => '\App\Http\Controllers\Api\V1'], function () {
    Route::get('projects', ProjectIndexController::class)
        ->name('projects.index')
        ->middleware(['auth:sanctum', ProjectTokenEnum::List->toAbility()]);

    Route::get('projects/{project}', ProjectShowController::class)
        ->name('projects.show')
        ->middleware(['auth:sanctum', ProjectTokenEnum::Read->toAbility()]);

    Route::post('project', ProjectStoreController::class)
        ->name('projects.store')
        ->middleware(['auth:sanctum', ProjectTokenEnum::Create->toAbility()]);

    Route::put('projects/{project}', ProjectUpdateController::class)
        ->name('projects.update')
        ->middleware(['auth:sanctum', ProjectTokenEnum::Update->toAbility()]);

    Route::delete('projects/{project}', ProjectDestroyController::class)
        ->name('projects.destroy')
        ->middleware(['auth:sanctum', ProjectTokenEnum::Delete->toAbility()]);
});

Route::post('/login', [AuthController::class, 'login']);