<?php

namespace App\Enums\Auth\Token;

/**
 * Implement a token.
 */
enum TaskTokenEnum: string
{
    public const NAME = 'task-token';

    case List = 'task-list';
    case Read = 'task-read';
    case Create = 'task-create';
    case Update = 'task-update';
    case Delete = 'task-delete';

    case ReadTaskProjects = 'task-projects-read';

    /**
     * Return a token ablity representation.
     */
    public function toAbility(): string
    {
        return 'ability:'.$this->value;
    }
}
