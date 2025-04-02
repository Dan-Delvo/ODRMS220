<?php

namespace App\Http\Controllers;

use App\Models\StudentInformationModel;
use App\Models\DocumentRequestModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentPageController extends Controller
{
    //
    public function mainpage(){
        $studID = Auth::user()->std_students_id;

        $studInfo = StudentInformationModel::Find($studID);
        $DocRequests = DocumentRequestModel::where('std_students_id', $studID)
        ->with('claimer')
        ->with('studentInformation')
        ->paginate(9);;

        return view('common.studentDashboard', compact('studInfo', 'DocRequests'));
    }
}
