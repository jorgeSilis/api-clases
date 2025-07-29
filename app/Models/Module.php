<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    /**
     * Get the tutor that owns the Module
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    /**
     * The students that belong to the Module
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_module', 'module_id', 'student_id');
    }

    /**
     * Get all of the sessions for the Module
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }



    protected $table = 'modules';

    protected $fillable = [
        'name',
        'number_sessions',
        'tutor_id'
    ];
}
