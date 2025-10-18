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

            self::where('Request_ID', $id)->update(['Status' => $status]);
            Log::info('Success');

        } catch (QueryException $e) {

            Log::error($e->getMessage());

        }
    }

}
