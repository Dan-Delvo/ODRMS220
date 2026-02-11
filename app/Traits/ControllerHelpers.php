<?php
namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait ControllerHelpers
{

    public function setCurrentVariable() {
        $username = Auth::check() ? Auth::user()->username : 'guest';
        DB::connection()->getPdo()->exec("SET @current_user = " . DB::connection()->getPdo()->quote($username));
    }
    
}
