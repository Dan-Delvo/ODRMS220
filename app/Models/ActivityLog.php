<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'log_access';

    public $timestamps = false;        

    protected $fillable = [
        'user_account_id',
        'username',
        'email_address',
        'action_type',
        'remarks',
        'timestamp'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'user_account_id', 'user_account_id');
    }
}
