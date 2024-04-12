@extends('layouts.base')

@section('title')
    View Student
@endsection

@section('content')
    <script src="/custom/js/jquery-3.7.1.min.js"></script>
    <link href="/custom/css/select2.min.css" rel="stylesheet" />
    <script src="/custom/js/select2.min.js"></script>


    <div class="row">
    <div class="col-3">
        <div class="card">
            <div class="row">
                <div class="col-8">
                    <h5 class="card-header">{{$student->name}}</h5>
                </div>
                <div class="col-4">
                    <a class="btn btn-sm btn-primary m-2 mt-4 text-white"><i class="bx bx-pencil"></i>Edit</a>
                </div>
            </div>

            <div class="card-body">
                <p><strong class="mx-2">Email:</strong> {{$student->user->email}}</p>
                <p><strong class="mx-2">Phone:</strong> {{$student->phone_number}}</p>
                <p><strong class="mx-2">DOB:</strong> {{$student->dob}}</p>
                <p><strong class="mx-2">Sex:</strong> {{$student->sex}}</p>
            </div>
        </div>
    </div>
    <div class="col-9">

            <div class="card">
                <h5 class="card-header">Classes</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                        <a class="btn btn-sm btn-info mx-3 text-white" data-bs-toggle="modal"
                           data-bs-target="#staticBackdrop">Add
                            New</a>
                        <tr>
                            <th>Name</th>
                            <th>Grade</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        @foreach($student->courses as $class)
                            <tr>
                                <td>{{$class->course->name}}</td>
                                <td>{{$class->course->grade->name}}</td>
                                <td>{{$class->course->subject->name}}</td>
                                <td>{{$class->course->is_active}}</td>
                                <td>
                        <span>
                            <a class="btn btn-sm btn-secondary text-white" href="{{route('view-activities',['class' => $class->id, 'student' => $student->id])}}">View</a>
                            <a class="btn btn-sm btn-danger text-white" href="{{route('unenroll-student',['class' => $class->course_id, 'student' => $student->id])}}">Dis-enroll</a>
                        </span>
                                </td>
                            </tr>
                        @endforeach


                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('enroll-student')}}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">New Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" value="{{$student->id}}" name="student_id">
                        <label for="class">Classes</label>
                        <select style="width: 100%" required name="class[]" multiple="multiple" id="select22">
                            @foreach($classes as $class)
                                <option value="{{$class->id}}">{{$class->name}} {{$class->grade->name}} {{$class->subject->name}}</option>
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

    <script>
        $('#select22').select2({
            dropdownParent: $('#staticBackdrop'),
            containerCssClass: 'big-container',
            placeholder: 'Select an option'
        });
    </script>
@endsection
