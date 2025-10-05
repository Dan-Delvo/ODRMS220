<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\StudentInformationModel;
use App\Models\Account;
use App\Models\DocumentRequestModel;
use Exception;

class SyncService
{
    /**
     * Check if online database is reachable
     */
    public function isOnline(): bool
    {
        try {
            DB::connection('mysql_online')->getPdo();
            return DB::connection('mysql_online')->getDatabaseName() ? true : false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Full sync: Push local changes to online AND pull online changes to local
     */
    public function syncToOnline(): array
    {
        $results = [
            'success' => false,
            'pushed' => [
                'students' => 0,
                'accounts' => 0,
                'requests' => 0,
            ],
            'failed' => 0,
            'errors' => []
        ];

        if (!$this->isOnline()) {
            $results['errors'][] = 'Online database is not reachable';
            return $results;
        }

        try {
            // ONLY PUSH - Remove the pull part
            $pushResults = $this->pushToOnline();
            $results['pushed'] = $pushResults['synced'];
            $results['failed'] += $pushResults['failed'];
            $results['errors'] = array_merge($results['errors'], $pushResults['errors']);

            $results['success'] = true;

        } catch (Exception $e) {
            $results['errors'][] = 'Sync failed: ' . $e->getMessage();
            Log::error('Sync transaction failed', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Push local unsynced data to online database
     */
    protected function pushToOnline(): array
    {
        $results = [
            'synced' => [
                'students' => 0,
                'accounts' => 0,
                'requests' => 0,
            ],
            'failed' => 0,
            'errors' => []
        ];

        try {
            // Don't use transaction - sync each table independently

            // 1. Push Students
            $studentResults = $this->pushStudents();
            $results['synced']['students'] = $studentResults['synced'];
            $results['failed'] += $studentResults['failed'];
            $results['errors'] = array_merge($results['errors'], $studentResults['errors']);

            // 2. Push Accounts (only if students synced successfully)
            if ($studentResults['synced'] > 0) {
                $accountResults = $this->pushAccounts();
                $results['synced']['accounts'] = $accountResults['synced'];
                $results['failed'] += $accountResults['failed'];
                $results['errors'] = array_merge($results['errors'], $accountResults['errors']);
            }

            // 3. Push Document Requests (only if students synced)
            if ($studentResults['synced'] > 0) {
                $requestResults = $this->pushDocumentRequests();
                $results['synced']['requests'] = $requestResults['synced'];
                $results['failed'] += $requestResults['failed'];
                $results['errors'] = array_merge($results['errors'], $requestResults['errors']);
            }

        } catch (Exception $e) {
            $results['errors'][] = 'Push failed: ' . $e->getMessage();
            Log::error('Push failed', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Pull new data from online database to local
     */
    protected function pullFromOnline(): array
    {
        $results = [
            'synced' => [
                'students' => 0,
                'acc_users' => 0,
                'requests' => 0,
            ],
            'failed' => 0,
            'errors' => []
        ];

        try {
            // Get last sync timestamp from local database
            $lastSync = DB::connection('sqlite_local')
                ->table('sync_log')
                ->latest('synced_at')
                ->value('synced_at') ?? '2000-01-01 00:00:00';

            // Pull new students from online
            $newStudents = DB::connection('mysql')
                ->table('std_students')
                ->where('created_at', '>', $lastSync)
                ->get();

            foreach ($newStudents as $student) {
                try {
                    // Check if already exists locally
                    $exists = DB::connection('sqlite_local')
                        ->table('std_students')
                        ->where('online_id', $student->id)
                        ->exists();

                    if (!$exists) {
                        $data = (array) $student;
                        $onlineId = $data['id'];
                        unset($data['id']);

                        $data['online_id'] = $onlineId;
                        $data['synced'] = true;
                        $data['synced_at'] = now();

                        DB::connection('sqlite_local')
                            ->table('std_students')
                            ->insert($data);

                        $results['synced']['students']++;
                    }
                } catch (Exception $e) {
                    $results['failed']++;
                    Log::error('Failed to pull student', ['error' => $e->getMessage()]);
                }
            }

            // Similar logic for acc_users and requests...
            // (abbreviated for brevity)

            // Log successful sync
            DB::connection('sqlite_local')
                ->table('sync_log')
                ->insert([
                    'synced_at' => now(),
                    'direction' => 'pull',
                    'status' => 'success'
                ]);

        } catch (Exception $e) {
            $results['errors'][] = 'Pull failed: ' . $e->getMessage();
            Log::error('Pull failed', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Push students to online database
     */
    protected function pushStudents(): array
    {
        $results = ['synced' => 0, 'failed' => 0, 'errors' => []];

        $unsyncedStudents = DB::connection('mysql_local')
            ->table('std_students')
            ->where('synced', false)
            ->get();

        foreach ($unsyncedStudents as $student) {
            try {
                $data = (array) $student;

                // Keep the ID but remove sync columns
                unset($data['synced'], $data['needs_sync'], $data['synced_at'], $data['online_id']);

                // Check if already exists in online database
                $exists = DB::connection('mysql_online')
                    ->table('std_students')
                    ->where('id', $student->id)
                    ->exists();

                if (!$exists) {
                    // Insert to online database with the same ID
                    DB::connection('mysql_online')
                        ->table('std_students')
                        ->insert($data);
                }

                // Mark as synced in local
                DB::connection('mysql_local')
                    ->table('std_students')
                    ->where('id', $student->id)
                    ->update([
                        'synced' => true,
                        'synced_at' => now(),
                        'online_id' => $student->id
                    ]);

                $results['synced']++;
            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Failed to push student ID {$student->id}: " . $e->getMessage();
                Log::error("Student push failed", ['id' => $student->id, 'error' => $e->getMessage()]);
            }
        }

        return $results;
    }

    /**
     * Push acc_users to online database
     */
    protected function pushAccounts(): array
    {
        $results = ['synced' => 0, 'failed' => 0, 'errors' => []];

        $unsyncedAccounts = DB::connection('mysql_local')
            ->table('acc_users')
            ->where('synced', false)
            ->get();

        foreach ($unsyncedAccounts as $account) {
            try {
                $data = (array) $account;

                // Remove only sync columns, keep user_account_id
                unset($data['synced'], $data['needs_sync'], $data['synced_at'], $data['online_id']);

                // Check if exists
                $exists = DB::connection('mysql_online')
                    ->table('acc_users')
                    ->where('user_account_id', $account->user_account_id)
                    ->exists();

                if (!$exists) {
                    DB::connection('mysql_online')
                        ->table('acc_users')
                        ->insert($data);

                    // Send temp password email
                    $this->sendTempPasswordEmail($account);
                }

                // Mark as synced
                DB::connection('mysql_local')
                    ->table('acc_users')
                    ->where('user_account_id', $account->user_account_id)
                    ->update([
                        'synced' => true,
                        'synced_at' => now(),
                        'online_id' => $account->user_account_id
                    ]);

                $results['synced']++;
            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Failed to push account: " . $e->getMessage();
                Log::error("Account push failed", ['error' => $e->getMessage()]);
            }
        }

        return $results;
    }

    /**
     * Push document requests to online database
     */
    protected function pushDocumentRequests(): array
    {
        $results = ['synced' => 0, 'failed' => 0, 'errors' => []];

        $unsyncedRequests = DB::connection('mysql_local')
            ->table('doc_requests')
            ->where('synced', false)
            ->get();

        foreach ($unsyncedRequests as $request) {
            try {
                $data = (array) $request;

                // Remove only sync columns
                unset($data['synced'], $data['needs_sync'], $data['synced_at'], $data['online_id']);

                // Check if exists
                $exists = DB::connection('mysql_online')
                    ->table('doc_requests')
                    ->where('id', $request->id)
                    ->exists();

                if (!$exists) {
                    DB::connection('mysql_online')
                        ->table('doc_requests')
                        ->insert($data);
                }

                // Mark as synced
                DB::connection('mysql_local')
                    ->table('doc_requests')
                    ->where('id', $request->id)
                    ->update([
                        'synced' => true,
                        'synced_at' => now(),
                        'online_id' => $request->id
                    ]);

                $results['synced']++;
            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Failed to push request: " . $e->getMessage();
                Log::error("Request push failed", ['error' => $e->getMessage()]);
            }
        }

        return $results;
    }

    /**
     * Send temporary password email after sync
     */
    protected function sendTempPasswordEmail($account)
    {
        try {
            $student = StudentInformationModel::on('sqlite_local')
                ->where('id', $account->std_students_id)
                ->first();

            $name = $student->FirstName . ' ' . $student->LastName;
            $email = $account->email_address;
            $tempPassword = \Illuminate\Support\Str::random(10);

            // Update password in online database
            DB::connection('mysql')
                ->table('acc_users')
                ->where('email_address', $email)
                ->update(['password' => bcrypt($tempPassword)]);

            $subject = 'Your Temporary Password';

            Mail::send('emails.tempPassword', compact('subject', 'name', 'tempPassword', 'email'), function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });
        } catch (Exception $e) {
            Log::error("Failed to send email to {$account->email_address}: " . $e->getMessage());
        }
    }

    /**
     * Get sync status
     */
    public function getSyncStatus(): array
    {
        try {
            $pendingStudents = DB::connection('mysql_local')
                ->table('std_students')
                ->where('synced', false)
                ->count();

            $pendingAccounts = DB::connection('mysql_local')
                ->table('acc_users')
                ->where('synced', false)
                ->count();

            $pendingRequests = DB::connection('mysql_local')
                ->table('doc_requests')
                ->where('synced', false)
                ->count();

            return [
                'pending_students' => $pendingStudents,
                'pending_accounts' => $pendingAccounts,
                'pending_requests' => $pendingRequests,
                'total_pending' => $pendingStudents + $pendingAccounts + $pendingRequests,
                'is_online' => $this->isOnline()
            ];
        } catch (\Exception $e) {
            Log::error('getSyncStatus Error: ' . $e->getMessage());

            return [
                'pending_students' => 0,
                'pending_accounts' => 0,
                'pending_requests' => 0,
                'total_pending' => 0,
                'is_online' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
