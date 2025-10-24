<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ResetLockouts extends Command
{
    protected $signature = 'lockout:reset {type?} {value?}';
    protected $description = 'Reset lockout counters for testing';

    public function handle()
    {
        $type = $this->argument('type');
        $value = $this->argument('value');

        if (!$type) {
            // Clear ALL lockouts
            Cache::flush();
            $this->info('All lockouts cleared!');
            return;
        }

        switch ($type) {
            case 'email':
                Cache::forget("locked:email:{$value}");
                Cache::forget("attempts:email:{$value}");
                Cache::forget("lockout_count:email:{$value}");
                $this->info("Email lockout cleared for: {$value}");
                break;

            case 'ip':
                Cache::forget("locked:ip:{$value}");
                Cache::forget("attempts:ip:{$value}");
                Cache::forget("lockout_count:ip:{$value}");
                $this->info("IP lockout cleared for: {$value}");
                break;

            case 'device':
                Cache::forget("locked:device:{$value}");
                Cache::forget("attempts:device:{$value}");
                Cache::forget("lockout_count:device:{$value}");
                $this->info("Device lockout cleared for: {$value}");
                break;

            default:
                $this->error('Invalid type. Use: email, ip, or device');
        }
    }
}