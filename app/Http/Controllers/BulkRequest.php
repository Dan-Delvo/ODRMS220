<?php

namespace App\Http\Controllers;

use App\Models\BulkRequest as ModelsBulkRequest;
use Illuminate\Http\Request;

class BulkRequest extends Controller
{
    //

    public function index() {

        $requests = ModelsBulkRequest::getBulkRequest();

        return view('bulk_request.bulk_request', [
            'requests' => $requests,
        ]);
    }
}
