<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerDetail extends Model
{
    use Hasfactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'address_id',
        'shop_name',
        'shop_owner_name',
        'so_id',
        'shop_address',
        'shop_image',
        'gst_number',
        'gst_image',
        'fssi_image',
        'adhaar_number',
        'adhaar_front_img',
        'adhaar_back_img',
        'pan_number',
        'pan_img',
        'bank_cheque_img',
        'created_at',
        'update_at',
        'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function customerDetail()
    {
        return $this->hasOne(CustomerDetail::class, 'so_id', 'id');
    }

    public function salesOfficer()
    {
        return $this->belongsTo(User::class, 'so_id', 'id');
    }
}
