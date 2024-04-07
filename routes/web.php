<?php

use App\Http\Controllers\ClassController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TermController;
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

        Route::controller(TermController::class)->group(function () {
            Route::prefix('settings')->group(function () {
                Route::prefix('terms')->group(function () {
                    Route::get('/', 'index')->name('show-terms');
                    Route::post('/', 'create')->name('create-term');
                    Route::get('status/{id}', 'toggle')->name('toggle-term-status');
                });
            });
        });

        Route::controller(GradeController::class)->group(function () {
            Route::prefix('settings')->group(function () {
                Route::prefix('grades')->group(function () {
                    Route::get('/', 'index')->name('show-grades');
                    Route::post('/', 'create')->name('create-grade');
                    Route::get('status/{id}', 'toggle')->name('toggle-grade-status');
                });
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


