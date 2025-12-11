<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $table = 'guests';

    protected $fillable = [
        'doc_request_id',
        'name',
        'email_address',
        'contact_no',
    ];

    /**
     * Relationship: A guest belongs to one document request
     */
    public function documentRequest()
    {
        return $this->belongsTo(DocumentRequestModel::class, 'doc_request_id', 'id');
    }
}
