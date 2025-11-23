@extends('layouts.base')

@section('title')
    My Dashboard
@endsection

@section('content')
    <div class="row">
        <div class="col-12 col-md-4">
            <div class="card">
                <h5 class="card-header">My Profile</h5>
                <div class="card-body">
                    <p><strong>Name:</strong> {{$student->name}}</p>
                    <p><strong>Email:</strong> {{$student->user->email}}</p>
                    <p><strong>ID Number:</strong> {{$student->id_number}}</p>
                    <p><strong>Phone:</strong> {{$student->phone_number}}</p>
                    <p><strong>DOB:</strong> {{$student->dob}}</p>
                    <p><strong>Sex:</strong> {{ucfirst($student->sex)}}</p>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md-8">
            <div class="card">
                <h5 class="card-header">My Classes</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Class Name</th>
                                <th>Grade</th>
                                <th>Subject</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($courses as $enrollment)
                                <tr>
                                    <td>{{$enrollment->course->name}}</td>
                                    <td>{{$enrollment->course->grade->name}}</td>
                                    <td>{{$enrollment->course->subject->name}}</td>
                                    <td>
                                        <a class="btn btn-sm btn-primary text-white" 
                                           href="{{route('student-assignments', ['course' => $enrollment->course_id])}}">
                                            View Assignments
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">You are not enrolled in any classes yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
