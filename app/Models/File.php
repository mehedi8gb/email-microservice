<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'student_id',
        'file_name',
        'file_path',
        'file_type',
    ];

    protected array $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        // 'user_id' => 'encrypted',
        // 'student_id' => 'encrypted',
        'file_name' => 'encrypted',
        'file_path' => 'encrypted',
//        'file_type' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
