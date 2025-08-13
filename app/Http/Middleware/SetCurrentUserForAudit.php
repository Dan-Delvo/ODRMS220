<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SetCurrentUserForAudit
{
    public function handle($request, Closure $next)
    {
        $username = 'system'; // Default value

        try {
            if (Auth::check()) {
                $user = Auth::user();
                $username = $user->username ?? $user->email ?? 'user_' . $user->id;

                Log::info('Authenticated user detected for audit', [
                    'user_id' => $user->id,
                    'username' => $username,
                    'auth_guard' => Auth::getDefaultDriver(),
                    'route' => $request->route()?->getName() ?? $request->path(),
                ]);
            } else {
                $username = 'guest';
                Log::info('No authenticated user - using guest');
            }

            // Set on default connection
            DB::statement("SET @current_user = ?", [$username]);

            // Store in config as backup
            config(['audit.current_user' => $username]);

            // Verify it was set
            $verification = DB::select("SELECT @current_user as current_user")[0]->current_user;

            Log::info('Audit user session variable set', [
                'set_to' => $username,
                'verified_as' => $verification,
                'match' => $username === $verification
            ]);

        } catch (\Exception $e) {
            Log::error('Audit middleware error', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            // Emergency fallback
            try {
                DB::statement("SET @current_user = ?", ['system']);
                config(['audit.current_user' => 'system']);
            } catch (\Exception $fallbackError) {
                Log::critical('Failed to set fallback audit user', [
                    'error' => $fallbackError->getMessage()
                ]);
            }
        }

        $response = $next($request);

        // Optional: Log after request processing
        try {
            $finalCheck = DB::select("SELECT @current_user as current_user")[0]->current_user;
            Log::info('Audit user after request processing', [
                'final_value' => $finalCheck
            ]);
        } catch (\Exception $e) {
            // Ignore errors in final check
        }

        return $response;
    }
}
