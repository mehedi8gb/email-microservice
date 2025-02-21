<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Email extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'subject', 'message', 'other_data'];

    protected $casts = [
        'other_data' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
