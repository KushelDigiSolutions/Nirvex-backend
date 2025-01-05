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
        'mrp',
        'availability',
        'specification',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id');
    }

    
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_cat_id');
    }
}
