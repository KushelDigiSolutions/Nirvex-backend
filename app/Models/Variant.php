<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'type',
        'name',
        'short_description',
        'images',
        'sku'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function pricing()
    {
        return $this->hasOne(Pricing::class, 'product_sku_id', 'sku');
    }

}
