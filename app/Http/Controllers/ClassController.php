<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\ActivityType;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\CourseTeacher;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Term;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClassController extends Controller
{
    public $studentController;

    public function __construct()
    {
        $this->studentController = new StudentController();
    }

    //
    public function showClasses(Request $request)
    {
        $grades = Grade::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $teachers = Teacher::where('is_active', true)->get();
        $classes = Course::paginate(30);
//        $url = $request->path();
        return view('pages.classes.index', compact('grades', 'subjects', 'teachers', 'classes'));
    }

    public function create(Request $request)
    {
        // Validate the request data


        try {
            $validatedData = $request->validate([
                'name' => 'required|string',
                'grade_id' => 'required|integer',
                'subject_id' => 'required|integer',
                'teacher_id' => 'nullable|integer',
                'description' => 'nullable|string',
            ]);
            $course = Course::create($validatedData);
            $course->save();
            if ($validatedData['teacher_id'] != null) {
                $courseTeacher = CourseTeacher::where('course_id', $course->id)
                    ->where('is_active', true)
                    ->first();
                if ($courseTeacher == null) {
                    $courseTeacher = new CourseTeacher();
                    $courseTeacher->course_id = $course->id;
                    $courseTeacher->teacher_id = $validatedData['teacher_id'];
                    $courseTeacher->is_active = true;
                    $courseTeacher->save();
                } else {
                    return back()->with('error', 'Class has already been assigned.');
                }
            }

            return back()->with('success', 'Class has been created');
        } catch (\Exception $e) {
            // Log the error message for debugging
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to create class');
        }
    }

    public function edit(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'required|integer',
                'name' => 'required|string',
                'grade_id' => 'required|integer',
                'subject_id' => 'required|integer',
                'teacher_id' => 'nullable|integer',
                'description' => 'nullable|string',
            ]);
            $course = Course::find($validatedData['id']);
            $course->update($validatedData);
            $courseTeacher = CourseTeacher::where('course_id', $course->id)->where('is_active', true)->first();
            if ($courseTeacher == null && $validatedData['teacher_id'] != null) {
                $courseTeacher = new CourseTeacher();
                $courseTeacher->course_id = $course->id;
                $courseTeacher->teacher_id = $validatedData['teacher_id'];
                $courseTeacher->is_active = true;
                $courseTeacher->save();
            } else if ($courseTeacher != null && $validatedData['teacher_id'] != null) {
                if ($courseTeacher->teacher_id != $validatedData['teacher_id']) {
                    $courseTeacher->is_active = false;
                    $courseTeacher->save();
                    $courseTeacher = new CourseTeacher();
                    $courseTeacher->course_id = $course->id;
                    $courseTeacher->teacher_id = $validatedData['teacher_id'];
                    $courseTeacher->is_active = true;
                    $courseTeacher->save();
                }
            }
            return back()->with('success', 'Class has been updated');
        } catch (\Exception $e) {
            // Log the error message for debugging
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to update class');
        }
    }

    public function toggle(Course $course)
    {
        $course->is_active = !$course->is_active;
        $course->save();
        return back()->with('success', 'Class status has been updated');
    }

    public function view(Course $course)
    {
        return view('pages.classes.view', [
            'class' => $course,
            'students' => Student::where('is_active', true)->get(),
            'teachers' => Teacher::where('is_active', true)->get(),
            'classes' => Course::where('is_active', true)->get()
        ]);
    }

    public function enroll(Request $request)
    {
//        dd($request);
        $class = Course::find($request->course_id);
        foreach ($request->students as $student) {
            $enrolled = $this->studentController->validateEnrollment($student, $class->id);
            if ($enrolled) {
                if (!$enrolled->is_active) {
                    $enrolled->is_active = true;
                    $enrolled->save();
                }
            } else {
                $studentInstance = new CourseStudent();
                $studentInstance->student_id = $student;
                $studentInstance->course_id = $class->id;
                $studentInstance->save();
            }
        }
        return back()->with('success', 'Students have been enrolled');
    }

    public function copy(Request $request)
    {
        $currentEnrolledClass = Course::find($request->currentCourse);
        try {

            $this->iterateEnrolment($request, $currentEnrolledClass);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }


        return back()->with('success', 'Students have been enrolled');
    }

    public function move(Request $request)
    {
        $currentEnrolledClass = Course::find($request->currentCourse);
        try {

            $this->iterateEnrolment($request, $currentEnrolledClass);
            $currentEnrolledClass->students()->update(['is_active' => false]);
//            $currentEnrolledClass->save();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }


        return back()->with('success', 'Students have been enrolled');
    }

    /**
     * @param Request $request
     * @param $currentEnrolledClass
     * @return void
     */
    public function iterateEnrolment(Request $request, $currentEnrolledClass): void
    {
        foreach ($request->courses as $course) {
            $class = Course::find($course);
            foreach ($currentEnrolledClass->students as $student) {
                $enrolled = $this->studentController->validateEnrollment($student->student_id, $class->id);
                if ($enrolled) {
                    if (!$enrolled->is_active) {
                        $enrolled->is_active = true;
                        $enrolled->save();
                    }
                } else {
                    $courseStudentInstance = new CourseStudent();
                    $courseStudentInstance->student_id = $student->student_id;
                    $courseStudentInstance->course_id = $class->id;
                    $courseStudentInstance->save();
                }
            }
        }
    }

    public function viewActivities(Course $course)
    {
        return view('pages.classes.activities', [
            'class' => $course,
            'activityTypes' => ActivityType::where('is_active', true)->get(),
            'first' => ActivityType::where('is_active', true)->first()->id,
            'activities' => Activity::where('course_id', $course->id)->get()

        ]);
    }

    public function addActivity(Request $request)
    {

        try {
            $term = $this->getTermForToday();
            if (!$term) {
                return back()->with('error', 'There is currently no active term for your activity. Please go add a new term in your settings.');
            }

            $file = $request->file('file');
            if ($file) {
                $destinationPath = 'class/files';
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move($destinationPath, $fileName);
                $activity = new Activity();
                $activity->activity_type_id = $request->activity_type_id;
                $activity->teacher_id = $request->teacher_id;
                $activity->course_id = $request->course_id;
                $activity->name = $request->name;
                $activity->note = $request->note;
                $activity->total = $request->total;
                $activity->due_date = $request->due_date;
                $activity->term_id = $term->id;
                $activity->file = $destinationPath . '/' . $fileName;
                $activity->save();

            } else {

                $activity = new Activity();
                $activity->activity_type_id = $request->activity_type_id;
                $activity->teacher_id = $request->teacher_id;
                $activity->course_id = $request->course_id;
                $activity->name = $request->name;
                $activity->note = $request->note;
                $activity->total = $request->total;
                $activity->due_date = $request->due_date;
                $activity->term_id = $term->id;
                $activity->save();
            }

            // Initialize activity logs for enrolled students
            $enrolledStudents = CourseStudent::where('course_id', $request->course_id)->where('is_active', true)->get();
            foreach ($enrolledStudents as $student) {
                $activityLog = new ActivityLog();
                $activityLog->student_id = $student->student_id;
                $activityLog->activity_id = $activity->id;
                $activityLog->save();
            }

            return back()->with('success', 'Activity has been created and activity logs initialized for enrolled students');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    function getTermForToday() {
        $today = Carbon::today()->toDateString();

        return Term::where('start', '<=', $today)
            ->where('end', '>=', $today)
            ->where('is_active', true)
            ->first();
    }

    public function viewActivity(Activity $activity)
    {
        return view('pages.classes.view-activity', compact('activity'));
    }

    public function addActivityLog(Request $request)
    {
        try {
            $i = 0;
            $marks = $request->student;
            $activity = Activity::find($request->activity_id);
            $studentsfromclass = $activity->course->students;

            if ($activity->type->type == 'value') {
                foreach ($studentsfromclass as $student) {
                    $activityLog = ActivityLog::where('activity_id', $request->activity_id)->where('student_id', $student->student_id)->first();

                    if ($marks[$i] > $activity->total) {
                        return back()->with('error', 'Students cannot score more than total marks. Please check again. ');
                    }

                    if ($activityLog) {
                        if ($marks[$i]) {
                            $activityLog->score = $marks[$i];
                        }
                        $activityLog->save();
                    } else {
                        $activityLog = new ActivityLog();
                        $activityLog->activity_id = $request->activity_id;
                        $activityLog->student_id = $student->student_id;
                        if ($marks[$i]) {
                            $activityLog->score = $marks[$i];
                        }
                        $activityLog->save();
                    }

                    $i++;
                }
                return back()->with('success', 'Activity has been logged');
            } elseif ($activity->type->type == 'boolean') {
                $studentIds = null;
                if ($marks) {
                    $studentIds = array_keys($marks);

                    foreach ($studentIds as $id) {
                        $activityLog = ActivityLog::where('activity_id', $request->activity_id)->where('student_id', $id)->first();
                        if ($activityLog) {
                            $activityLog->score = $marks[$id] ? 2 : 1;
                            $activityLog->save();
                        } else {
                            $activityLog = new ActivityLog();
                            $activityLog->activity_id = $request->activity_id;
                            $activityLog->student_id = $id;
                            $activityLog->score = $marks[$id] ? 2 : 1;
                            $activityLog->save();
                        }
                    }

                    // Mark remaining students as false
                    $remainingStudents = array_diff(
                        array_column($studentsfromclass->toArray(), 'student_id'),
                        $studentIds
                    );

                    foreach ($remainingStudents as $studentId) {
                        $activityLog = ActivityLog::where('activity_id', $request->activity_id)
                            ->where('student_id', $studentId)
                            ->first();

                        if ($activityLog) {
                            $activityLog->score = 1; // Mark as false
                            $activityLog->save();
                        } else {
                            $activityLog = new ActivityLog();
                            $activityLog->activity_id = $request->activity_id;
                            $activityLog->student_id = $studentId;
                            $activityLog->score = 1; // Mark as false
                            $activityLog->save();
                        }
                    }
                } else {
                    // Mark all students as false
                    foreach ($studentsfromclass as $student) {
                        $activityLog = ActivityLog::where('activity_id', $request->activity_id)
                            ->where('student_id', $student->student_id)
                            ->first();

                        if ($activityLog) {
                            $activityLog->score = 1; // Mark as false
                            $activityLog->save();
                        } else {
                            $activityLog = new ActivityLog();
                            $activityLog->activity_id = $request->activity_id;
                            $activityLog->student_id = $student->student_id;
                            $activityLog->score = 1; // Mark as false
                            $activityLog->save();
                        }
                    }
                }

                return back()->with('success', 'Activity has been logged');
            } else {
                return back()->with('error', 'Activity has not been logged');
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function viewStudentActivities(Student $student,Course $course)
    {
        $logs = ActivityLog::where('student_id', $student->id)
            ->whereHas('activity', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->get();
        $activityTypes = ActivityType::where('is_active', true)->get();
        $first = ActivityType::where('is_active', true)->first()->id;
        return view('pages.students.activities', compact( 'student', 'course', 'activityTypes','first'));
    }


}

