<?php

namespace App\Enums\Auth\Token;

enum UserTokenEnum: string
{
    public const NAME = 'user-token';

    case ReadUserTasks = 'user-tasks-read';

    /**
     * Return a token ablity representation.
     */
    public function toAbility(): string
    {
        return 'ability:'.$this->value;
    }
}
