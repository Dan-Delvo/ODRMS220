<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentRequestController;
use App\Http\Controllers\OngoingController;
use App\Http\Controllers\PendingController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\StudentPageController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\DocumentsModelController;
use App\Http\Controllers\GenerateRequestController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentInformationModelController;
use App\Http\Controllers\StudentRequestController;
use App\Models\Account;
use Illuminate\Support\Facades\Mail;
use App\Models\DocumentRequestModel;
use Illuminate\Routing\RouteGroup;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Http\Controllers\FcmController;
use App\Http\Controllers\forgotpassword;
use Illuminate\Support\Facades\Http;

Route::get('/', [AuthController::class, 'login'])->name('login'); // Para sa login

Route::get('logout', [AuthController::class, 'logout']); // Para sa logout

Route::post('/', [AuthController::class, 'auth_login']); //  Authentication sa database

Route::get('/student/create', [RegistrationController::class, 'create'])->name('student.create');
Route::post('/student/store', [RegistrationController::class, 'store'])->name('student.store');

Route::get('/account/create', [AccountController::class, 'create'])->name('account.create');
Route::post('/account/store', [AccountController::class, 'store'])->name('account.store');





Route::put('update-device-token', [FcmController::class, 'updateDeviceToken']);
Route::post('send-fcm-notification', [FcmController::class, 'sendFcmNotification']);



// Route::get('/', function () {
//     return view('layout.blankpage');
// });

Route::group(['middleware' => 'useradmin'], function(){


    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    //Request Management        ================================================================================
    Route::resource('tables', DocumentRequestController::class);
    Route::resource('pending', PendingController::class);
    Route::resource('ongoing', OngoingController::class);
    Route::put('/pending/completeRequest/{id}', [PendingController::class, 'completeRequest'])->name('document-request.complete');
    Route::put('/ongoing/completeRequest/{id}', [OngoingController::class, 'completeRequest'])->name('document-request2.complete');
    Route::get('/walkin/form', [DocumentRequestController::class, 'showRequestForm'])->name('walkin.form');
    Route::post('/walkin/store', [DocumentRequestController::class, 'storeWalkIn'])->name('walkin.store');

    //Request Management       ================================================================================


    //Role Maintenance         ================================================================================
    Route::get('panel/role', [RoleController::class, 'list'])->name('role');
    Route::get('panel/role/add', [RoleController::class, 'add'])->name('role.add');
    Route::post('panel/role/add', [RoleController::class, 'insert'])->name('role.insert');
    Route::get('panel/role/edit/{id}', [RoleController::class, 'edit'])->name('role.edit');
    Route::post('panel/role/edit/{id}', [RoleController::class, 'update'])->name('role.update');
    Route::delete('panel/role/delete/{id}', [RoleController::class, 'delete'])->name('role.delete');
    //Role Maintenance          ================================================================================

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
    //DocType Maintenance       ================================================================================

    //Report Generation         ================================================================================
    Route::get('panel/report', [GenerateRequestController::class, 'display'])->name('generate');
    Route::get('panel/pdf', [GenerateRequestController::class, 'pdfGenerator'])->name('generatePDF');
    Route::get('panel/excel', [GenerateRequestController::class, 'exportExcel'])->name('generateExcel');
    Route::get('/generate-reports', [GenerateRequestController::class, 'handleReports'])->name('generateReports');
    //Report Generation         ================================================================================


});

Route::group(['middleware' => 'userstudent'], function(){
    Route::get('stpage', [StudentPageController::class, 'mainpage'])->name('st.page');
    // Display the document request form
    Route::get('/student-request', [StudentRequestController::class, 'create'])->name('studentrequest.create');
    Route::get('/view', [StudentRequestController::class, 'viewRequest'])->name('studentrequest.view');
    // Handle the form submission
    Route::post('/student-request', [StudentRequestController::class, 'store'])->name('studentrequest.store');
    Route::post('/save-fcm-token', [AccountController::class, 'saveFcmToken'])->name('save.fcm.token');


    Route::get('/send-notification', function(\Illuminate\Http\Request $request){
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


Route::get('hompage', function () {
    return view('common.homepage');
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



Route::get('/forgotpassword', [forgotpassword::class, 'index'])->name('forgot');
Route::post('/forgotpassword', [forgotpassword::class, 'forgotpost'])->name('forgot.submit');
Route::get('/verifyotp', [forgotpassword::class, 'showVerifyOTP'])->name('verifyotp');
Route::post('/verifyotp', [forgotpassword::class, 'verifyOTP'])->name('verifyotp.submit');
Route::get('/newpassword', [forgotpassword::class, 'showNewPassword'])->name('newpassword');
Route::post('/newpassword', [forgotpassword::class, 'newpassword'])->name('newpassword.submit');