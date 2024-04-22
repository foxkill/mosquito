<?php

use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Enums\TaskTokenEnum;
use App\Models\User;
use Illuminate\Validation\Rules\Enum;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(Authenticate::using('sanctum'));

Route::group(['prefix' => 'v1', 'namespace' => '\App\Http\Controllers\Api\V1'], function () {
    // Route::apiResource('tasks', TaskController::class)->middleware(['auth:sanctum']);
    Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index')->middleware(['auth:sanctum', 'ability:task-list']);
    Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store')->middleware(['auth:sanctum', 'ability:task-create']);
    Route::get('tasks/{task}', [TaskController::class, 'show'])->name('tasks.show')->middleware(['auth:sanctum', 'ability:task-read']);
    Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update')->middleware(['auth:sanctum', 'ability:task-update']);
    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy')->middleware(['auth:sanctum', 'ability:task-delete']);
});

Route::post('/auth/token', function (Request $request) {
    $request->validate([
        'name' => 'required',
        'password' => 'required',
    ]);
 
    $user = User::where('name', $request->name)->first();
 
    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'username' => ['The provided credentials are incorrect.'],
        ]);
    }
 
    return $user->createToken(
        'task-access',
        [
            TaskTokenEnum::Read,
            TaskTokenEnum::Create,
            TaskTokenEnum::Delete,
            TaskTokenEnum::Update,
        ]
    )->plainTextToken;
});
