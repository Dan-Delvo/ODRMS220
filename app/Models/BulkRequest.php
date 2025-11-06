<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class BulkRequest extends Model
{
    protected $primaryKey = 'Request_ID';
    protected $table = 'bulk_requests';
    public $timestamps = false;

    protected $fillable = [
        'School_Name',
        'School_Email',
        'Doc_Type',
        'Status',
        'claimer_id', // ✅ Add this
        'request_date',
        'approve_date',
        'forRelease_date',
        'claimed_date',
    ];

    public function students()
    {
        return $this->hasMany(BulkStudent::class, 'Request_ID', 'Request_ID');
    }

    // ✅ Add claimer relationship
    public function claimer()
    {
        return $this->belongsTo(ClaimerModel::class, 'claimer_id', 'id');
    }

    public static function getBulkRequest()
    {
        return self::withCount('students')->get();
    }

    public static function moveRequest(string $status, int $id) 
    {
        try {
            $data = ['Status' => $status];

            if ($status === 'Processing') {
                $data['approve_date'] = now();
            } elseif ($status === 'For Release') {
                $data['forRelease_date'] = now();
            }

            self::where('Request_ID', $id)->update($data);
            Log::info('Request moved successfully to ' . $status);
        } catch (QueryException $e) {
            Log::error($e->getMessage());
        }
    }

    // ✅ New method for moving to claimed with claimer
    public static function moveRequestWithClaimer(string $status, int $id, int $claimerId, string $claimedDate) 
    {
        try {
            $data = [
                'Status' => $status,
                'claimer_id' => $claimerId,
                'claimed_date' => $claimedDate,
            ];

            self::where('Request_ID', $id)->update($data);
            Log::info('Request moved to Claimed with claimer ID: ' . $claimerId);
        } catch (QueryException $e) {
            Log::error('Error moving request with claimer: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function createWithStudents(array $data, array $students): self
    {
        $bulkRequest = self::create([
            'School_Name' => $data['school_name'],
            'School_Email' => $data['email'],
            'Doc_Type' => 'Form 137',
        ]);

        BulkStudent::createBulkStudents($bulkRequest->Request_ID, $students);

        return $bulkRequest;
    }
}
