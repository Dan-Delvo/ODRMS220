<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        'req_no',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Use a transaction to prevent race conditions
            DB::beginTransaction();
            try {
                $year = date('Y');

                // Lock the table and get the last record for this year
                $last = self::where('req_no', 'LIKE', 'BR-' . $year . '-%')
                            ->lockForUpdate()
                            ->orderByRaw('CAST(SUBSTRING(req_no, LOCATE("-", req_no, 4) + 1) AS UNSIGNED) DESC')
                            ->first();

                if ($last && preg_match('/^BR-'.$year.'-(\d+)$/', $last->req_no, $match)) {
                    $next = intval($match[1]) + 1;
                } else {
                    $next = 1;
                }

                // Use 4-digit padding for better scalability
                // Format: BR-YYYY-#### (e.g., BR-2025-0001)
                $model->req_no = 'BR-' . $year . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        });
    }
}
