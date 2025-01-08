<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'type',
        'name',
        'short_description',
        'images',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
