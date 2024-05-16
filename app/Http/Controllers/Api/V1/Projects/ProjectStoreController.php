<?php

namespace App\Http\Controllers\Api\V1\Projects;

use App\Api\V1\Actions\CreateProjectAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreProjectRequest;

class ProjectStoreController extends Controller
{
    /**
     * Store a newly created project in the backend.
     */
    public function __invoke(StoreProjectRequest $request, CreateProjectAction $action)
    {
        return $action->execute($request->validated());
    }
}
