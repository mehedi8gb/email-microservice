<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'email',  // The admin/specific user email who will get notifications
        'enabled' // Boolean to enable/disable notifications
    ];
}
