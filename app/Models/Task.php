<?php

namespace App\Models;

use App\Enums\Auth\Roles\Role;
use App\Enums\StateEnum;
use App\Models\Scopes\CreatorScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy([CreatorScope::class])]
class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'state',
        'project_id',
        'deadline',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, string>
     */
    protected $dispatchesEvents = [
        // 'saving' => TaskUpdating::class,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        // TODO: check if casting works.
        // return [
        //     'state' => AsEnumCollection::of(StateEnum::class),
        // ];
        return [
            'deadline' => 'datetime',
        ];
    }

    /**
     * Define the relationship with the User model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship with the Project model.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Scope a query to only include overdue tasks.
     */
    public function scopeOverdue(Builder $query): void
    {
        $query->where('deadline', '<', now());
    }

    /**
     * Scope a query to only include overdue tasks.
     */
    public function scopeNoAdminTasks(Builder $query): void
    {
        if (auth()->user()->role_id != Role::ADMINISTRATOR->value) {
            return;
        }

        $query->where('user_id', '!=', auth()->id());
    }

    /**
     * Scope a query to only show open tasks.
     */
    public function scopeOpen(Builder $query): void
    {
        $query->whereIn('state', [StateEnum::Todo->value, StateEnum::InProgess]);
    }
}
