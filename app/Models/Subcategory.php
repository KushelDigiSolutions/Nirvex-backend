<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subcategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['cat_id', 'name', 'image', 'status'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id');
     
        // return $this->belongsTo(Category::class);
    
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'sub_cat_id');
    }

}
