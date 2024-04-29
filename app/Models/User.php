<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\Auth\Roles\Role;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Define the relationship with the User model.
     * 
     * @return HasMany
     */
    public function tasks(): HasMany 
    {
        return $this->hasMany(Task::class);
    }

    // May there will be a project owner later.
    // public function project(): HasMany {
    //     return $this->hasMany(Project::class, 'owner_id');
    // }

    /**
     * Helper function to check if user has admin role.
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role_id == Role::ADMINISTRATOR->value;
    }
}
