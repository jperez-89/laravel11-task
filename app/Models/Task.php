<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'title',
        'description',
        "user_id"
    ];

    /**
     * Get the user that owns the Task
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    // Relacion de muchas tareas a un usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The sharedWith that belong to the Task
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sharedWith(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_user')
            ->withPivot('permission')
            ->withTimestamps();
    }
}
