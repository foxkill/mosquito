<?php

namespace App\Api\V1\Actions;

use App\Models\Project;

class UpdateProjectAction
{
    /**
     * Update an existing project
     * 
     * @return Project
     */
    public function execute(Project $project, array $projectData): Project
    {
        $project->update($projectData);

        return $project;
    }
}
