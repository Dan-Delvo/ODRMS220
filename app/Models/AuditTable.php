<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AuditTable extends Model
{
    protected $table = 'audit_table';

    protected $primaryKey = 'id';

    public $timestamps = false; // Since you're manually storing 'time'

    protected $fillable = [
        'type',
        'old_data',
        'new_data',
        'time',
        'changedBy',
        'fromTableName',
        'description'
    ];

    protected $casts = [
        'time' => 'datetime',
    ];
}
