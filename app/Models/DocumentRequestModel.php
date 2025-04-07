<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class DocumentRequestModel extends Model
{
    //
    use Notifiable;

    protected $table = 'doc_requests';
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
        'status'
    ];

    public function claimer()
    {
        return $this->belongsTo(ClaimerModel::class, 'clm_claimers_id', 'id');
    }

    public function studentInformation()
    {
        return $this->belongsTo(StudentInformationModel::class, 'std_students_id', 'id');
    }

    public function documents()
    {
        return $this->belongsTo(DocumentsModel::class, 'doc_categories_id', 'id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'std_students_id', 'std_students_id');
    }

    public static function updateOrCreateRequest(array $data)
    {
        return self::updateOrCreate(
            ['id' => $data['id']], // Search by ID
            [
                'doc_categories_id' => $data['document_id'],

                'request_time' => Carbon::now()->format('H:i:s'),
                'request_date' => Carbon::now()->toDateString(),

                'request_schl_entity' => $data['request_schl_entity'],
                'request_mode' => $data['request_mode'],
                'release_mode' => $data['release_mode'],

                'remarks' => $data['remarks'],
                'status' => $data['status'],
            ]
        );
    }

    public static function createDocumentRequest(array $data)
    {
        return self::create([ // Insert into the database
            'id' => $data['id'],
            'claimer_id' => $data['claimer_id'],
            'student_information_id' => $data['student_information_id'],
            'approval_id' => "1",  // Assuming this is static for now
            'document_id' => $data['document_id'],
            'request_time' => Carbon::now()->format('H:i:s'),
            'request_date' => Carbon::now()->toDateString(),
            'request_schl_entity' => $data['request_schl_entity'],
            'requested_sf10' => $data['requested_sf10'],
            'release_mode' => $data['release_mode'],
            'remarks' => $data['remarks'],
            'status' => $data['status'],
        ]);
    }


}
