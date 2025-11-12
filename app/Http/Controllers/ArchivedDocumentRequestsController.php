<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArchivedDocumentRequestsController extends Controller
{
    //

    public function pending(){
        return view('archiveDocRequests.pending');
    }
}
