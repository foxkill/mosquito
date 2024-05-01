<?php

use App\Http\Controllers\Api\V1\Projects\ProjectDestroyController;
use App\Http\Controllers\Api\V1\Projects\ProjectUpdateController;
use App\Http\Controllers\Api\V1\Projects\ProjectIndexController;
use App\Http\Controllers\Api\V1\Projects\ProjectStoreController;
use App\Http\Controllers\Api\V1\Projects\ProjectTasksController;
use App\Http\Controllers\Api\V1\Projects\ProjectShowController;
use App\Http\Controllers\Api\V1\Tasks\TaskProjectsController;
use App\Http\Controllers\Api\V1\Tasks\TaskOverdueController;
use App\Http\Controllers\Api\V1\Tasks\TaskUpdateController;
use App\Http\Controllers\Api\V1\Tasks\TaskIndexController;
use App\Http\Controllers\Api\V1\Users\UserTasksController;
use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Auth\Middleware\Authenticate;
use App\Enums\Auth\Token\ProjectTokenEnum;
use App\Http\Controllers\AuthController;
use App\Enums\Auth\Token\TaskTokenEnum;
use App\Enums\Auth\Token\UserTokenEnum;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(Authenticate::using('sanctum'));

Route::group([
    'prefix' => 'v1', 
    'namespace' => '\App\Http\Controllers\Api\V1',
    'middleware' => ['deadline']
], function () {
    // Route::apiResource('tasks', TaskController::class)->middleware(['auth:sanctum']);
    Route::get('tasks/overdue', TaskOverdueController::class)
        ->name('tasks.overdue')
        ->middleware(['auth:sanctum', TaskTokenEnum::Read->toAbility()]);

    Route::get('tasks', TaskIndexController::class)
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
        ->middleware(['auth:sanctum', TaskTokenEnum::Delete->toAbility()]);
    
    Route::get('tasks/{task}/project', TaskProjectsController::class)
        ->name('tasks.project')
        ->middleware(['auth:sanctum', TaskTokenEnum::ReadTaskProjects->toAbility()]);

    Route::patch('tasks/{task}/deadline', TaskUpdateController::class)
        ->name('tasks.deadline')
        ->middleware(['auth:sanctum', TaskTokenEnum::Update->toAbility()]);
});

Route::group(['prefix' => 'v1', 'namespace' => '\App\Http\Controllers\Api\V1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('projects', ProjectIndexController::class)
        ->name('projects.index')
        ->middleware(ProjectTokenEnum::List->toAbility());

    Route::get('projects/{project}', ProjectShowController::class)
        ->name('projects.show')
        ->middleware(ProjectTokenEnum::Read->toAbility());

    Route::post('project', ProjectStoreController::class)
        ->name('projects.store')
        ->middleware(ProjectTokenEnum::Create->toAbility());

    Route::put('projects/{project}', ProjectUpdateController::class)
        ->name('projects.update')
        ->middleware(ProjectTokenEnum::Update->toAbility());

    Route::delete('projects/{project}', ProjectDestroyController::class)
        ->name('projects.destroy')
        ->middleware(ProjectTokenEnum::Delete->toAbility());

    Route::get('projects/{project}/tasks', ProjectTasksController::class)
        ->name('projects.tasks')
        ->middleware(ProjectTokenEnum::ReadProjectTasks->toAbility());
});

// Get tasks for a specfic user.
Route::group([
    'prefix' => 'v1', 
    'namespace' => '\App\Http\Controllers\Api\V1',
    'middleware' => ['auth:sanctum']
], function () {
    Route::get('users/{user}/tasks', UserTasksController::class)
        ->name('user.tasks')
        ->middleware(UserTokenEnum::ReadUserTasks->toAbility());
});

// Login facility.
Route::post('/login', [AuthController::class, 'login']);