<?php

namespace App\Http\Controllers\Api\V1\Projects;

use App\Http\Requests\V1\StoreProjectRequest;
use App\Api\V1\Actions\UpdateProjectAction;
use App\Http\Controllers\Controller;
use App\Models\Project;

class ProjectUpdateController extends Controller
{
    /**
     * Update a specific project.
     */
    public function __invoke(Project $project, StoreProjectRequest $request, UpdateProjectAction $action)
    {
        return $action->execute($project, $request->validated());
    }
}
