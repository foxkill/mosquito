<?php

namespace App\Http\Controllers\Api\V1\Projects;

use App\Api\V1\Actions\DeleteProjectAction;
use App\Http\Controllers\Controller;
use App\Models\Project;

class ProjectDestroyController extends Controller
{
    /**
     * Remove a project.
     */
    public function __invoke(Project $project, DeleteProjectAction $action)
    {
        $action->execute($project);

        // Return 204 - No content.
        return response()->noContent();
    }
}
