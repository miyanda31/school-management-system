<?php

use App\Http\Controllers\AllocationController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AwardsController;
use App\Http\Controllers\BursariesController;
use App\Http\Controllers\BursaryController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ChartsController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\GenerateController;
use App\Http\Controllers\GradeBookController;
use App\Http\Controllers\GradingController;
use App\Http\Controllers\MarkSheetsController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromoteStudentsController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ReportAnalysisController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SchoolCalendarController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentGradesController;
use App\Http\Controllers\SubjectsController;
use App\Http\Controllers\TimetableController;
use Illuminate\Support\Facades\Auth;



Auth::routes();


Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('timetable', TimetableController::class);
Route::resource('examinations', ExaminationController::class);
Route::resource('allocations', AllocationController::class);
Route::resource('periods', PeriodController::class);
Route::resource('attendance', AttendanceController::class);
Route::resource('period-register', RegisterController::class);
Route::resource('parents', ParentController::class);
Route::resource('students', StudentController::class);
Route::resource('department', DepartmentController::class);
Route::resource('departments', DepartmentsController::class);
Route::resource('staff', StaffController::class);
Route::resource('profile', ProfileController::class);
Route::resource('events', EventsController::class);
Route::resource('packages', PackagesController::class);
Route::resource('fees', GenerateController::class);
Route::resource('payments', PaymentsController::class);
Route::resource('sponsorships', BursariesController::class);
Route::resource('sponsor', BursaryController::class);
Route::resource('gradebook', GradeBookController::class);
Route::resource('mark-sheets', MarkSheetsController::class);
Route::resource('reports', ReportsController::class);
Route::resource('analysis', ReportAnalysisController::class);
Route::resource('awards', AwardsController::class);
Route::resource('settings', SettingsController::class);
Route::resource('subjects', SubjectsController::class);
Route::resource('classes', ClassesController::class);
Route::resource('grading', GradingController::class);
Route::resource('calendar', SchoolCalendarController::class);
Route::resource('codes', CodeController::class);
Route::resource('promote', PromoteStudentsController::class);
Route::resource('categories', CategoriesController::class);
Route::resource('charts', ChartsController::class);
Route::resource('performance', PerformanceController::class);
Route::resource('grade-book', StudentGradesController::class);

//Route::get('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

