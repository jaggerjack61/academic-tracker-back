<?php

namespace App\Http\Controllers;

use App\Models\Term;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TermController extends Controller
{
    public function index()
    {
//        $terms = Term::where('is_active', true)->get();
        $terms = Term::all();
        return view('pages.settings.terms', compact('terms'));
    }

    public function create(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        try {
            Term::create($validatedData);

            return back()->with('success', 'Term has been created');
        } catch (\Exception $e) {
            // Log the error message for debugging
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to create term');
        }
    }

    public function toggle($id): RedirectResponse
    {
        $term = Term::find($id);
        try {
            if($term->is_active){
                $term->is_active = false;
                $term->save();
                return back()->with('success', 'Term has been deactivated');
            }
            else{
                $term->is_active = true;
                $term->save();
                return back()->with('success', 'Term has been activated');
            }

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

    }
}
