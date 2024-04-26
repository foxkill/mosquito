<?php

namespace App\Enums\Auth\Token;

enum ProjectTokenEnum: string
{
    public const NAME = 'project-token';

    case List = 'projct-list';
    case Read = 'projct-read';
    case Create = 'projct-create';
    case Update = 'projct-update';
    case Delete = 'projct-delete';

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
