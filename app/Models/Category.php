<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    // protected $table = 'categories';

    protected $fillable = ['id','name', 'image', 'status'];


    public function subcategories()
    {
        return $this->hasMany(SubCategory::class, 'cat_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'cat_id');
    }

}
