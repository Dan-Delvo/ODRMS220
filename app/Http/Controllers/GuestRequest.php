<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuestRequest extends Controller
{
    //

    public function index(){
        return view('common.guest_request');
    }

    public function insertRequest(){
        
    }
}
