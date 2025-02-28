<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $hidden = ['created_at', 'updated_at', 'deleted_at']; 
    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'qty',
        'tax',
        'total_price',
        'sale_price',
        'delivery_status',
    ];
    public function orders(){
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id'); // Fix incorrect class reference
    }

    
    
}
