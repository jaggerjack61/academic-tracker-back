<?php

namespace App\Http\Controllers;

use App\Models\ActivityType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ActivityTypeController extends Controller
{
    public function index()
    {
        $activityTypes = ActivityType::all();
        return view('pages.settings.activity-types', compact('activityTypes'));
    }

    public function create(Request $request)
    {
        $file = $request->file('file');
        $destinationPath = 'activity_types/files';
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move($destinationPath, $fileName);

        $validatedData = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|string|in:boolean,value,static',
            'true_value' => 'nullable|string',
            'false_value' => 'nullable|string',
        ]);
        $validatedData['image'] = $destinationPath . '/' . $fileName;

        try {
            ActivityType::create($validatedData);

            return back()->with('success', 'Activity type has been created');
        } catch (\Exception $e) {
            // Log the error message for debugging
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to create activity type');
        }
    }

    public function edit(Request $request)
    {
        try {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);
        $file = $request->file('file');
        if($file) {
            $destinationPath = 'activity_types/files';
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($destinationPath, $fileName);
            $validatedData['image'] = $destinationPath . '/' . $fileName;
        }
        $activityType = ActivityType::find($request->id);



            $activityType->update($validatedData);
            return back()->with('success', 'Activity type has been updated');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to update activity type'.$e->getMessage());
        }
    }

    public function toggle($id): RedirectResponse
    {
        $activityType = ActivityType::find($id);
        try {
            if($activityType->is_active){
                $activityType->is_active = false;
                $activityType->save();
                return back()->with('success', 'Activity type has been deactivated');
            }
            else{
                $activityType->is_active = true;
                $activityType->save();
                return back()->with('success', 'Activity type has been activated');
            }

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

    }
}
