<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BulkRequestSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['Pending', 'Processing', 'For Release', 'Claimed'];

        for ($i = 1; $i <= 5; $i++) { // 5 bulk requests for demo
            $status = $statuses[array_rand($statuses)];

            $dates = [
                'request_date' => Carbon::now(),
                'approve_date' => null,
                'forRelease_date' => null,
                'claimed_date' => null,
            ];

            // Set conditional dates based on status
            switch ($status) {
                case 'Processing':
                    $dates['approve_date'] = Carbon::now()->addDays(1);
                    break;
                case 'For Release':
                    $dates['approve_date'] = Carbon::now()->addDays(1);
                    $dates['forRelease_date'] = Carbon::now()->addDays(2);
                    break;
                case 'Claimed':
                    $dates['approve_date'] = Carbon::now()->addDays(1);
                    $dates['forRelease_date'] = Carbon::now()->addDays(2);
                    $dates['claimed_date'] = Carbon::now()->addDays(3);
                    break;
            }

            // Insert bulk request
            $requestId = DB::table('bulk_requests')->insertGetId([
                'School_Name' => 'School ' . $i,
                'School_Email' => 'school' . $i . '@example.com',
                'Doc_Type' => 'Form 137',
                'Status' => $status,
                'request_date' => $dates['request_date'],
                'approve_date' => $dates['approve_date'],
                'forRelease_date' => $dates['forRelease_date'],
                'claimed_date' => $dates['claimed_date'],
            ]);

            // Generate up to 200 students per request
            $studentCount = rand(50, 200);

            $students = [];
            for ($j = 1; $j <= $studentCount; $j++) {
                $students[] = [
                    'Request_ID' => $requestId,
                    'Student_Name' => 'Student ' . Str::random(5) . " ($j)",
                ];
            }

            DB::table('bulk_students')->insert($students);
        }
    }
}
