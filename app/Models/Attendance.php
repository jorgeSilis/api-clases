<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    /**
     * Get the session that owns the Attendance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'session_id');
    }


    /**
     * Get the student that owns the Attendance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    protected $fillable = [
        'session_id',
        'student_id',
        'status',
    ];

}
