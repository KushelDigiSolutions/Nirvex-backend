<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    use HasFactory;
    protected $fillable = [
        'user_id', 'total_mrp', 'total_tax', 'total_price',
        'total_discount', 'coupon_id', 'grand_total','razor_order_id','address_id'
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at']; 


    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function products()
    {
        return $this->hasManyThrough(Product::class, OrderItem::class, 'order_id', 'id', 'product_id');
    }

    public function orderItems() 
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function addresses()
    {
        return $this->belongsTo(Address::class, 'id');
    }    

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
    
}
