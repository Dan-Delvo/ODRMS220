<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PopulateAuditTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder populates the audit_table with historical data from:
     * 1. Document requests (doc_requests table)
     * 2. Student account creations (std_students table)
     */
    public function run(): void
    {
        echo "Starting audit table population...\n";

        // Disable audit events temporarily
        DB::statement("SET @DISABLE_AUDIT_TRIGGERS = 1");

        // 1. Populate audit records for all document requests
        $this->populateDocumentRequestAudits();

        // 2. Populate audit records for all student account creations
        $this->populateStudentAccountAudits();

        // Re-enable audit events
        DB::statement("SET @DISABLE_AUDIT_TRIGGERS = 0");

        echo "Audit table population completed successfully!\n";
    }

    /**
     * Populate audit records for document requests
     */
    private function populateDocumentRequestAudits(): void
    {
        echo "Populating document request audits...\n";

        // Get all document requests with related information
        $docRequests = DB::table('doc_requests as dr')
            ->join('doc_categories as dc', 'dr.doc_categories_id', '=', 'dc.id')
            ->join('std_students as ss', 'dr.std_students_id', '=', 'ss.id')
            ->select(
                'dr.id',
                'dr.req_no',
                'dr.request_date',
                'dr.request_time',
                'dr.approve_date',
                'dr.forRelease_date',
                'dr.claimed_date',
                'dr.claimed_time',
                'dr.request_schl_entity',
                'dr.request_mode',
                'dr.release_mode',
                'dr.status',
                'dr.remarks',
                'dr.reason',
                'dr.relationship',
                'dc.DocType',
                'dc.DocPrice',
                DB::raw("CONCAT(ss.FirstName, ' ', ss.LastName) as student_name"),
                'ss.LRN',
                'ss.Grade_level',
                'ss.Std_status'
            )
            ->orderBy('dr.request_date')
            ->orderBy('dr.request_time')
            ->get();

        echo "Found " . $docRequests->count() . " document requests.\n";

        $auditRecords = [];
        $batchSize = 500; // Process in batches to avoid memory issues

        foreach ($docRequests as $index => $request) {
            // 1. CREATE INSERT audit log for initial request
            $timestamp = Carbon::parse($request->request_date . ' ' . ($request->request_time ?? '00:00:00'));

            $baseData = [
                'Request No' => $request->req_no ?? 'N/A',
                'Student Name' => $request->student_name,
                'LRN' => $request->LRN ?? 'N/A',
                'Grade Level' => $request->Grade_level ?? 'N/A',
                'Student Status' => $request->Std_status ?? 'N/A',
                'Document Type' => $request->DocType,
                'Document Price' => 'Php ' . number_format($request->DocPrice, 2),
                'School/Entity' => $request->request_schl_entity ?? 'N/A',
                'Request Mode' => $request->request_mode ?? 'N/A',
                'Release Mode' => $request->release_mode ?? 'N/A',
            ];

            if (!empty($request->reason)) {
                $baseData['Reason'] = $request->reason;
            }
            if (!empty($request->relationship)) {
                $baseData['Relationship'] = $request->relationship;
            }

            $newData = array_merge($baseData, [
                'Status' => 'Pending',
                'Remarks' => 'Pending',
            ]);

            $auditRecords[] = [
                'type' => 'INSERT',
                'old_data' => null,
                'new_data' => json_encode($newData),
                'time' => $timestamp,
                'changedBy' => 'Registrar Window',
                'fromTableName' => 'doc_requests',
                'description' => 'Document request created: ' . $request->DocType . ' for ' . $request->student_name
            ];

            // 2. CREATE UPDATE audit log for APPROVED status (if approve_date exists)
            if (!empty($request->approve_date)) {
                $oldData = array_merge($baseData, [
                    'Status' => 'Pending',
                    'Remarks' => 'Pending',
                ]);

                $newData = array_merge($baseData, [
                    'Status' => 'Processing',
                    'Remarks' => 'Processing',
                ]);

                $approveTimestamp = Carbon::parse($request->approve_date);

                $auditRecords[] = [
                    'type' => 'UPDATE',
                    'old_data' => json_encode($oldData),
                    'new_data' => json_encode($newData),
                    'time' => $approveTimestamp,
                    'changedBy' => 'Registrar Window',
                    'fromTableName' => 'doc_requests',
                    'description' => 'Document request approved: ' . $request->req_no
                ];
            }

            // 3. CREATE UPDATE audit log for FOR RELEASE status (if forRelease_date exists)
            if (!empty($request->forRelease_date)) {
                $oldData = array_merge($baseData, [
                    'Status' => 'Processing',
                    'Remarks' => 'Processing',
                ]);

                $newData = array_merge($baseData, [
                    'Status' => 'For Release',
                    'Remarks' => 'For Release',
                ]);

                $forReleaseTimestamp = Carbon::parse($request->forRelease_date);

                $auditRecords[] = [
                    'type' => 'UPDATE',
                    'old_data' => json_encode($oldData),
                    'new_data' => json_encode($newData),
                    'time' => $forReleaseTimestamp,
                    'changedBy' => 'Registrar Window',
                    'fromTableName' => 'doc_requests',
                    'description' => 'Document request ready for release: ' . $request->req_no
                ];
            }

            // 4. CREATE UPDATE audit log for CLAIMED status (if claimed_date exists)
            if (!empty($request->claimed_date)) {
                $oldData = array_merge($baseData, [
                    'Status' => 'For Release',
                    'Remarks' => 'For Release',
                ]);

                $newData = array_merge($baseData, [
                    'Status' => 'Claimed',
                    'Remarks' => 'Claimed',
                ]);

                $claimedTimestamp = Carbon::parse($request->claimed_date . ' ' . ($request->claimed_time ?? '00:00:00'));

                $auditRecords[] = [
                    'type' => 'UPDATE',
                    'old_data' => json_encode($oldData),
                    'new_data' => json_encode($newData),
                    'time' => $claimedTimestamp,
                    'changedBy' => 'Registrar Window',
                    'fromTableName' => 'doc_requests',
                    'description' => 'Document request claimed: ' . $request->req_no
                ];
            }

            // Insert in batches
            if (count($auditRecords) >= $batchSize) {
                DB::table('audit_table')->insert($auditRecords);
                echo "Inserted batch of " . count($auditRecords) . " audit records.\n";
                $auditRecords = [];
            }
        }

        // Insert remaining records
        if (count($auditRecords) > 0) {
            DB::table('audit_table')->insert($auditRecords);
            echo "Inserted final batch of " . count($auditRecords) . " audit records.\n";
        }

        echo "Document request audits completed.\n\n";
    }

    /**
     * Populate audit records for student account creations
     */
    private function populateStudentAccountAudits(): void
    {
        echo "Populating student account creation audits...\n";

        // Get all student accounts with account information
        $students = DB::table('std_students as ss')
            ->leftJoin('acc_users as au', 'ss.id', '=', 'au.std_students_id')
            ->select(
                'ss.id',
                'ss.FirstName',
                'ss.LastName',
                'ss.MiddleName',
                'ss.Suffix',
                'ss.LRN',
                'ss.Grade_level',
                'ss.Std_status',
                'ss.Last_sy_attended',
                'au.email_address',
                'au.username',
                'au.account_created'
            )
            ->orderBy('ss.id')
            ->get();

        echo "Found " . $students->count() . " student accounts.\n";

        $auditRecords = [];
        $batchSize = 500;

        foreach ($students as $index => $student) {
            // Use account_created timestamp if available, otherwise use a default timestamp
            // Since we don't have the exact creation date, we'll use a reasonable estimate
            $timestamp = $student->account_created
                ? Carbon::parse($student->account_created)
                : Carbon::now()->subMonths(6); // Default to 6 months ago if no timestamp

            // Build student name
            $fullName = trim(
                $student->FirstName . ' ' .
                ($student->MiddleName ? $student->MiddleName . ' ' : '') .
                $student->LastName .
                ($student->Suffix ? ' ' . $student->Suffix : '')
            );

            // Build the new_data field with student account details
            $newData = [
                'Student ID' => $student->id,
                'Full Name' => $fullName,
                'First Name' => $student->FirstName,
                'Last Name' => $student->LastName,
            ];

            if (!empty($student->MiddleName)) {
                $newData['Middle Name'] = $student->MiddleName;
            }

            if (!empty($student->Suffix)) {
                $newData['Suffix'] = $student->Suffix;
            }

            $newData['LRN'] = $student->LRN ?? 'N/A';
            $newData['Grade Level'] = $student->Grade_level ?? 'N/A';
            $newData['Student Status'] = $student->Std_status ?? 'N/A';
            $newData['Last SY Attended'] = $student->Last_sy_attended ?? 'N/A';

            if (!empty($student->username)) {
                $newData['Username'] = $student->username;
            }

            if (!empty($student->email_address)) {
                $newData['Email Address'] = $student->email_address;
            }

            $auditRecords[] = [
                'type' => 'INSERT',
                'old_data' => null,
                'new_data' => json_encode($newData),
                'time' => $timestamp,
                'changedBy' => 'Registrar Window',
                'fromTableName' => 'std_students',
                'description' => 'Student account created: ' . $fullName
            ];

            // Insert in batches
            if (count($auditRecords) >= $batchSize) {
                DB::table('audit_table')->insert($auditRecords);
                echo "Inserted batch of " . count($auditRecords) . " student account audits.\n";
                $auditRecords = [];
            }
        }

        // Insert remaining records
        if (count($auditRecords) > 0) {
            DB::table('audit_table')->insert($auditRecords);
            echo "Inserted final batch of " . count($auditRecords) . " student account audits.\n";
        }

        echo "Student account creation audits completed.\n\n";
    }
}
