<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkStudent extends Model
{
    protected $primaryKey = 'Student_ID';
    public $timestamps = false;

    protected $fillable = ['Request_ID', 'Student_Name'];

    public function request()
    {
        return $this->belongsTo(BulkRequest::class, 'Request_ID', 'Request_ID');
    }


}
