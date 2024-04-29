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
            // Let the auth layer handle it.
            return;
        }

        // Dont apply the scope when adminstrator.
        if (auth()->user()->role_id == Role::ADMINISTRATOR->value) {
            return;
        }
        
        // Allow only the owner of the task to access it.
        $builder->where('user_id', auth()->id());
    }
}