<?php

namespace App\Enums\Auth\Token;

/**
 * Implement a token. 
 * 
 * TODO: Move later to a more appropriate location like: 
 * \App\Auth\Tokens.
 */
enum TaskTokenEnum: string {
    public const NAME = 'task-token';

    case List = 'task-list';
    case Read = 'task-read';
    case Create = 'task-create';
    case Update = 'task-update';
    case Delete = 'task-delete';

    case ReadTaskProjects = 'task-projects-read';

    /**
     * Return a token ablity representation.
     * 
     * @return string 
     */
    public function toAbility(): string
    {
        return 'ability:' . $this->value;
    }
}