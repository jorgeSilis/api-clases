<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{

    /**
     * Get all of the modules for the User (en este caso tutor)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class, 'tutor_id');
    }

    /**
     * The modules that belong to the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function modulesAssigned(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'user_module', 'student_id', 'module_id');
    }

    /**
     * Get all of the attendances for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    /**
     * Get all of the markedAttendances for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function markedAttendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'marked_by');
    }

    /**
     * The modules that belong to the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function user_modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'user_module', 'student_id', 'module_id');
    }


    protected $table = 'users';

    protected $fillable = [
        'first_name',
        'last_name',
        'age',
        'email',
        'user_type',
    ];
}
