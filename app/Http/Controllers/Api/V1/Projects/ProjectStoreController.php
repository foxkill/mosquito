<?php

namespace App\Http\Controllers\Api\V1\Projects;

use App\Http\Requests\V1\StoreProjectRequest;
use App\Api\V1\Actions\CreateProjectAction;
use App\Http\Controllers\Controller;
use App\Models\Project;

class ProjectStoreController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(StoreProjectRequest $request, CreateProjectAction $action)
    {
        return $action->execute($request->validated());
    }
}
