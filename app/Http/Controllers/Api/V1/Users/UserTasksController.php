<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Resources\V1\UserTasksResource;
use App\Http\Controllers\Controller;
use App\Models\User;

class UserTasksController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(User $user)
    {
        // return $user->with('tasks')->get();
        return new UserTasksResource($user);
    }
}
