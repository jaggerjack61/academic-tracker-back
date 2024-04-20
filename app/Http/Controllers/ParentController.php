<?php

namespace App\Http\Controllers;

use App\Models\Relationship;
use App\Models\Student;
use App\Models\StudentParent;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    public function show()
    {
        $parents = StudentParent::where('is_active', true)->get();
        return view('pages.parents.index', compact('parents'));
    }

    public function view(StudentParent $parent)
    {
        $students = Student::where('is_active', true)->get();
        return view('pages.parents.view', compact('parent', 'students'));
    }

    public function addRelationship(Request $request)
    {
        $students = $request->input('students');
        foreach ($students as $student) {
            $relationship = Relationship::where('student_id', $student)->where('parent_id', $request->parent_id)->first();
            if ($relationship) {
                if($relationship->is_active) {
                    return redirect()->back()->with('error', 'Student '.$relationship->student->name.' already exists in this parent');
                }
                else {
                    $relationship->update([
                        'is_active' => true
                    ]);
                }
            }
            else{
                Relationship::create([
                    'student_id' => $student,
                    'parent_id' => $request->parent_id,
                    'is_active' => true
                ]);
            }

        }
        return redirect()->back()->with('success', 'Student relationship added successfully');
    }

    public function removeRelationship(Relationship $relationship)
    {
        $relationship->update([
            'is_active' => false
        ]);
        return redirect()->back()->with('success', 'Student relationship removed successfully');
    }
}
