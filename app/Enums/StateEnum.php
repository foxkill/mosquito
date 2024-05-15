<?php

namespace App\Enums;

enum StateEnum: string
{
    case Todo = 'todo';
    case InProgess = 'in_progress';
    case Done = 'done';
}
