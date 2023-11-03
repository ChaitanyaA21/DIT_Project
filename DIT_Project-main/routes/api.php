<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SemesterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AcademicCalendarController;
use App\Http\Controllers\SubjectsController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\NoticeBoardController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\StudentController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('getNotice',[NoticeBoardController::class,'getNotices'])->name('admin.notice')->withoutMiddleware('auth:admin-api');


Route::group([

    'middleware' => 'auth:faculty-api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('facultyLogin', [FacultyController::class, 'login'])->name('faculty.login')
    ->withoutMiddleware('auth:faculty-api');
    Route::post('facultyLogout', [FacultyController::class,'logout']);
    Route::get('me', [FacultyController::class,'me']);
    Route::get('getFacultySubjects',[SubjectsController::class,'facultySubjects'])->withoutMiddleware('auth:admin-api');
    Route::get('getEnrolledStudents',[FacultyController::class,'enrolledStudents']);
    Route::get('getStudentDetails',[FacultyController::class,'getStudentDetails']);
    Route::post('markAttendance',[FacultyController::class,'markAttendance']);
    Route::post('addInternalMarks',[FacultyController::class,'addInternalMarks']);
    Route::put('facultyMarkAsRead',[FacultyController::class,'markAsRead']);
    Route::get('getFacultyNotifications',[FacultyController::class,'getNotifications']);

    Route::post('raiseComplaintFaculty',[FacultyController::class,'raiseComplaint']);

    Route::put('updatePwdFaculty',[FacultyController::class,'updatePassword']);
    Route::put('setPwdFaculty',[FacultyController::class,'setPassword'])->withoutMiddleware('auth:faculty-api');
    Route::put('updateContactFaculty',[FacultyController::class,'updateContact']);

    Route::post('sendOTPFaculty',[FacultyController::class,"sendOtp"])->withoutMiddleware('auth:faculty-api');
    Route::post('otpVerifyFaculty',[FacultyController::class,"otpVerification"])->withoutMiddleware('auth:faculty-api');
    Route::post('sendNotificationsFaculty',[FacultyController::class,'sendNotifications']);

    Route::get('/getCalendarFaculty',[FacultyController::class,'getCalendar']);


    // Route::get('getNotice',[NoticeBoardController::class,'get'])->name('faculty.notice');


});

Route::group([
    'middleware' => 'auth:admin-api',
    'prefix' => 'auth'
], function ($router){
    Route::post('adminEntry',[AdminController::class,'adminEntry'])->withoutMiddleware('auth:admin-api');
    Route::post('adminLogin',[AdminController::class,'login'])->name('admin.login')->withoutMiddleware('auth:admin-api');
    Route::post('adminLogout',[AdminController::class,'logout']);
    Route::put('updatePwdAdmin',[AdminController::class,'updatePassword']);
    Route::get('adminMe',[AdminController::class,'me']);

    Route::post('addSubjects',[SubjectsController::class,'add'])->withoutMiddleware('auth:faculty-api');
    Route::get('/getSubjects',[SubjectsController::class,'get'])->withoutMiddleware('auth:faculty-api');

    Route::post('/addSemester',[SemesterController::class,'add']);
    Route::get('/getSemester',[SemesterController::class,'get']);
    Route::put('/updateSemester',[SemesterController::class,'update']);
    Route::delete('/removeSemester',[SemesterController::class,'delete']);

    Route::post("/addReRegister",[SemesterController::class,'addReRegister']);
    Route::get("/getReRegister",[SemesterController::class,'getReRegister']);
    Route::delete("/deleteReRegister",[SemesterController::class,'deleteReRegister']);

    Route::post('/assignFaculty',[SubjectsController::class,'assignFaculty'])->withoutMiddleware('auth:faculty-api');
    Route::get('/getAssignedFaculty',[SubjectsController::class,'getAssignedFaculty'])->withoutMiddleware('auth:faculty-api');
    Route::delete('/deleteAssignment',[SubjectsController::class,'deleteAssignment'])->withoutMiddleware('auth:faculty-api');
    Route::put('/updateAssignment',[SubjectsController::class,'updateAssignment'])->withoutMiddleware('auth:faculty-api');

    Route::post("/addStdLogin",[AuthenticationController::class,'addStdLogin']);
    Route::put("/updateStdLogin",[AuthenticationController::class,'updateStdLogin']);
    Route::delete("/deleteStdLogin",[AuthenticationController::class,'deleteStdLogin']);

    Route::post("/addFacLogin",[AuthenticationController::class,'addFacLogin']);
    Route::put("/updateFacLogin",[AuthenticationController::class,'updateFacLogin']);
    Route::delete("/deleteFacLogin",[AuthenticationController::class,'deleteFacLogin']);

    Route::post('/addCalendar',[AcademicCalendarController::class,'add']);
    Route::get('/getCalendar',[AcademicCalendarController::class,'get']);
    Route::put('/updateCalendar',[AcademicCalendarController::class,'update']);

    Route::post('/addNotice',[NoticeBoardController::class,'add']);
    Route::put('/updateNotice',[NoticeBoardController::class,'update']);
    Route::delete('/deleteNotice',[NoticeBoardController::class,'delete']);

    Route::get('getComplaints',[AdminController::class,'getComplaints']);
    Route::delete('deleteComplaint',[AdminController::class,'deleteComplaint']);

    Route::post("facultyRegistration",[AdminController::class,'facultyReg']);
    Route::get("facultyDetails",[AdminController::class,'getFaculty']);

    Route::post("studentRegistration",[AdminController::class,'studentReg']);
    Route::get("studentsDetails",[AdminController::class,'getStudents']);
    Route::post("uploadSoftCopies",[AdminController::class,'uploadAndSaveFiles']);

    Route::post("addForms",[AdminController::class,'addForms']);
    Route::get('viewForms',[AdminController::class,'viewForms']);
    Route::post('updateForm',[AdminController::class,'updateForm']);
    Route::delete('deleteForm',[AdminController::class,'deleteForm']);


});


