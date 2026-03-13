<?php

namespace App\Support\Transformers;

use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\ActivityType;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\CourseTeacher;
use App\Models\Grade;
use App\Models\Profile;
use App\Models\Relationship;
use App\Models\Role;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;

class CoreTransformer
{
    private static ?array $rolePayloadsByName = null;

    public static function role(Role $role): array
    {
        return $role->toArray();
    }

    public static function user(User $user): array
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'is_active' => (bool) $user->is_active,
        ];
    }

    public static function profile(Profile $profile): array
    {
        $profile->loadMissing('user');
        $role = self::rolePayload($profile->type);

        return [
            'id' => $profile->id,
            'type' => $profile->type,
            'first_name' => $profile->first_name,
            'last_name' => $profile->last_name,
            'dob' => $profile->dob,
            'sex' => $profile->sex,
            'phone_number' => $profile->phone_number,
            'is_active' => (bool) $profile->is_active,
            'id_number' => $profile->id_number,
            'user_id' => $profile->user_id,
            'created_at' => optional($profile->created_at)?->toISOString(),
            'updated_at' => optional($profile->updated_at)?->toISOString(),
            'full_name' => $profile->full_name,
            'user' => $profile->user ? self::user($profile->user) : null,
            'role' => $role,
        ];
    }

    public static function grade(Grade $grade): array
    {
        return self::timestamps($grade, [
            'id' => $grade->id,
            'name' => $grade->name,
            'is_active' => (bool) $grade->is_active,
        ]);
    }

    public static function subject(Subject $subject): array
    {
        return self::timestamps($subject, [
            'id' => $subject->id,
            'name' => $subject->name,
            'is_active' => (bool) $subject->is_active,
        ]);
    }

    public static function term(Term $term): array
    {
        return self::timestamps($term, [
            'id' => $term->id,
            'name' => $term->name,
            'start' => $term->start,
            'end' => $term->end,
            'is_active' => (bool) $term->is_active,
        ]);
    }

    public static function course(Course $course): array
    {
        $course->loadMissing(['grade', 'subject', 'teacherAssignments.teacher']);
        $assignment = $course->teacherAssignments->firstWhere('is_active', true);

        return self::timestamps($course, [
            'id' => $course->id,
            'name' => $course->name,
            'code' => $course->code,
            'description' => $course->description,
            'grade_id' => $course->grade_id,
            'subject_id' => $course->subject_id,
            'is_active' => (bool) $course->is_active,
            'grade_name' => $course->grade?->name,
            'subject_name' => $course->subject?->name,
            'teacher' => $assignment?->teacher ? self::profile($assignment->teacher) : null,
            'student_count' => $course->studentEnrollments()->where('is_active', true)->count(),
        ]);
    }

    public static function courseTeacher(CourseTeacher $assignment): array
    {
        $assignment->loadMissing(['course', 'teacher']);

        return self::timestamps($assignment, [
            'id' => $assignment->id,
            'course_id' => $assignment->course_id,
            'teacher_id' => $assignment->teacher_id,
            'is_active' => (bool) $assignment->is_active,
            'teacher_name' => $assignment->teacher?->full_name,
            'course_name' => $assignment->course?->name,
        ]);
    }

    public static function courseStudent(CourseStudent $enrollment): array
    {
        $enrollment->loadMissing('student');

        return self::timestamps($enrollment, [
            'id' => $enrollment->id,
            'course_id' => $enrollment->course_id,
            'student_id' => $enrollment->student_id,
            'is_active' => (bool) $enrollment->is_active,
            'student_name' => $enrollment->student?->full_name,
            'student_data' => $enrollment->student ? self::profile($enrollment->student) : null,
        ]);
    }

    public static function activityType(ActivityType $activityType): array
    {
        return self::timestamps($activityType, [
            'id' => $activityType->id,
            'name' => $activityType->name,
            'description' => $activityType->description,
            'type' => $activityType->type,
            'is_active' => (bool) $activityType->is_active,
            'image' => $activityType->image,
            'true_value' => $activityType->true_value,
            'false_value' => $activityType->false_value,
        ]);
    }

    public static function activity(Activity $activity): array
    {
        $activity->loadMissing(['activityType', 'teacher', 'course', 'term']);

        return self::timestamps($activity, [
            'id' => $activity->id,
            'name' => $activity->name,
            'teacher_id' => $activity->teacher_id,
            'activity_type_id' => $activity->activity_type_id,
            'course_id' => $activity->course_id,
            'term_id' => $activity->term_id,
            'note' => $activity->note,
            'total' => $activity->total,
            'due_date' => $activity->due_date,
            'file' => $activity->file,
            'is_active' => (bool) $activity->is_active,
            'activity_type_data' => $activity->activityType ? self::activityType($activity->activityType) : null,
            'teacher_name' => $activity->teacher?->full_name,
            'course_name' => $activity->course?->name,
            'term_name' => $activity->term?->name,
        ]);
    }

    public static function activityLog(?ActivityLog $log): ?array
    {
        if (! $log) {
            return null;
        }

        $log->loadMissing(['student', 'activity']);

        return self::timestamps($log, [
            'id' => $log->id,
            'student_id' => $log->student_id,
            'activity_id' => $log->activity_id,
            'score' => $log->score,
            'status' => $log->status,
            'file' => $log->file,
            'is_active' => (bool) $log->is_active,
            'student_name' => $log->student?->full_name,
            'activity_data' => $log->activity ? self::activity($log->activity) : null,
        ]);
    }

    public static function relationship(Relationship $relationship): array
    {
        $relationship->loadMissing(['student', 'parent']);

        return self::timestamps($relationship, [
            'id' => $relationship->id,
            'student_id' => $relationship->student_id,
            'parent_id' => $relationship->parent_id,
            'is_active' => (bool) $relationship->is_active,
            'student_data' => $relationship->student ? self::profile($relationship->student) : null,
            'parent_data' => $relationship->parent ? self::profile($relationship->parent) : null,
        ]);
    }

    private static function timestamps(object $model, array $payload): array
    {
        $payload['created_at'] = optional($model->created_at ?? null)?->toISOString();
        $payload['updated_at'] = optional($model->updated_at ?? null)?->toISOString();

        return $payload;
    }

    private static function rolePayload(?string $roleName): ?array
    {
        if (! $roleName) {
            return null;
        }

        if (self::$rolePayloadsByName === null) {
            self::$rolePayloadsByName = Role::query()
                ->orderBy('name')
                ->get()
                ->mapWithKeys(fn (Role $role) => [$role->name => self::role($role)])
                ->all();
        }

        return self::$rolePayloadsByName[$roleName] ?? null;
    }
}