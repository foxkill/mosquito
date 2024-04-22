<?php

namespace App\Enums;

enum TaskTokenEnum: string {
    case List = 'task-list';
    case Read = 'task-read';
    case Create = 'task-create';
    case Update = 'task-update';
    case Delete = 'task-delete';
}