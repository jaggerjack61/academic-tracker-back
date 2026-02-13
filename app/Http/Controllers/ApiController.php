<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public function login(Request $request)
    {
        //        Log::info($request->all());
        //        $credentials = $request->only('email', 'password');
        if (Auth::attempt(['email' => $request->credentials['email'], 'password' => $request->credentials['password']])) {
            auth()->user()->tokens()->delete();
            $token = $request->user()->createToken('authToken')->plainTextToken;

            return response()->json(['token' => $token, 'user' => auth()->user(), 'message' => auth()->user()->role->name]);
        }

        return response()->json(['message' => 'unauthorized'], 401);

    }

    public function Classes(Request $request)
    {
        $student = Profile::where('user_id', auth()->user()->id)->where('type', 'student')
            ->with('courses.course.grade', 'courses.course.subject', 'courses.course.activities.logs', 'courses.course.activities.type')
            ->get();

        //        $clases = Course::with('activities','students.student')->get();
        return response()->json(['data' => $student, 'message' => 'student'], 200);
    }

    public function getStudents()
    {
        $parent = Profile::where('user_id', auth()->user()->id)->where('type', 'parent')
            ->with('relationships.student.courses.course.grade', 'relationships.student.courses.course.subject', 'relationships.student.courses.course.activities.logs', 'relationships.student.courses.course.activities.type')
            ->get();

        return response()->json(['data' => $parent, 'message' => 'parent'], 200);
    }

    public function studentUpload(Request $request)
    {

        $file = $request->file('file');
        if ($file) {
            $destinationPath = 'students-files/'.$request->student_id.'/uploads';
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move($destinationPath, $fileName);
            ActivityLog::where('id', $request->log_id)->update(['file' => $destinationPath.'/'.$fileName]);

            return response()->json(['uri' => $destinationPath.'/'.$fileName, 'message' => 'success'], 200);
        } else {
            return response()->json(['message' => 'failed'], 405);
        }
    }
}
