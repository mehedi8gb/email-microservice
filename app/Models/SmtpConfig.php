<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmtpConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'from_email',
        'from_name',
        'host',
        'port',
        'username',
        'password',
        'encryption'
    ];
    protected $casts = [
        'host' => 'encrypted',
        'port' => 'encrypted',
        'username' => 'encrypted',
        'password' => 'encrypted',
        'encryption' => 'encrypted',
        'from_email' => 'encrypted',
        'from_name' => 'encrypted',
    ];

    protected $hidden = [
        'password',
    ];
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