// Route::post("/addAdminLogin",[AuthenticationController::class,'addAdminLogin']);
// Route::put("/updateAdminLogin",[AuthenticationController::class,'updateAdminLogin']);
// Route::delete("/deleteAdminLogin",[AuthenticationController::class,'deleteAdminLogin']);

Route::group([

    'middleware' => 'auth:student-api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('studentLogin', [StudentController::class, 'login'])->name('student.login')
    ->withoutMiddleware('auth:student-api');
    Route::post('studentLogout', [StudentController::class,'logout']);
    Route::get('studentMe', [StudentController::class,'me']);
    Route::get('studentsEnrolled', [StudentController::class,'enrolledStds']);
    Route::get('getStudentNotifications',[StudentController::class,'getNotifications']);
    Route::put('studentMarkAsRead',[StudentController::class,'markAsRead']);

    Route::post('raiseComplaintStudent',[StudentController::class,'raiseComplaint']);

    Route::put('SetPwdStd',[StudentController::class,'setPwd'])->withoutMiddleware('auth:student-api');
    Route::put('updateContactStd',[StudentController::class,'updateContact']);
    Route::put('updatePwdStd',[StudentController::class,'updatePwd']);


    Route::post('sendOTPStd',[StudentController::class,"sendOtp"])->withoutMiddleware('auth:student-api');
    Route::post('otpVerifyStd',[StudentController::class,"otpVerification"])->withoutMiddleware('auth:student-api');
    Route::get('getInternalMarks',[StudentController::class,'checkMarks']);
    Route::get('getAttendance',[StudentController::class,'checkAttendance']);
    Route::get('getAttendanceDayWise',[StudentController::class,'checkAttendanceDayWise']);

    Route::get('getForms',[StudentController::class,'getAvailableForms']);


    Route::get('/getAcademicCalendarStudent',[StudentController::class,'getCalendar']);
    Route::get('/enrolledSubjects',[StudentController::class,"enrolledSubjects"]);
    Route::get('/getSoftCopies',[StudentController::class,"getSoftCopiesUrls"]);
    Route::get('/getRequiredFeedbackSubjects',[StudentController::class,"requiredFeedbackSubjects"]);
    Route::get('/getQuestions',[StudentController::class,"getFeedbackQuestions"]);
    Route::post('/submitFeedback',[StudentController::class,"submitFeedback"]);

    // routes/api.php

Route::get('/checkFeedback',[StudentController::class,'checkFeedbackRequired']);

});
