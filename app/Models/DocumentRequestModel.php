<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

    // In DocumentRequestModel.php
    public function receipt()
    {
        return $this->hasOne(DocuPaymentFee::class, 'receipt_no', 'receipt_no');
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

    public static function getDocumentRequests(string $status, array $options = [])
    {
        $query = self::where('status', $status)
            ->with(['claimer', 'studentInformation', 'documents', 'receipt']);

        // Apply search if provided
        if (!empty($options['search'])) {
            $searchTerm = $options['search'];
            $filter = $options['filter'] ?? 'all';

            $query->where(function($q) use ($searchTerm, $filter) {
                switch ($filter) {
                    case 'student':
                        $q->whereHas('studentInformation', function($sq) use ($searchTerm) {
                            $sq->where(DB::raw("CONCAT(FirstName, ' ', LastName)"), 'LIKE', "%{$searchTerm}%");
                        });
                        break;
                    case 'document':
                        $q->whereHas('documents', function($dq) use ($searchTerm) {
                            $dq->where('DocType', 'LIKE', "%{$searchTerm}%");
                        });
                        break;

                    case 'school':
                        $q->where('request_schl_entity', 'LIKE', "%{$searchTerm}%");
                        break;

                    case 'reqno':
                        $q->where('req_no', 'LIKE', "%{$searchTerm}%");
                        break;

                    case 'status':
                        $q->where('status', 'LIKE', "%{$searchTerm}%");
                        break;

                    case 'all':
                    default:
                        // Search across all fields
                        $q->where(function($subQuery) use ($searchTerm) {
                            $subQuery->where('req_no', 'LIKE', "%{$searchTerm}%")
                                ->orWhere('request_schl_entity', 'LIKE', "%{$searchTerm}%")
                                ->orWhere('status', 'LIKE', "%{$searchTerm}%")
                                ->orWhere('remarks', 'LIKE', "%{$searchTerm}%")
                                ->orWhere('request_mode', 'LIKE', "%{$searchTerm}%")
                                ->orWhere('release_mode', 'LIKE', "%{$searchTerm}%")
                                ->orWhereHas('studentInformation', function($sq) use ($searchTerm) {
                                    $sq->where(DB::raw("CONCAT(FirstName, ' ', LastName)"), 'LIKE', "%{$searchTerm}%");
                                })
                                ->orWhereHas('documents', function($dq) use ($searchTerm) {
                                    $dq->where('DocType', 'LIKE', "%{$searchTerm}%");
                                });
                        });
                        break;
                }
            });
        }

        // Apply sorting
        $sort = $options['sort'] ?? 'default';
        switch ($sort) {
            case 'asc':
                $query->orderBy('req_no', 'asc');
                break;
            case 'desc':
                $query->orderBy('req_no', 'desc');
                break;
            case 'default':
            default:
                // Default ordering
                $query->orderBy('req_no', 'desc');
                break;
        }

        // Get per page from options or default to 10
        $perPage = $options['per_page'] ?? 10;

        return $query->paginate($perPage);
    }

    public static function getStatusCount(string $status)
    {
        return self::where('status', $status)->count();
    }



}
