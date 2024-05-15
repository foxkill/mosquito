<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserTasksResource;
use App\Models\User;

class UserTasksController extends Controller
{
    /**
     * Show a user with his associated tasks.
     */
    public function __invoke(User $user)
    {
        return new UserTasksResource($user);
    }
}
