<?php

namespace App\Enums\Auth\Token;

enum ProjectTokenEnum: string
{
    public const NAME = 'project-token';

    case List = 'project-list';
    case Read = 'project-read';
    case Create = 'project-create';
    case Update = 'project-update';
    case Delete = 'project-delete';

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
