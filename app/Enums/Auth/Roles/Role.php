<?php

namespace App\Enums\Auth\Roles;

enum Role: int
{
    case ADMINISTRATOR = 1;
    case PROJECT_OWNER = 2;
    case USER = 3;
}
