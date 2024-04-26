<?php

namespace App\Enums;

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