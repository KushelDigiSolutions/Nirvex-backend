<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    use HasFactory;
    protected $fillable = [
        'user_id',       // Add user_id to allow mass assignment
        'total_amount',  // Include other fields you want to allow
        'status',
    ];
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
    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }
    
}
