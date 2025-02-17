<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pricing extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'name', 'type', 'short_description','images','sku'];

}
