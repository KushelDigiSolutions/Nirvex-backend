<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'image', 'status'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
