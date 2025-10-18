<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BulkRequest extends Controller
{
    //

    public function index() {
        return view('bulk_request.bulk_request');
    }
}
