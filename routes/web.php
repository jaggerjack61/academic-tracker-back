<?php

use App\Http\Controllers\ActivityTypeController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\UserController;
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
Route::get('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout-route');


Route::middleware('auth')->group(function () {
    Route::middleware('role:teacher|admin')->group(function () {

        Route::controller(MainController::class)->group(function () {
            Route::get('/', 'showDashboard')->name('show-dashboard');
        });

        Route::controller(ChangePasswordController::class)->group(function () {
           Route::prefix('change-password')->group(function () {
               Route::get('/', 'show')->name('show-change-password');
               Route::post('/', 'change')->name('change-password');
           });
        });

        Route::controller(ClassController::class)->group(function () {
            Route::prefix('classes')->group(function () {
                Route::get('/', 'showClasses')->name('show-classes');
                Route::get('view/{course}', 'view')->name('view-class');
                Route::get('view/activity/{activity}', 'viewActivity')->name('view-class-activity');
                Route::post('/', 'create')->name('create-class');
                Route::patch('/', 'edit')->name('edit-class');
                Route::get('status/{course}', 'toggle')->name('toggle-class-status');
                Route::post('/enroll-class', 'enroll')->name('enroll-class');
                Route::post('/copy-class', 'copy')->name('copy-class');
                Route::post('/move-class', 'move')->name('move-class');
                Route::post('activities/add', 'addActivity')->name('add-class-activity');
                Route::post('activities/log/add', 'addActivityLog')->name('add-class-activity-log');
                Route::get('activities/{course}', 'viewActivities')->name('view-class-activities');

            });
        });

        Route::controller(StudentController::class)->group(function () {
            Route::prefix('students')->group(function () {
                Route::get('/', 'showStudents')->name('show-students');
                Route::get('view/{student}', 'view')->name('view-student');
                Route::post('enroll', 'enroll')->name('enroll-student');
                Route::get('unroll/{student}/{class}', 'unenroll')->name('unenroll-student');
                Route::get('activities/{student}/{course}', 'viewActivities')->name('view-student-activities');
            });
        });

        Route::controller(ParentController::class)->group(function () {
            Route::prefix('parents')->group(function () {
                Route::get('/', 'show')->name('show-parents');
                Route::get('view/{parent}', 'view')->name('view-parent');
                Route::post('view/add/relationship', 'addRelationship')->name('add-relationship');
                Route::get('view/remove/{relationship}', 'removeRelationship')->name('remove-relationship');
            });
        });
    });



    Route::middleware('role:admin')->group(function () {





        Route::controller(TeacherController::class)->group(function () {
            Route::prefix('teachers')->group(function () {
                Route::get('/', 'show')->name('show-teachers');
                Route::get('view/{teacher}', 'view')->name('view-teacher');
            });
        });

        Route::group(['prefix' => 'settings'], function () {
            Route::controller(TermController::class)->group(function () {
                Route::group(['prefix' => 'terms'], function () {
                    Route::get('/', 'index')->name('show-terms');
                    Route::post('/', 'create')->name('create-term');
                    Route::patch('/', 'edit')->name('edit-term');
                    Route::get('status/{id}', 'toggle')->name('toggle-term-status');
                });
            });

            Route::controller(ActivityTypeController::class)->group(function () {
                Route::group(['prefix' => 'activity-types'], function () {
                    Route::get('/', 'index')->name('show-activity-types');
                    Route::post('/', 'create')->name('create-activity-type');
                    Route::patch('/', 'edit')->name('edit-activity-type');
                    Route::get('status/{id}', 'toggle')->name('toggle-activity-type-status');
                });
            });

            Route::controller(GradeController::class)->group(function () {
                Route::group(['prefix' => 'grades'], function () {
                    Route::get('/', 'index')->name('show-grades');
                    Route::post('/', 'create')->name('create-grade');
                    Route::patch('/', 'edit')->name('edit-grade');
                    Route::get('status/{id}', 'toggle')->name('toggle-grade-status');
                });
            });

            Route::controller(SubjectController::class)->group(function () {
                Route::group(['prefix' => 'subjects'], function () {
                    Route::get('/', 'index')->name('show-subjects');
                    Route::post('/', 'create')->name('create-subject');
                    Route::patch('/', 'edit')->name('edit-subject');
                    Route::get('status/{id}', 'toggle')->name('toggle-subject-status');
                });
            });

            Route::controller(UserController::class)->group(function () {
                Route::group(['prefix' => 'users'], function () {
                    Route::get('/', 'index')->name('show-users');
                    Route::post('/', 'create')->name('create-user');
                    Route::patch('/', 'edit')->name('edit-user');
                    Route::get('status/{id}', 'toggle')->name('toggle-user-status');
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
        'register' => false,
        'reset'    => true,   // for resetting passwords
        'confirm'  => false,  // for additional password confirmations
        'verify'   => false,  // for email verification
    ]
);


