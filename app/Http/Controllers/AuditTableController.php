<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditTable;


class AuditTableController extends Controller
{
    //
    public function index()
    {
        $auditTrail = AuditTable::orderBy('time', 'desc')->paginate(10);
        $totalCount = AuditTable::count();

        return view('common.auditTrail', [
            'auditTrail' => $auditTrail,
            'totalCount' => $totalCount,
        ]);
    }

    public function activityLog()
    {
        return view('common.activityLog');
    }
}
