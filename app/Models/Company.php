<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email'];
    protected $casts = [
        'name' => 'encrypted',
//        'email' => 'encrypted',
    ];

    public function smtpConfig(): HasOne
    {
        return $this->hasOne(SMTPConfig::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

//    public function files(): HasMany
//    {
//        return $this->hasMany(File::class);
//    }
}
