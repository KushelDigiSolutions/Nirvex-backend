<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pricing extends Model
{
    use HasFactory;
    protected $table = 'pricings';

    protected $fillable = [
        'pincode',
        'product_id',
        'product_sku_id',
        'mrp',
        'price',
        'tax_type',
        'tax_value',
        'ship_charges',
        'valid_upto',
        'status',
        'is_cash',
    ];

    // Cast attributes to their proper data types
    // protected $casts = [
    //     'valid_upto' => 'datetime',
    //     'status' => 'boolean',
    //     'is_cash' => 'boolean',
    // ];

    /**
     * Relationship: A Price belongs to a Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Scope to filter active pricings.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

}
