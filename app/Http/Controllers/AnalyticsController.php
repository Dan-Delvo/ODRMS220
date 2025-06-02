<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\DocumentRequestModel;
use App\Models\DocumentsModel;
use App\Models\DocuPaymentFee;



class AnalyticsController extends Controller
{

public function index()
{
    // Monthly Request Count


    $monthlyRequestsData = DB::table('doc_requests')
        ->select(DB::raw("MONTH(request_date) as month"), DB::raw("COUNT(*) as count"))
        ->groupBy(DB::raw("MONTH(request_date)"))
        ->orderBy('month')
        ->get()
        ->mapWithKeys(function ($item) {
            return [Carbon::create()->month($item->month)->format('F') => $item->count];
    });


    // Document Type Distribution
    $docTypeData = DB::table('doc_requests')
        ->join('doc_categories', 'doc_requests.doc_categories_id', '=', 'doc_categories.id')
        ->select('doc_categories.DocType', DB::raw('COUNT(*) as count'))
        ->groupBy('doc_categories.DocType')
        ->get()
        ->pluck('count', 'DocType');


    // Request Mode Distribution (walk-in / online)
    $modeData = DB::table('doc_requests')
        ->select('request_mode', DB::raw('COUNT(*) as count'))
        ->groupBy('request_mode')
        ->get()
        ->pluck('count', 'request_mode');


    // Monthly Revenue
    $revenueData = DocuPaymentFee::select(
            DB::raw("MONTH(time_request) as month"),
            DB::raw("SUM(doc_amount) as total")
        )
        ->groupBy(DB::raw("MONTH(time_request)"))
        ->orderBy('month')
        ->get()
        ->mapWithKeys(function ($item) {
            return [Carbon::create()->month($item->month)->format('F') => $item->total];
        });


    return view('common.analyticsDashboard', [
        'monthlyRequestsData' => $monthlyRequestsData,
        'docTypeData' => $docTypeData,
        'modeData' => $modeData,
        'revenueData' => $revenueData
    ]);

}

}
