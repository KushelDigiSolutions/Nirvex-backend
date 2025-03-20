<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderReason extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_reasons';

    protected $fillable = [
        'order_id',
        'user_id',
        'type',
        'reason',
    ];

    protected $casts = [
        'type' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
