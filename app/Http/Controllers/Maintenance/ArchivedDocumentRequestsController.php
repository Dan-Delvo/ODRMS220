<?php

namespace App\Http\Controllers\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ArchivedDocumentRequestsController extends Controller
{
    //

    public function pending(){
        return view('archiveDocRequests.pending');
    }
}
