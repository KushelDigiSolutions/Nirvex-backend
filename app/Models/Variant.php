<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variant extends Model
{
    use HasFactory, SoftDeletes;

    protected $rules = [
        'sku' => 'sometimes|required|sku|unique:variants',
    ];

    protected $fillable = [
        'product_id',
        'type',
        'name',
        'sku',
        'short_description',
        'images',
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
