<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SellerPrice extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'variant_id', 'quantity', 'price'];

}
