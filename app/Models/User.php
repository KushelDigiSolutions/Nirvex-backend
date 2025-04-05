<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'dummy_phone',
        'dummy_email',
        'user_type',
        'password',
        'pincode',
        'image',
    ];

    protected $dates = ['deleted_at'];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey(); // Typically, the primary key of the user
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function roles()
    {

        return $this->belongsToMany(\Spatie\Permission\Models\Role::class, 'model_has_roles', 'model_id', 'role_id');
    }
    
    // public function customerDetails()
    // {
    //     return $this->hasOne(CustomerDetail::class);
    // }

    public function customerDetails()
    {
        return $this->hasone(CustomerDetail::class, 'user_id');
    }

    public function addresses()
    {
        // return $this->hasOne(Address::class, 'user_id', 'id');
        return $this->hasMany(Address::class, 'user_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }
}
