<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'usertype', 
        'email', 
        'fullname',
        'phonenumber',
        'gender',
        'gps_long',
        'gps_lat',
        'image_link',
        'id_link',
        'approval_status',
        'approved_by'
    ];


}
