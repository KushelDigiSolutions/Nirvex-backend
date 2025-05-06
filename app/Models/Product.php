<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use Hasfactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'cat_id',
        'sub_cat_id',
        'status',
        'image',
        'video',
        'mrp',
        'availability',
        'return_policy',
        'physical_property',
        'key_benefits',
        'specification',
        'standards',
    ];
    

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id', 'id');
        // return $this->belongsTo(Category::class, 'cat_id');
    }
    public function pricings()
    {
        return $this->hasMany(Pricing::class);
    }
    
    public function SubCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_cat_id');
    }

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }

    public function variant()
    {
        return $this->hasMany(Variant::class, 'product_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }
}
