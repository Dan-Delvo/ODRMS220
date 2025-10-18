<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentRequestController;
use App\Http\Controllers\OngoingController;
use App\Http\Controllers\PendingController;
use App\Http\Controllers\declinedController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\StudentPageController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\DocumentsModelController;
use App\Http\Controllers\GenerateRequestController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentInformationModelController;
use App\Http\Controllers\StudentRequestController;
use App\Http\Controllers\ClaimedDocumentController;
use App\Http\Controllers\AuditTableController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BulkRequest;
use App\Models\Account;
use Illuminate\Support\Facades\Mail;
use App\Models\DocumentRequestModel;
use Illuminate\Routing\RouteGroup;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Http\Controllers\FcmController;
use App\Http\Controllers\forgotpassword;
use Illuminate\Support\Facades\Http;


Route::middleware(['web'])->group(function () {
    Route::get('/back-to-login', function () {
        session()->forget(['otp', 'expiry', 'otp_attempts', 'lockout_until', 'email_entered', 'otp_requested', 'otp_verified', 'password_reset_step']); 
        return redirect()->route('login');
    })->name('otp.back');
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::post('/', [AuthController::class, 'auth_login'])->name('login.post');
    Route::get('logout', [AuthController::class, 'logout']);
});

Route::get('/student/create', [RegistrationController::class, 'create'])->name('student.create');
Route::post('/student/store', [RegistrationController::class, 'store'])->name('student.store');

Route::get('/account/otp', [AccountController::class, 'showOtp'])->name('account.create');
Route::post('/account/otp', [AccountController::class, 'viewOtp'])->name('account.otp');
Route::post('/account/verify', [AccountController::class, 'verifyOtp'])->name('account.verify');
Route::match(['get', 'post'], '/account/resend', [AccountController::class, 'SendAgainOTP'])->name('account.resend');

// New route for checking lockout status
Route::post('/account/lockout-status', [AccountController::class, 'checkLockoutStatus'])->name('account.lockout.status');


Route::post('/account/store', [AccountController::class, 'store'])->name('account.store');





Route::put('update-device-token', [FcmController::class, 'updateDeviceToken']);
Route::post('send-fcm-notification', [FcmController::class, 'sendFcmNotification']);



// Route::get('/', function () {
//     return view('layout.blankpage');
// });

