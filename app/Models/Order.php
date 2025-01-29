<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $hidden = ['created_at', 'updated_at', 'deleted_at']; 


    public function users()
    {
        return $this->belongsTo(User::class, 'id');
    }


    public function products()
    {
        return $this->hasManyThrough(Product::class, OrderItem::class, 'order_id', 'id', 'id', 'product_id');
    }

    public function orderItems() 
{
    return $this->hasMany(OrderItem::class, 'order_id');
}
    
}
