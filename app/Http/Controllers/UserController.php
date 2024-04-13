<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\Teacher;

//use App\Models\Parent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {

        return view('pages.settings.users');
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'dob' => 'required|date',
            'id_number' => 'required|string',
            'phone_number' => 'nullable|string',
            'sex' => 'required|string',
            'role_id' => 'required|integer'

        ]);

        try {
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'role_id' => $validatedData['role_id'],
                'password' => Hash::make($validatedData['email'])
            ]);
            $user->save();
            $validatedData['user_id'] = $user->id;
            $role = Role::find($validatedData['role_id']);
            if ($role->name == 'student') {
                $this->createStudent($validatedData);
            } elseif ($role->name == 'teacher') {
                $this->createTeacher($validatedData);
            } elseif ($role->name == 'parent') {
                $this->createParent($validatedData);
            } elseif ($role->name == 'admin') {
                $this->createAdmin($validatedData);
            } else {
                return back()->with('error', 'Invalid role');
            }

            return back()->with('success', 'User has been created');
        } catch (\Exception $e) {
            // Log the error message for debugging
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to create user');
        }
    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string',
            'email' => 'required|email',
            'dob' => 'required|date',
            'id_number' => 'required|string',
            'phone_number' => 'nullable|string',
            'sex' => 'required|string',
        ]);

        try {
            $user = User::find($validatedData['id']);
            $user->update([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['email'])
            ]);
            unset($validatedData['id']);
            $name = $this->splitName($validatedData['name']);
            $validatedData['first_name'] = $name[0];
            $validatedData['last_name'] = $name[1];
            $user->userable->update($validatedData);
            return back()->with('success', 'User has been updated');
        } catch (\Exception $e) {
            // Log the error message for debugging
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to update user');
        }
    }


    public function toggle($id): RedirectResponse
    {

        try {
            $user = User::find($id);
            $user->userable->is_active = !$user->userable->is_active;
            $user->userable->save();
            return back()->with('success', 'User status has been updated');


        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

    }

    private function splitName($fullName)
    {
        $lastSpacePosition = strrpos($fullName, ' ');
        $firstName = substr($fullName, 0, $lastSpacePosition);
        $lastName = substr($fullName, $lastSpacePosition + 1);

        return [$firstName, $lastName];
    }

    public function createStudent($data)
    {
        [$firstName, $lastName] = $this->splitName($data['name']);

        Student::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'dob' => $data['dob'],
            'sex' => $data['sex'],
            'id_number' => $data['id_number'],
            'phone_number' => $data['phone_number'],
            'user_id' => $data['user_id']
        ]);
    }

    public function createTeacher($data)
    {
        [$firstName, $lastName] = $this->splitName($data['name']);

        Teacher::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'dob' => $data['dob'],
            'sex' => $data['sex'],
            'id_number' => $data['id_number'],
            'phone_number' => $data['phone_number'],
            'user_id' => $data['user_id']
        ]);
    }


    public function createParent($data)
    {
        [$firstName, $lastName] = $this->splitName($data['name']);

        StudentParent::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'dob' => $data['dob'],
            'sex' => $data['sex'],
            'id_number' => $data['id_number'],
            'phone_number' => $data['phone_number'],
            'user_id' => $data['user_id']
        ]);
    }

    public function createAdmin($data)
    {
        [$firstName, $lastName] = $this->splitName($data['name']);

        Admin::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'dob' => $data['dob'],
            'sex' => $data['sex'],
            'id_number' => $data['id_number'],
            'phone_number' => $data['phone_number'],
            'user_id' => $data['user_id']
        ]);
    }
}
