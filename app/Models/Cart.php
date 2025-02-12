<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id'];

    /**
     * Get the cart items associated with the cart.
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
