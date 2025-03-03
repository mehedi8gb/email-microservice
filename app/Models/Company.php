<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'email'];
    protected $casts = [
        'name' => 'encrypted',
//        'email' => 'encrypted',
    ];

    public function smtpConfigs(): HasMany
    {
        return $this->hasMany(SmtpConfig::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class);
    }

//    public function files(): HasMany
//    {
//        return $this->hasMany(File::class);
//    }
}
