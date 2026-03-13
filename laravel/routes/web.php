<?php

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\Collab\ClassGroupSyncController;
use App\Http\Controllers\Api\Collab\DmController;
use App\Http\Controllers\Api\Collab\GroupController;
use App\Http\Controllers\Api\Collab\UserLookupController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\Finance\ArrearsController;
use App\Http\Controllers\Api\Finance\DashboardController as FinanceDashboardController;
use App\Http\Controllers\Api\Finance\FeeStructureController;
use App\Http\Controllers\Api\Finance\FeeTypeController;
use App\Http\Controllers\Api\Finance\PaymentController;
use App\Http\Controllers\Api\Finance\PaymentLogController;
use App\Http\Controllers\Api\Finance\PaymentPlanController;
use App\Http\Controllers\Api\Finance\SpecialFeeController;
use App\Http\Controllers\Api\Finance\StudentFeeController;
use App\Http\Controllers\Api\LookupController;
use App\Http\Controllers\Api\ParentController;
use App\Http\Controllers\Api\Settings\ActivityTypeSettingsController;
use App\Http\Controllers\Api\Settings\GradeSettingsController;
use App\Http\Controllers\Api\Settings\SubjectSettingsController;
use App\Http\Controllers\Api\Settings\TermSettingsController;
use App\Http\Controllers\Api\Settings\UserSettingsController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\StudentPortalController;
use App\Http\Controllers\Api\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Academic Tracker Laravel backend']);
});

