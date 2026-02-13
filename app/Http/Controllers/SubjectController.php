<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubjectController extends Controller
{
    public function index()
    {
        //        $subjects = Subject::where('is_active', true)->get();
        $subjects = Subject::all();

        return view('pages.settings.subjects', compact('subjects'));
    }

    public function create(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string',
        ]);

        try {
            Subject::create($validatedData);

            return back()->with('success', 'Subject has been created');
        } catch (\Exception $e) {
            // Log the error message for debugging
            Log::error($e->getMessage());

            return back()->with('error', 'Failed to create subject');
        }
    }

    public function edit(Request $request)
    {
        $subject = Subject::find($request->id);
        $validatedData = $request->validate([
            'name' => 'required|string',
        ]);

        try {
            $subject->update($validatedData);

            return back()->with('success', 'Subject has been updated');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return back()->with('error', 'Failed to update subject');
        }
    }

    public function toggle($id): RedirectResponse
    {
        $subject = Subject::find($id);
        try {

            $subject->is_active = ! $subject->is_active;
            $subject->save();

            return back()->with('success', 'Subject status has been updated');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

    }
}
