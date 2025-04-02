<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Account extends Authenticatable
{
    use HasFactory, Notifiable;

    // Specify the table name
    protected $table = 'acc_users';
    public $timestamps = false;

    // Specify the primary key if not 'id'
    protected $primaryKey = 'user_account_id'; // The actual primary key in the table
    protected $keyType = 'int';  // The key is an integer
    public $incrementing = false;  // Disable auto-increment since it's custom

    // Define fillable fields for mass assignment
    protected $fillable = [
        'user_account_id',
        'std_students_id',
        'role_id',
        'email_address',
        'email_verified_at',
        'username',
        'password',
        'remember_token',
        'account_created',
        'account_edited',
        'deleted_at',
        'fcm_token'
    ];

    // Hide sensitive fields
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Define casting for date fields
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequestModel::class, 'std_students_id', 'std_students_id');
    }

    public function roles()
    {
        return $this->belongsTo(RolesModel::class, 'role_id', 'id');
    }
}