Route::prefix('api')->group(function (): void {
    Route::post('auth/login/', [AuthController::class, 'login']);
    Route::post('auth/password-reset/', [AuthController::class, 'passwordReset']);

    Route::middleware('auth')->group(function (): void {
        Route::post('auth/logout/', [AuthController::class, 'logout']);
        Route::get('auth/me/', [AuthController::class, 'me']);
        Route::post('auth/change-password/', [AuthController::class, 'changePassword']);

        Route::get('dashboard/staff/', [DashboardController::class, 'staff'])->middleware('profile.role:admin,teacher');
        Route::get('dashboard/student/', [DashboardController::class, 'student'])->middleware('profile.role:student');

        Route::middleware('profile.role:admin,teacher')->group(function (): void {
            Route::get('students/', [StudentController::class, 'index']);
            Route::get('students/{pk}/', [StudentController::class, 'show']);
            Route::post('students/{pk}/enroll/', [StudentController::class, 'enroll']);
            Route::post('students/{pk}/unenroll/', [StudentController::class, 'unenroll']);
            Route::post('students/{pk}/toggle-status/', [StudentController::class, 'toggleStatus']);
            Route::get('students/{student_id}/courses/{course_id}/history/', [StudentController::class, 'courseHistory']);
            Route::get('students/{pk}/activity-history/', [StudentController::class, 'allHistory']);

            Route::get('parents/', [ParentController::class, 'index']);
            Route::get('parents/{pk}/', [ParentController::class, 'show']);
            Route::post('parents/{pk}/link-students/', [ParentController::class, 'linkStudents']);
            Route::post('parents/{pk}/unlink-student/', [ParentController::class, 'unlinkStudent']);

            Route::get('classes/', [ClassController::class, 'index']);
            Route::post('classes/create/', [ClassController::class, 'store']);
            Route::get('classes/{pk}/', [ClassController::class, 'show']);
            Route::put('classes/{pk}/update/', [ClassController::class, 'update']);
            Route::post('classes/{pk}/toggle-status/', [ClassController::class, 'toggleStatus']);
            Route::post('classes/{pk}/enroll/', [ClassController::class, 'enrollStudents']);
            Route::post('classes/{pk}/unenroll/', [ClassController::class, 'unenrollStudent']);
            Route::post('classes/{pk}/copy-roster/', [ClassController::class, 'copyRoster']);
            Route::post('classes/{pk}/move-roster/', [ClassController::class, 'moveRoster']);

            Route::get('courses/{course_id}/activities/', [ActivityController::class, 'courseActivities']);
            Route::post('activities/create/', [ActivityController::class, 'store']);
            Route::get('activities/{pk}/', [ActivityController::class, 'show']);
            Route::post('activities/{pk}/log/', [ActivityController::class, 'log']);

            Route::post('collab/sync-class-groups/', [ClassGroupSyncController::class, 'syncAll']);
        });

        Route::middleware('profile.role:admin')->group(function (): void {
            Route::get('teachers/', [TeacherController::class, 'index']);
            Route::get('teachers/{pk}/', [TeacherController::class, 'show']);

            Route::get('settings/users/', [UserSettingsController::class, 'index']);
            Route::post('settings/users/create/', [UserSettingsController::class, 'store']);
            Route::put('settings/users/{pk}/update/', [UserSettingsController::class, 'update']);
            Route::post('settings/users/{pk}/toggle-status/', [UserSettingsController::class, 'toggleStatus']);

            Route::get('settings/grades/', [GradeSettingsController::class, 'index']);
            Route::post('settings/grades/create/', [GradeSettingsController::class, 'store']);
            Route::put('settings/grades/{pk}/update/', [GradeSettingsController::class, 'update']);
            Route::post('settings/grades/{pk}/toggle/', [GradeSettingsController::class, 'toggle']);

            Route::get('settings/subjects/', [SubjectSettingsController::class, 'index']);
            Route::post('settings/subjects/create/', [SubjectSettingsController::class, 'store']);
            Route::put('settings/subjects/{pk}/update/', [SubjectSettingsController::class, 'update']);
            Route::post('settings/subjects/{pk}/toggle/', [SubjectSettingsController::class, 'toggle']);

            Route::get('settings/terms/', [TermSettingsController::class, 'index']);
            Route::post('settings/terms/create/', [TermSettingsController::class, 'store']);
            Route::put('settings/terms/{pk}/update/', [TermSettingsController::class, 'update']);
            Route::post('settings/terms/{pk}/toggle/', [TermSettingsController::class, 'toggle']);

            Route::get('settings/activity-types/', [ActivityTypeSettingsController::class, 'index']);
            Route::post('settings/activity-types/create/', [ActivityTypeSettingsController::class, 'store']);
            Route::put('settings/activity-types/{pk}/update/', [ActivityTypeSettingsController::class, 'update']);
            Route::post('settings/activity-types/{pk}/toggle/', [ActivityTypeSettingsController::class, 'toggle']);
        });

        Route::middleware('profile.role:student')->group(function (): void {
            Route::get('student/courses/{course_id}/assignments/', [StudentPortalController::class, 'courseAssignments']);
            Route::get('student/assignments/', [StudentPortalController::class, 'allAssignments']);
        });

        Route::get('lookup/grades/', [LookupController::class, 'grades']);
        Route::get('lookup/subjects/', [LookupController::class, 'subjects']);
        Route::get('lookup/teachers/', [LookupController::class, 'teachers']);
        Route::get('lookup/students/', [LookupController::class, 'students']);
        Route::get('lookup/activity-types/', [LookupController::class, 'activityTypes']);
        Route::get('lookup/courses/', [LookupController::class, 'courses']);
        Route::get('lookup/roles/', [LookupController::class, 'roles']);
        Route::get('lookup/terms/', [LookupController::class, 'terms']);

        Route::get('finance/dashboard/', [FinanceDashboardController::class, 'show']);
        Route::get('finance/fee-types/', [FeeTypeController::class, 'index']);
        Route::post('finance/fee-types/create/', [FeeTypeController::class, 'store']);
        Route::put('finance/fee-types/{pk}/update/', [FeeTypeController::class, 'update']);
        Route::post('finance/fee-types/{pk}/toggle/', [FeeTypeController::class, 'toggle']);
        Route::get('finance/fee-structures/', [FeeStructureController::class, 'index']);
        Route::post('finance/fee-structures/create/', [FeeStructureController::class, 'store']);
        Route::put('finance/fee-structures/{pk}/update/', [FeeStructureController::class, 'update']);
        Route::post('finance/fee-structures/{pk}/toggle/', [FeeStructureController::class, 'toggle']);
        Route::get('finance/payments/', [PaymentController::class, 'index']);
        Route::post('finance/payments/create/', [PaymentController::class, 'store']);
        Route::get('finance/payments/{pk}/', [PaymentController::class, 'show']);
        Route::put('finance/payments/{pk}/update/', [PaymentController::class, 'update']);
        Route::delete('finance/payments/{pk}/delete/', [PaymentController::class, 'destroy']);
        Route::get('finance/payment-logs/', [PaymentLogController::class, 'index']);
        Route::get('finance/special-fees/', [SpecialFeeController::class, 'index']);
        Route::post('finance/special-fees/create/', [SpecialFeeController::class, 'store']);
        Route::post('finance/special-fees/{pk}/toggle/', [SpecialFeeController::class, 'toggle']);
        Route::get('finance/payment-plans/', [PaymentPlanController::class, 'index']);
        Route::post('finance/payment-plans/create/', [PaymentPlanController::class, 'store']);
        Route::get('finance/payment-plans/{pk}/', [PaymentPlanController::class, 'show']);
        Route::post('finance/installments/{pk}/toggle-paid/', [PaymentPlanController::class, 'togglePaid']);
        Route::get('finance/student-fees/', [StudentFeeController::class, 'index']);
        Route::get('finance/student-fees/{pk}/', [StudentFeeController::class, 'show']);
        Route::get('finance/arrears/', [ArrearsController::class, 'index']);

        Route::get('collab/groups/', [GroupController::class, 'index']);
        Route::post('collab/groups/create/', [GroupController::class, 'store']);
        Route::get('collab/groups/{pk}/', [GroupController::class, 'show']);
        Route::get('collab/groups/{pk}/messages/', [GroupController::class, 'messages']);
        Route::post('collab/groups/{pk}/send/', [GroupController::class, 'sendMessage']);
        Route::post('collab/groups/{pk}/add-members/', [GroupController::class, 'addMembers']);
        Route::post('collab/groups/{pk}/remove-member/', [GroupController::class, 'removeMember']);
        Route::post('collab/dm/', [DmController::class, 'store']);
        Route::get('collab/users/', [UserLookupController::class, 'index']);
    });
});
