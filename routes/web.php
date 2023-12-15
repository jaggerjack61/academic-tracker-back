<?php

use App\Http\Controllers\ClassController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});



Route::middleware('auth')->group(function () {

    Route::controller(MainController::class)->group(function () {
        Route::get('/', 'showDashboard')->name('show-dashboard');
    });

    Route::middleware('role:superuser')->group(function () {

        Route::controller(StudentController::class)->group(function () {
            Route::prefix('students')->group(function () {
                Route::get('/', 'showStudents')->name('show-students');
                Route::get('view', 'view')->name('view-student');
                Route::get('activities', 'viewActivities')->name('view-activities');
            });
        });

        Route::controller(ClassController::class)->group(function () {
            Route::prefix('classes')->group(function () {
                Route::get('/', 'showClasses')->name('show-classes');
            });
        });

        Route::controller(TeacherController::class)->group(function () {
            Route::prefix('teachers')->group(function () {
                Route::get('/', 'showTeachers')->name('show-teachers');
                Route::get('view', 'view')->name('view-teacher');
            });
        });

        Route::controller(SettingsController::class)->group(function () {
            Route::prefix('settings')->group(function () {
                Route::get('terms', 'showTerms')->name('show-terms');
            });
        });

        Route::get('/home', [HomeController::class, 'index'])->name('home');
    });

});

Auth::routes(
    [
        'login'    => true,
        'logout'   => true,
        'register' => true,
        'reset'    => true,   // for resetting passwords
        'confirm'  => false,  // for additional password confirmations
        'verify'   => false,  // for email verification
    ]
);


