<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\DocumentRequestModel;
use App\Models\DocumentsModel;
use App\Models\DocuPaymentFee;
use Illuminate\Http\Request;


class AnalyticsController extends Controller
{

    public function index(Request $request)
    {
        $startDate = $request->input('start_date') ?? Carbon::now()->startOfYear()->toDateString();
        $endDate = $request->input('end_date') ?? Carbon::now()->endOfYear()->toDateString();

        // Monthly Document Requests (FILTERED)
        $monthlyRequestsData = DB::table('doc_requests')
            ->select(DB::raw("MONTH(request_date) as month"), DB::raw("COUNT(*) as count"))
            ->whereBetween('request_date', [$startDate, $endDate])
            ->groupBy(DB::raw("MONTH(request_date)"))
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function ($item) {
                return [Carbon::create()->month($item->month)->format('F') => $item->count];
            });
        // Total for label
        $totalRequestsInInterval = array_sum($monthlyRequestsData->values()->toArray());


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

        $unclaimedData = DB::table('doc_requests')
            ->select(DB::raw("MONTH(request_date) as month"), DB::raw("COUNT(*) as count"))
            ->whereNotNull('forRelease_date')
            ->whereNull('claimed_date') // Assuming this means unclaimed
            ->groupBy(DB::raw("MONTH(forRelease_date)"))
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function ($item) {
                return [Carbon::create()->month($item->month)->format('F') => $item->count];
            });

        return view('common.analyticsDashboard', [
            'monthlyRequestsData' => $monthlyRequestsData,
            'docTypeData' => $docTypeData,
            'modeData' => $modeData,
            'revenueData' => $revenueData,
            'unclaimedData' => $unclaimedData,
            'totalRequestsInInterval' => $totalRequestsInInterval,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
}
