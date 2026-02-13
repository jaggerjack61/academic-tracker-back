<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function showDashboard()
    {
        $prev_week = ActivityLog::whereHas('activity', function ($query) {
            $query->whereHas('type', function ($query) {
                $query->where('id', 2);
            });
        })
            ->where('score', 1)
            ->where('created_at', '<=', now()->subDays(5))
            ->where('created_at', '>=', now()->subDays(12))
            ->get()->count();
        $this_week = ActivityLog::whereHas('activity', function ($query) {
            $query->whereHas('type', function ($query) {
                $query->where('id', 2);
            });
        })
            ->where('score', 1)
            ->where('created_at', '>=', now()->subDays(5))
            ->get()->count();

        $absences = ActivityLog::select('created_at', DB::raw('count(*) as absences'))
            ->whereHas('activity', function ($query) {
                $query->whereHas('type', function ($query) {
                    $query->where('id', 2);
                });
            })
            ->where('score', 1)
            ->where('created_at', '>=', now()->subDays(5))
            ->groupBy('created_at')
            ->get();
        //        dd($absences->count(),$prev_week->count());

        $students = Profile::where('type', 'student')->where('is_active', true)->orderBy('id', 'desc')->take(8)->get();
        $studentCount = Profile::where('type', 'student')->where('is_active', true)->count();
        $teachers = Profile::where('type', 'teacher')->where('is_active', true)->get();
        $classes = Course::where('is_active', true)->get();

        return view('pages.dashboard', compact('this_week', 'prev_week', 'students', 'teachers', 'studentCount', 'classes', 'absences'));
    }
}
