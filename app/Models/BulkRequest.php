<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkRequest extends Model
{
    protected $primaryKey = 'Request_ID';
    public $timestamps = false;

    protected $fillable = [
        'School_Name', 'School_Email', 'Doc_Type', 'Status',
        'request_date', 'approve_date', 'forRelease_date', 'claimed_date'
    ];

    public function students()
    {
        return $this->hasMany(BulkStudent::class, 'Request_ID', 'Request_ID');
    }
}
