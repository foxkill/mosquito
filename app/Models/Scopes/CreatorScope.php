<?php

namespace App\Models\Scopes;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Enums\Auth\Roles\Role;

class CreatorScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (! auth()->check()) {
            throw new AuthenticationException();
        }

        if (auth()->user()->role_id == Role::ADMINISTRATOR->value) {
            return;
        }

        $builder->where('user_id', auth()->id());
    }
}
