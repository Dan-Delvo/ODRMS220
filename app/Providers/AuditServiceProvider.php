<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuditServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Listen to database query events for debugging
        DB::listen(function ($query) {
            // Log queries that affect doc_requests table
            if (str_contains(strtolower($query->sql), 'doc_requests')) {
                try {
                    $currentUser = DB::select("SELECT @current_user as current_user")[0]->current_user ?? 'not_set';

                    Log::info('Doc requests query detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'current_user_var' => $currentUser,
                        'connection' => $query->connectionName ?? 'default'
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Failed to check @current_user during query', [
                        'error' => $e->getMessage()
                    ]);
                }
            }
        });
    }
}
