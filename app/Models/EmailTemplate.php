<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',  // e.g., "forgot_password", "welcome_email"
        'body',
        'placeholders' // JSON field for allowed placeholders
    ];
}
