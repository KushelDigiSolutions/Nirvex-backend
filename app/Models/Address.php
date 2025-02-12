<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $table = 'addresses';

    protected $fillable = [
        'user_id',
        'name',
        'address1',
        'address2',
        'landmark',
        'phone',
        'city',
        'state',
        'address_type',
        'pincode',
        'status'
    ];


    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
}
