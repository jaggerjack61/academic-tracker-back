<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public function login(Request $request)
    {
//        Log::info($request->all());
//        $credentials = $request->only('email', 'password');
        if (Auth::attempt(['email'=>$request->credentials['email'],'password'=>$request->credentials['password']])) {
            auth()->user()->tokens()->delete();
            $token = $request->user()->createToken('authToken')->plainTextToken;
            return response()->json(['token' => $token,'user'=>auth()->user(),'message' =>'success']);
        }

        return response()->json(['message' => 'unauthorized'], 401);

    }


    public function Classes(Request $request)
    {
        $student =Student::where('user_id', auth()->user()->id
        )
            ->with('courses.course.grade','courses.course.subject','courses.course.activities.logs','courses.course.activities.type')
            ->get();
//        $clases = Course::with('activities','students.student')->get();
        return response()->json(['data' =>$student,'message' =>'student'], 200);
    }
}
