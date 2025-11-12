<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivedDocumentRequests extends Model
{
    protected $table = 'doc_requests_archive';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'clm_claimers_id',
        'std_students_id',
        'doc_categories_id',

        'request_time',
        'request_date',

        'request_schl_entity',
        'request_mode',
        'release_mode',

        'remarks',
        'status',
        'receipt_no',
        'approve_date',
        'forRelease_date',
        'claimed_date',
        'claimed_time',
        'deleted_at',
        'req_no',
        'image',
        'supporting_document'
    ];

    public function claimer()
    {
        return $this->belongsTo(ArchivedClaimers::class, 'clm_claimers_id', 'id');
    }

    public function studentInformation()
    {
        return $this->belongsTo(StudentInformationModel::class, 'std_students_id', 'id');
    }

    public function documents()
    {
        return $this->belongsTo(DocumentsModel::class, 'doc_categories_id', 'id');
    }
}
