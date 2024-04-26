<?php

namespace App\Http\Controllers\Api\V1\Projects;

use App\Http\Resources\V1\ProjectResource;
use App\Http\Controllers\Controller;
use App\Models\Project;

class ProjectIndexController extends Controller
{
    /**
     * Handle the incoming index request.
     */
    public function __invoke()
    {
        return ProjectResource::collection(Project::latest()->get());
    }
}
