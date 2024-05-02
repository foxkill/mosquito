<?php

namespace App\Api\V1\Actions;

use App\Models\Project;

class CreateProjectAction
{
    /**
     * Create a new project
     * 
     * @return Project
     */
    public function execute(array $projectData): Project
    {
        return Project::create($projectData);
    }
}
