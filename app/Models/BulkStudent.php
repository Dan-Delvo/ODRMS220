<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BulkStudent extends Model
{
    protected $primaryKey = 'Student_ID';
    public $timestamps = false;

    protected $fillable = ['Request_ID', 'Student_Name'];

    public function request()
    {
        return $this->belongsTo(BulkRequest::class, 'Request_ID', 'Request_ID');
    }

    public static function getStudent(){
        return self::all();
    }

    public static function createBulkStudents(int $requestId, array $studentNames): void
    {
        $studentsData = [];

        foreach ($studentNames as $studentName) {
            $studentsData[] = [
                'Request_ID' => $requestId,
                'Student_Name' => $studentName,
            ];
        }

        self::insert($studentsData);
    }

    public static function unionStudentTable()
    {
        return self::query()
            ->join('bulk_requests', 'bulk_students.Request_ID', '=', 'bulk_requests.Request_ID')
            ->select(
                'bulk_students.Student_ID as id',
                'bulk_students.Request_ID as req_no',
                'bulk_students.Student_Name as full_name',
                'bulk_requests.Doc_Type as DocType',
                'bulk_requests.School_Name as request_schl_entity',
                DB::raw("'Bulk Request' as request_mode"),
                DB::raw("'Walk In' as release_mode"),
                DB::raw("NULL as remarks"),
                'bulk_requests.Status as status',
                'bulk_requests.request_date as request_date',
                'bulk_requests.approve_date as approve_date',
                'bulk_requests.forRelease_date as forRelease_date',
                'bulk_requests.claimed_date as claimed_date'
            );
    }

}
