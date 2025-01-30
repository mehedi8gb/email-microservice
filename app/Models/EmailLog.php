<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    protected $guarded = [];

//    public function email(): BelongsTo
//    {
//        return $this->belongsTo(Email::class);
//    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
