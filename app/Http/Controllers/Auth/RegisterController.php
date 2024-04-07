<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        if ($data['type'] == 'student') {
            $this->createStudent($data);
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => $data['role']
            ]);

        } elseif ($data['type'] == 'teacher') {
            $this->createTeacher($data);
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => $data['role']
            ]);
        } elseif ($data['type'] == 'admin') {
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => $data['role']
            ]);
        } else  {
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => $data['role']
            ]);
        }

    }

    public function createStudent($data)
    {
        $lastSpacePosition = strrpos($data['name'], ' ');
        $firstName = substr($data['name'], 0, $lastSpacePosition);
        $lastName = substr($data['name'], $lastSpacePosition + 1);

        return Student::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'dob' => $data['dob'],
            'sex' => $data['sex'],
        ]);
    }

    public function createTeacher($data)
    {
        $lastSpacePosition = strrpos($data['name'], ' ');
        $firstName = substr($data['name'], 0, $lastSpacePosition);
        $lastName = substr($data['name'], $lastSpacePosition + 1);

        return Teacher::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'dob' => $data['dob'],
            'sex' => $data['sex'],
            'id_number' => $data['id_number']
        ]);
    }
}
