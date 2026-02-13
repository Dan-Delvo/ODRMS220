<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AiGenerate;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\DocumentRequestModel;
use App\Models\DocumentsModel;
use App\Models\DocuPaymentFee;
use App\Models\PermissionRoleModel;
use Exception;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Gemini\Exceptions\ErrorException as GeminiException;
use OpenAI\Laravel\Facades\OpenAI;

class AnalyticsController extends Controller
{

    public function index(Request $request)
    {
        $PermissionAnalytics = PermissionRoleModel::getPermission('analytics', Auth::user()->role_id);
        if (empty($PermissionAnalytics)) {
            abort(404);
        }

        // Get the earliest request date from the database for smart defaults
        $earliestDate = DB::table('doc_requests')->min('request_date');
        
        // Default to earliest data date if exists, otherwise current year
        if (!$request->input('start_date') && $earliestDate) {
            $startDate = Carbon::parse($earliestDate)->startOfYear()->toDateString();
        } else {
            $startDate = $request->input('start_date') ?? Carbon::now()->startOfYear()->toDateString();
        }
        $endDate = $request->input('end_date') ?? Carbon::now()->endOfYear()->toDateString();

        // Ensure dates are properly formatted (handle cases where only a year is passed)
        if (strlen($startDate) === 4) {
            $startDate = $startDate . '-01-01';
        }
        if (strlen($endDate) === 4) {
            $endDate = $endDate . '-12-31';
        }

        // Ensure end date includes the full day
        $endDateForQuery = Carbon::parse($endDate)->endOfDay()->toDateTimeString();

        // Monthly Document Requests (FILTERED) - only once, no duplicate
        $monthlyRequestsData = DB::table('doc_requests')
            ->select(
                DB::raw("YEAR(request_date) as year"),
                DB::raw("MONTH(request_date) as month"),
                DB::raw("COUNT(*) as count")
            )
            ->whereBetween('request_date', [$startDate, $endDateForQuery])
            ->groupBy(DB::raw("YEAR(request_date)"), DB::raw("MONTH(request_date)"))
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Format labels based on whether data spans multiple years
        $startYear = Carbon::parse($startDate)->year;
        $endYear = Carbon::parse($endDate)->year;

        if ($startYear === $endYear) {
            // Single year: just month names
            $monthlyRequestsData = $monthlyRequestsData->mapWithKeys(function ($item) {
                return [Carbon::create()->month($item->month)->format('F') => $item->count];
            });
        } else {
            // Multiple years: include year in label
            $monthlyRequestsData = $monthlyRequestsData->mapWithKeys(function ($item) {
                $monthName = Carbon::create()->month($item->month)->format('M');
                return [$monthName . ' ' . $item->year => $item->count];
            });
        }

        // Total for label
        $totalRequestsInInterval = $monthlyRequestsData->sum();

        // Yearly Document Requests
        $yearlyRequestsData = DB::table('doc_requests')
            ->select(DB::raw("YEAR(request_date) as year"), DB::raw("COUNT(*) as count"))
            ->groupBy(DB::raw("YEAR(request_date)"))
            ->orderBy('year')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->year => $item->count];
            });

        // Document Type Distribution
        $docTypeData = DB::table('doc_requests')
            ->join('doc_categories', 'doc_requests.doc_categories_id', '=', 'doc_categories.id')
            ->select('doc_categories.DocType', DB::raw('COUNT(*) as count'))
            ->whereBetween('request_date', [$startDate, $endDateForQuery])
            ->groupBy('doc_categories.DocType')
            ->get()
            ->pluck('count', 'DocType');

        // Request Mode Distribution (walk-in / online)
        $modeData = DB::table('doc_requests')
            ->select('request_mode', DB::raw('COUNT(*) as count'))
            ->whereBetween('request_date', [$startDate, $endDateForQuery])
            ->groupBy('request_mode')
            ->get()
            ->pluck('count', 'request_mode');

        // Monthly Revenue
        $revenueData = DocuPaymentFee::select(
            DB::raw("MONTH(time_request) as month"),
            DB::raw("SUM(doc_amount) as total")
        )
            ->whereBetween('time_request', [$startDate, $endDateForQuery])
            ->groupBy(DB::raw("MONTH(time_request)"))
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function ($item) {
                return [Carbon::create()->month($item->month)->format('F') => $item->total];
            });

        $gradeLevelData = DocumentRequestModel::join('std_students', 'doc_requests.std_students_id', '=', 'std_students.id')
            ->select('std_students.Grade_level', DB::raw('COUNT(*) as count'))
            ->whereNotNull('std_students.Grade_level')
            ->whereBetween('doc_requests.request_date', [$startDate, $endDateForQuery])
            ->groupBy('std_students.Grade_level')
            ->orderBy('std_students.Grade_level')
            ->get()
            ->pluck('count', 'Grade_level');

        $unclaimedData = DB::table('doc_requests')
            ->select(DB::raw("MONTH(forRelease_date) as month"), DB::raw("COUNT(*) as count"))
            ->whereNotNull('forRelease_date')
            ->whereNull('claimed_date')
            ->whereBetween('request_date', [$startDate, $endDateForQuery])
            ->groupBy(DB::raw("MONTH(forRelease_date)"))
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function ($item) {
                return [Carbon::create()->month($item->month)->format('F') => $item->count];
            });

        // Yearly unclaimed data
        $unclaimedYearlyData = DB::table('doc_requests')
            ->select(DB::raw("YEAR(forRelease_date) as year"), DB::raw("COUNT(*) as count"))
            ->whereNotNull('forRelease_date')
            ->whereNull('claimed_date')
            ->groupBy(DB::raw("YEAR(forRelease_date)"))
            ->orderBy('year')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->year => $item->count];
            });

        return view('common.analyticsDashboard', [
            'monthlyRequestsData' => $monthlyRequestsData,
            'yearlyRequestsData' => $yearlyRequestsData,
            'docTypeData' => $docTypeData,
            'modeData' => $modeData,
            'revenueData' => $revenueData,
            'unclaimedData' => $unclaimedData,
            'unclaimedYearlyData' => $unclaimedYearlyData,
            'gradeLevelData' => $gradeLevelData,
            'totalRequestsInInterval' => $totalRequestsInInterval,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    /**
     * Export all analytics data as structured JSON for AI consumption.
     */
    public function exportForAI(Request $request)
    {
        // Date range (defaults to all time if not specified)
        $startDate = $request->input('start_date') ?? '2000-01-01';
        $endDate = $request->input('end_date') ?? Carbon::now()->endOfYear()->toDateString();

        if (strlen($startDate) === 4) {
            $startDate = $startDate . '-01-01';
        }
        if (strlen($endDate) === 4) {
            $endDate = $endDate . '-12-31';
        }

        $endDateForQuery = Carbon::parse($endDate)->endOfDay()->toDateTimeString();

        // --- 1. Monthly Document Requests ---
        $monthlyRequests = DB::table('doc_requests')
            ->select(
                DB::raw("YEAR(request_date) as year"),
                DB::raw("MONTH(request_date) as month"),
                DB::raw("COUNT(*) as count")
            )
            ->whereBetween('request_date', [$startDate, $endDateForQuery])
            ->groupBy(DB::raw("YEAR(request_date)"), DB::raw("MONTH(request_date)"))
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'year' => (int) $item->year,
                    'month' => (int) $item->month,
                    'month_name' => Carbon::create()->month($item->month)->format('F'),
                    'count' => (int) $item->count,
                ];
            });

        // --- 2. Yearly Document Requests ---
        $yearlyRequests = DB::table('doc_requests')
            ->select(DB::raw("YEAR(request_date) as year"), DB::raw("COUNT(*) as count"))
            ->groupBy(DB::raw("YEAR(request_date)"))
            ->orderBy('year')
            ->get()
            ->map(function ($item) {
                return [
                    'year' => (int) $item->year,
                    'count' => (int) $item->count,
                ];
            });

        // --- 3. Document Type Distribution ---
        $docTypeDistribution = DB::table('doc_requests')
            ->join('doc_categories', 'doc_requests.doc_categories_id', '=', 'doc_categories.id')
            ->select('doc_categories.DocType', DB::raw('COUNT(*) as count'))
            ->whereBetween('request_date', [$startDate, $endDateForQuery])
            ->groupBy('doc_categories.DocType')
            ->orderByDesc('count')
            ->get()
            ->map(function ($item) {
                return [
                    'document_type' => $item->DocType,
                    'count' => (int) $item->count,
                ];
            });

        // --- 4. Request Mode Distribution ---
        $requestModes = DB::table('doc_requests')
            ->select('request_mode', DB::raw('COUNT(*) as count'))
            ->whereBetween('request_date', [$startDate, $endDateForQuery])
            ->groupBy('request_mode')
            ->orderByDesc('count')
            ->get()
            ->map(function ($item) {
                return [
                    'mode' => $item->request_mode,
                    'count' => (int) $item->count,
                ];
            });

        // --- 5. Monthly Revenue ---
        $monthlyRevenue = DocuPaymentFee::select(
                DB::raw("YEAR(time_request) as year"),
                DB::raw("MONTH(time_request) as month"),
                DB::raw("SUM(doc_amount) as total_revenue"),
                DB::raw("COUNT(*) as transaction_count")
            )
            ->whereBetween('time_request', [$startDate, $endDateForQuery])
            ->groupBy(DB::raw("YEAR(time_request)"), DB::raw("MONTH(time_request)"))
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'year' => (int) $item->year,
                    'month' => (int) $item->month,
                    'month_name' => Carbon::create()->month($item->month)->format('F'),
                    'total_revenue' => (float) $item->total_revenue,
                    'transaction_count' => (int) $item->transaction_count,
                ];
            });

        // --- 6. Grade Level Distribution ---
        $gradeLevelDistribution = DocumentRequestModel::join('std_students', 'doc_requests.std_students_id', '=', 'std_students.id')
            ->select('std_students.Grade_level', DB::raw('COUNT(*) as count'))
            ->whereNotNull('std_students.Grade_level')
            ->whereBetween('doc_requests.request_date', [$startDate, $endDateForQuery])
            ->groupBy('std_students.Grade_level')
            ->orderBy('std_students.Grade_level')
            ->get()
            ->map(function ($item) {
                return [
                    'grade_level' => $item->Grade_level,
                    'count' => (int) $item->count,
                ];
            });

        // --- 7. Unclaimed Documents ---
        $unclaimedMonthly = DB::table('doc_requests')
            ->select(
                DB::raw("YEAR(forRelease_date) as year"),
                DB::raw("MONTH(forRelease_date) as month"),
                DB::raw("COUNT(*) as count")
            )
            ->whereNotNull('forRelease_date')
            ->whereNull('claimed_date')
            ->whereBetween('request_date', [$startDate, $endDateForQuery])
            ->groupBy(DB::raw("YEAR(forRelease_date)"), DB::raw("MONTH(forRelease_date)"))
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'year' => (int) $item->year,
                    'month' => (int) $item->month,
                    'month_name' => Carbon::create()->month($item->month)->format('F'),
                    'count' => (int) $item->count,
                ];
            });

        // --- 8. Status Breakdown ---
        $statusBreakdown = DB::table('doc_requests')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('request_date', [$startDate, $endDateForQuery])
            ->groupBy('status')
            ->orderByDesc('count')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => $item->status,
                    'count' => (int) $item->count,
                ];
            });

        // --- 9. Summary Stats ---
        $totalRequests = DB::table('doc_requests')
            ->whereBetween('request_date', [$startDate, $endDateForQuery])
            ->count();

        $totalRevenue = DocuPaymentFee::whereBetween('time_request', [$startDate, $endDateForQuery])
            ->sum('doc_amount');

        $totalUnclaimed = DB::table('doc_requests')
            ->whereNotNull('forRelease_date')
            ->whereNull('claimed_date')
            ->whereBetween('request_date', [$startDate, $endDateForQuery])
            ->count();

        $avgProcessingDays = DB::table('doc_requests')
            ->whereNotNull('request_date')
            ->whereNotNull('forRelease_date')
            ->whereBetween('request_date', [$startDate, $endDateForQuery])
            ->selectRaw('AVG(DATEDIFF(forRelease_date, request_date)) as avg_days')
            ->value('avg_days');

        // --- Build JSON Response ---
        return response()->json([
            'meta' => [
                'description' => 'Online Document Request Management System - Analytics Data',
                'generated_at' => Carbon::now()->toIso8601String(),
                'date_range' => [
                    'start' => $startDate,
                    'end' => $endDate,
                ],
            ],
            'summary' => [
                'total_requests' => $totalRequests,
                'total_revenue' => round((float) $totalRevenue, 2),
                'total_unclaimed' => $totalUnclaimed,
                'avg_processing_days' => $avgProcessingDays ? round((float) $avgProcessingDays, 1) : null,
            ],
            'monthly_requests' => $monthlyRequests->values(),
            'yearly_requests' => $yearlyRequests->values(),
            'document_type_distribution' => $docTypeDistribution->values(),
            'request_mode_distribution' => $requestModes->values(),
            'monthly_revenue' => $monthlyRevenue->values(),
            'grade_level_distribution' => $gradeLevelDistribution->values(),
            'unclaimed_documents' => $unclaimedMonthly->values(),
            'status_breakdown' => $statusBreakdown->values(),
        ], 200, [], JSON_PRETTY_PRINT);
    }


    public function generateAI()
    {
        $existing = AiGenerate::whereDate('date_generated', Carbon::today())->latest('date_generated')->first();

        if($existing) {
            return response()->json([
                'success' => 'already generated',
                'data' => $existing->ai_output
            ]);
        }

        $startDate = '2025-01-01';
        $endDate = Carbon::now()->toDateString();
        $currentMonth = Carbon::now()->format('F Y');

        $dateRange = new Request([
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        $data = $this->exportForAI($dateRange)->getContent();

        if (empty($data) || $data === '[]') {
            return response()->json(['error' => 'No data found for the selected period.'], 400);
        }

        $prompt = <<<PROMPT
            You are a data analyst for a public high school's Online Document Request Management System (ODRMS).
            This is a public school — there are NO fees or payments. Completely ignore any revenue or payment data.

            Current date: {$currentMonth}
            Data period: {$startDate} to {$endDate}

            DATA:
            {$data}

            Return a JSON object with exactly these 5 keys. Each value should be a string with clear, detailed bullet points using actual numbers from the data:

            {
                "overview": "A 2-3 sentence summary with total requests, most requested document type, dominant request mode (online vs walk-in), and the grade level with the most requests.",

                "busiestMonths": "Rank ALL months from highest to lowest with request counts. Explain why peak months are high (enrollment, graduation, college apps, etc). Note the slowest months and why.",

                "trends": "Is volume increasing, decreasing, or stable month-over-month? Which document types are growing or declining? Online vs walk-in ratio and digital adoption trend. Which grade levels drive the most requests and why.",

                "forecast": "Predict expected volume for {$currentMonth} and the next 3 months based on patterns. Flag upcoming peak periods the registrar should prepare for. Suggest specific staffing adjustments with numbers.",

                "processAndOperations": "State the average processing time and whether it is acceptable. Unclaimed documents: total count, percentage of completed requests, worst months. Give 2-3 actionable recommendations to reduce unclaimed documents and improve processing."
            }

            Rules:
            - Use actual numbers in every bullet point
            - Compare months to each other
            - Be specific, not generic
            - Do NOT mention revenue, payments, or fees
            - Do not criticize the registrar staff
            - Return ONLY valid JSON, no markdown, no extra text
        PROMPT;

        try {
            $result = OpenAI::chat()->create([
                'model' => 'google/gemini-2.0-flash-001',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $content = $result->choices[0]->message->content;

            // Strip markdown code fences if present
            $content = preg_replace('/```json\s*/', '', $content);
            $content = preg_replace('/```\s*/', '', $content);
            $content = trim($content);

            $parsed = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'error' => 'Failed to parse AI response',
                    'raw' => $content
                ], 500);
            }

            $output = [
                        'overview' => $parsed['overview'] ?? null,
                        'busiestMonths' => $parsed['busiestMonths'] ?? null,
                        'trends' => $parsed['trends'] ?? null,
                        'forecast' => $parsed['forecast'] ?? null,
                        'processAndOperations' => $parsed['processAndOperations'] ?? null,
                        'generated_at' => Carbon::now()->toIso8601String(),
                        'data_period' => [
                            'start' => $startDate,
                            'end' => $endDate
                        ]
                    ];

            AiGenerate::insertOutput($output);

            return response()->json([
                'success' => 'success'
            ], 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'AI Error: ' . $e->getMessage()], 503);
        }
    }

    public function getLatestAI()
    {
        $latest = AiGenerate::latest('date_generated')->first();

        if (!$latest) {
            return response()->json(['error' => 'No AI report found.'], 404);
        }

        return response()->json([
            'data' => $latest->ai_output,
            'generated_at' => $latest->date_generated
        ], 200);
    }
    
}

