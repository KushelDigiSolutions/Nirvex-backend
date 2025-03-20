<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'additional_data',
    ];

    protected $casts = [
        'additional_data' => 'array', // Automatically cast JSON data to array
    ];
}
