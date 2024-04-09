@extends('layouts.base')

@section('title')
    Classes
@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">Classes</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <a class="btn btn-sm btn-info mx-3 text-white" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Add
                    New</a>
                <tr>
                    <th>Name</th>
                    <th>Grade</th>
                    <th>Subject</th>
                    <th>Teacher</th>
                    <th>Students</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                @foreach($classes as $class)
                    <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{$class->name}}</strong>
                        </td>
                        <td>{{$class->grade->name}}</td>
                        <td>
                            {{$class->subject->name}}
                        </td>
                        <td>{{optional($class->teacher)->teacherName()}}</td>
                        <td>25</td>
                        <td><span class="badge bg-label-primary me-1">{{$class->is_active ? 'Active' : 'Inactive'}}</span></td>
                        <td>
                        <span>
                            <a class="btn btn-sm btn-primary text-white">Edit</a>
                            <a class="btn btn-sm btn-secondary text-white">View</a>
                            <a class="btn btn-sm btn-danger text-white">Deactivate</a>
                        </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>



    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('create-class')}}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">New Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <input type="text" class="form-control my-2" placeholder="Class Name" name="name"
                               required/>

                        <label for="teacher">Teacher</label>
                        <select class="form-control" name="teacher_id" id="teacher">
                            <option value="">Select a teacher</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                        <label for="grade">Grade</label>
                        <select class="form-control" required name="grade_id" id="grade">
                            <option value="">Select a grade</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                            @endforeach
                        </select>
                        <label for="subject">Subject</label>
                        <select class="form-control" required name="subject_id" id="subject">
                            <option value="">Select a subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
