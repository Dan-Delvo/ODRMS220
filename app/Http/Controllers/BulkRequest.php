<?php

namespace App\Http\Controllers;

use App\Models\BulkRequest as ModelsBulkRequest;
use App\Models\BulkStudent;
use Illuminate\Http\Request;

class BulkRequest extends Controller
{
    //

    public function index() {

        $requests = ModelsBulkRequest::getBulkRequest();
        $student = BulkStudent::getStudent();

        return view('bulk_request.bulk_request', [
            'requests' => $requests,
            'students' => $student,
        ]);
    }

    public function moveToProcessing($Request_ID) {

        ModelsBulkRequest::moveRequest('Processing', $Request_ID);

        return redirect('/bulk-request')->with('Success', 'Moved Successfully');
    }

    public function moveToForRelease($Request_ID) {

        ModelsBulkRequest::moveRequest('For Release', $Request_ID);

        return redirect('/bulk-request')->with('Success', 'Moved Successfully');
    }

    public function moveToClaimed($Request_ID) {

        ModelsBulkRequest::moveRequest('Claimed', $Request_ID);

        return redirect('/bulk-request')->with('Success', 'Moved Successfully');
    }

}
