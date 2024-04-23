<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function show()
    {
        return view('auth.passwords.change');
    }

    public function change(Request $request)
    {
        try{
            $request->validate([
                'old_password' => 'required',
                'password' => 'required',
            ]);

            if($request->password == $request->old_password){
                return back()->with('error', 'New password cannot be the same as old password');
            }

            if($request->password != $request->confirm_password){
                return back()->with('error', 'Passwords do not match');
            }

            $user = auth()->user();

            if (!Hash::check($request->old_password, $user->password)) {
                return back()->with('error', 'Old password does not match');
            }
            else{
                $user->password = Hash::make($request->password);
                $user->save();
                return redirect()->route('show-dashboard')->with('success', 'Password changed successfully');
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

    }
}