Route::group(['middleware' => 'useradmin'], function () {

    // in routes/web.php
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');

    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    //Request Management        ================================================================================
    Route::resource('tables', DocumentRequestController::class);
    Route::resource('pending', PendingController::class);
    Route::resource('ongoing', OngoingController::class);
    Route::delete('pending/decline/{id}', [PendingController::class, 'decline'])->name('pending.decline');
    Route::put('/pending/completeRequest/{id}', [PendingController::class, 'completeRequest'])->name('document-request.complete');
    Route::put('/ongoing/completeRequest/{id}', [OngoingController::class, 'completeRequest'])->name('document-request2.complete');
    Route::put('/tables/completeRequest/{id}', [DocumentRequestController::class, 'completeRequest'])->name('document-request3.complete');
    Route::get('/walkin/form', [DocumentRequestController::class, 'showRequestForm'])->name('walkin.form');
    Route::post('/walkin/store', [DocumentRequestController::class, 'storeWalkIn'])->name('walkin.store');
    Route::get('/pending/ajax', [PendingController::class, 'ajaxPending'])->name('pending.ajax');
    Route::resource('claimed-documents', ClaimedDocumentController::class);
    Route::get('/declined-documents', [declinedController::class, 'index'])->name('declined-documents.index');

    Route::get('/bulk-request', [BulkRequest::class, 'index'])->name('bulk_request.index');

    // Additional custom routes for specific functionality
    Route::prefix('claimed-documents')->group(function () {
        // Revert a claimed document back to "For Release" status
        Route::put('{id}/revert', [ClaimedDocumentController::class, 'revertToForRelease'])
            ->name('claimed-documents.revert');

        // Generate report for claimed documents
        Route::post('report', [ClaimedDocumentController::class, 'generateReport'])
            ->name('claimed-documents.report');

        // Export claimed documents to CSV
        Route::post('export-csv', [ClaimedDocumentController::class, 'exportToCsv'])
            ->name('claimed-documents.export-csv');
    });

    //Request Management       ================================================================================


    //Role Maintenance         ================================================================================
    Route::get('panel/role', [RoleController::class, 'list'])->name('role');
    Route::get('panel/role/add', [RoleController::class, 'add'])->name('role.add');
    Route::post('panel/role/add', [RoleController::class, 'insert'])->name('role.insert');
    Route::get('panel/role/edit/{id}', [RoleController::class, 'edit'])->name('role.edit');
    Route::post('panel/role/edit/{id}', [RoleController::class, 'update'])->name('role.update');
    Route::delete('panel/role/delete/{id}', [RoleController::class, 'delete'])->name('role.delete');
    //Role Maintenance          ================================================================================
    Route::get('/backup/download', [BackupController::class, 'downloadBackup'])->name('backup.download');
    Route::post('/backup/restore', [BackupController::class, 'restoreBackup'])->name('backup.restore');
    //User Maintenance          ================================================================================
    Route::get('panel/user', [AccountController::class, 'display'])->name('user');
    Route::get('panel/user/edit/{id}', [AccountController::class, 'edit'])->name('user.edit');
    Route::put('panel/user/edit/{id}', [AccountController::class, 'update'])->name('user.update');
    Route::delete('panel/user/delete/{id}', [AccountController::class, 'delete'])->name('user.delete');
    Route::get('user/show/{id}', [AccountController::class, 'show'])->name('user.show');
    //User Maintenance          ================================================================================
    Route::get('userStud/add', [AccountController::class, 'addUserStud'])->name('userStud.add');
    Route::post('userStud/add', [AccountController::class, 'storeUserStud'])->name('userStud.store');
    //Student Maintenance       ================================================================================
    Route::get('panel/student', [StudentInformationModelController::class, 'display'])->name('student');
    Route::get('panel/students/edit/{id}', [StudentInformationModelController::class, 'edit'])->name('student.edit');
    Route::put('panel/students/edit/{id}', [StudentInformationModelController::class, 'update'])->name('student.update');
    Route::delete('panel/edit/delete/{id}', [StudentInformationModelController::class, 'delete'])->name('student.delete');
    Route::get('student/show/{id}', [StudentInformationModelController::class, 'show'])->name('student.show');
    //Student Maintenance       ================================================================================

    //DocType Maintenance       ================================================================================
    Route::get('panel/doc', [DocumentsModelController::class, 'display'])->name('doc');
    Route::get('panel/doc/add', [DocumentsModelController::class, 'add'])->name('doc.add');
    Route::post('panel/doc/add', [DocumentsModelController::class, 'insert'])->name('doc.insert');

    Route::get('panel/doc/edit/{id}', [DocumentsModelController::class, 'edit'])->name('doc.edit');
    Route::put('panel/doc/{id}', [DocumentsModelController::class, 'update'])->name('doc.update');
    Route::delete('panel/doc/{id}', [DocumentsModelController::class, 'destroy'])->name('doc.destroy');
    Route::post('panel/doc/{id}', [DocumentsModelController::class, 'generateCertificate'])->name('doc.print');
    //DocType Maintenance       ================================================================================

    //Report Generation         ================================================================================
    Route::get('panel/report', [GenerateRequestController::class, 'display'])->name('generate');
    Route::get('panel/pdf', [GenerateRequestController::class, 'pdfGenerator'])->name('generatePDF');
    Route::get('panel/excel', [GenerateRequestController::class, 'exportExcel'])->name('generateExcel');
    Route::get('/generate-reports', [GenerateRequestController::class, 'handleReports'])->name('generateReports');
    // Display the generate request page with optional filtering
    Route::get('/generate-reports', [GenerateRequestController::class, 'display'])
        ->name('generateReports.display');

    // Handle report generation (both PDF and Excel)
    Route::get('/generate-reports/create', [GenerateRequestController::class, 'handleReports'])
        ->name('generateReports');

    // API endpoint for status statistics (optional - for dashboard widgets)
    Route::get('/api/request-statistics', [GenerateRequestController::class, 'getStatusStatistics'])
        ->name('api.request.statistics');

    // Alternative individual routes (if you prefer separate routes)
    Route::get('/generate-reports/pdf', [GenerateRequestController::class, 'pdfGenerator'])
        ->name('generateReports.pdf');

    Route::get('/generate-reports/excel', [GenerateRequestController::class, 'exportExcel'])
        ->name('generateReports.excel');
    //Report Generation         ================================================================================

    Route::get('/auditTrail', [AuditTableController::class, 'index'])->name('audit.index');
    Route::get('/activityLog', [AuditTableController::class, 'activityLog']);
});


