<?php

namespace App\Http\Controllers\Api\V1\Projects;

use App\Http\Resources\V1\ProjectResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;

class ProjectShowController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Project $project)
    {
        return new ProjectResource($project);
    }
}
