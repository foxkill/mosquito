<?php

namespace App\Enums;

enum TaskTokenEnum: string {
    case TaskList = 'task-list';
    case TaskRead = 'task-read';
    case TaskCreate = 'task-create';
    case TaskUpdate = 'task-update';
    case TaskDelete = 'task-delete';
}