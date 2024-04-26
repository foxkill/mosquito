<?php

namespace App\Api\V1\Actions;

use App\Models\Project;

class DeleteProjectAction
{
    /**
     * Delete a project
     * 
     * @return void
     */
    public function execute(Project $project)
    {
        // There could be a massive logic for deleting a project.
        $project->delete();
    }
}
