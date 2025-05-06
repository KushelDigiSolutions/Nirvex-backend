<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerPrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'seller_prices';

    protected $fillable = [
        'user_id',
        'variant_id',
        'quantity',
        'prices',
    ];

    protected $casts = [
        'prices' => 'float',
        'quantity' => 'integer',
    ];

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }
}
