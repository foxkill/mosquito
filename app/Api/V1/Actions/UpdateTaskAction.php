<?php

namespace App\Api\V1\Actions;

use App\Models\Task;

class UpdateTaskAction
{
    /**
     * Update an existing task.
     * 
     * @return Task
     */
    public function execute(Task $task, array $taskData): bool
    {
        return $task->update($taskData);
    }
}