Route::middleware(['guest', 'forgotpassword', 'lockout'])->group(function () {
    Route::get('/forgotpassword', [forgotpassword::class, 'index'])->name('forgot');
    Route::post('/forgotpassword', [forgotpassword::class, 'forgotpost'])->name('forgot.submit');
    Route::get('/verifyotp', [forgotpassword::class, 'showVerifyOTP'])->name('verifyotp');
    Route::post('/verifyotp', [forgotpassword::class, 'verifyOTP'])->name('verifyotp.submit');
    Route::post('/resend-otp', [forgotpassword::class, 'resendOTP'])->name('resend.otp');
    Route::get('/newpassword', [forgotpassword::class, 'showNewPassword'])->name('newpassword');
    Route::post('/newpassword', [forgotpassword::class, 'newpassword'])->name('newpassword.submit');
});

Route::group(['middleware' => 'userstudent'], function () {

    Route::get('stpage', [StudentPageController::class, 'mainpage'])->name('st.page');
    // Display the document request form
    Route::get('/student-request', [StudentRequestController::class, 'create'])->name('studentrequest.create');
    Route::get('/view', [StudentRequestController::class, 'viewRequest'])->name('studentrequest.view');
    // Handle the form submission
    Route::post('/student-request', [StudentRequestController::class, 'store'])->name('studentrequest.store');
    Route::post('/save-fcm-token', [AccountController::class, 'saveFcmToken'])->name('save.fcm.token');
    Route::get('/student/profile', [StudentInformationModelController::class, 'showProfile'])->name('student.profile');
    Route::put('/student/profile/{id}', [StudentInformationModelController::class, 'updateProfile'])->name('student.profile.update');

    Route::post('/student/{id}/account/send-otp', [AccountController::class, 'accountSendOtp'])->name('student.password.sendOtp');
    Route::post('/student/{id}/account/verify-otp', [AccountController::class, 'accountVerifyOtp'])->name('student.password.verifyOtp');
    Route::put('/student/{id}/account/update-password', [AccountController::class, 'accountUpdatePassword'])->name('student.password.update');

    // Sends an Email Verification for updating profile
    Route::put('/student/profile/verify/{id}', [AccountController::class, 'verifyUpdateProfile'])->name('student.profile.verifyUpdate');
    // When clicking the Verify Email button
    Route::get('/student/profile/confirmUpdate/{token}', [AccountController::class, 'confirmUpdate'])
        ->name('student.profile.confirmUpdate');

    Route::put('/document-request/{id}/replace-file', [StudentRequestController::class, 'replaceFile'])->name('document-request.replaceFile');


    Route::get('/send-notification', function (\Illuminate\Http\Request $request) {
        $contents = $request->query('contents');
        $subscriptionIds = [$request->query('subscription_ids')];
        $url = $request->query('url');

        Route::get('hompage', function () {
            return view('common.homepage');
        });

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic os_v2_app_if32gbsxsffszlc2vzvuxojxx5v5u3kriweuqn4s2luqs6vfjt5gaoxdhoqhd6vi5w33ake2swiwgpvwudxdidn35dzpgubfyjeszsq',
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])->post('https://onesignal.com/api/v1/notifications', [
                'app_id' => '4177a306-5791-4b2c-ac5a-ae6b4bb937bf',
                'include_player_ids' => $subscriptionIds,
                'contents' => ['en' => $contents],
                'url' => $url,
            ]);

            return $response->body();
        } catch (\Exception $e) {
            report($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    });
});





//student authentication
Route::get('login', function () {
    return view('common.studentlogin');
});



Route::get('forget', function () {
    return view('common.forgetpass');
});

// Route::get('stpage', function () {
//     return view('layout.studentpage');
// });




// Route::get('admindash', function () {
//     return view('common.admin');
// });

Route::get('completed', function () {
    return view('requestTables.completed');
});

// Route::get('pending', function () {
//     return view('common.pending');
// });

// Route::get('ongoing', function () {
//     return view('common.ongoing');
// });
