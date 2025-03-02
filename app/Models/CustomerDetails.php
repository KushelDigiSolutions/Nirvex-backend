<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerDetails extends Model
{
    use HasFactory;

    protected $table = 'customer_details';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

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

    /**
     * Relationship with User model (belongsTo).
     * Each customer detail belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship with Sales Officer (belongsTo).
     * Each customer detail is associated with a sales officer.
     */
    public function salesOfficer()
    {
        return $this->belongsTo(User::class, 'so_id', 'id');
    }
}
