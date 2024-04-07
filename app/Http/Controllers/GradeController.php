<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GradeController extends Controller
{
    public function index()
    {
//        $grades = Grade::where('is_active', true)->get();
        $grades = Grade::all();
        return view('pages.settings.grades', compact('grades'));
    }

    public function create(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string'
        ]);

        try {
            Grade::create($validatedData);

            return back()->with('success', 'Grade has been created');
        } catch (\Exception $e) {
            // Log the error message for debugging
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to create grade');
        }
    }

    public function edit(Request $request)
    {
        $grade = Grade::find($request->id);
        $validatedData = $request->validate([
            'name' => 'required|string'
        ]);

        try {
            $grade->update($validatedData);
            return back()->with('success', 'Grade has been updated');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to update grade');
        }
    }

    public function toggle($id): RedirectResponse
    {
        $grade = Grade::find($id);
        try {
            if($grade->is_active){
                $grade->is_active = false;
                $grade->save();
                return back()->with('success', 'Grade has been deactivated');
            }
            else{
                $grade->is_active = true;
                $grade->save();
                return back()->with('success', 'Grade has been activated');
            }

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

    }
}
