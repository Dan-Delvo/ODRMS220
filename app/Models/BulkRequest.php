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
        'request_date',
        'approve_date',
        'forRelease_date',
        'claimed_date',
    ];

    public function students()
    {
        return $this->hasMany(BulkStudent::class, 'Request_ID', 'Request_ID');
    }

    public static function getBulkRequest()
    {
        return self::withCount('students')->get();
    }

    public static function moveRequest(string $status, int $id) {
        try{

            $data = ['Status' => $status];

            if ($status === 'Processing') {
                $data['approve_date'] = now();
            } elseif ($status === 'For Release') {
                $data['forRelease_date'] = now();
            } elseif ($status === 'Claimed') {
                $data['claimed_date'] = now();
            }

            self::where('Request_ID', $id)->update($data);
            Log::info('Success');

        } catch (QueryException $e) {

            Log::error($e->getMessage());

        }
    }

    public static function createWithStudents(array $data, array $students): self
    {
        // $latestId = self::max('Request_ID'); // Replace 'id' with your actual PK column name
        // $nextId = $latestId ? $latestId + 1 : 1;
        // Create the bulk request
        $bulkRequest = self::create([
            // 'Request_ID' => $nextId,
            'School_Name' => $data['school_name'],
            'School_Email' => $data['email'],
            'Doc_Type' => 'Form 137',
        ]);

        // Let BulkStudent model handle its own insertion
        BulkStudent::createBulkStudents($bulkRequest->Request_ID, $students);

        return $bulkRequest;
    }
}
