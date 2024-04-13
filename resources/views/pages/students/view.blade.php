@extends('layouts.base')

@section('title')
    View Student
@endsection

@section('content')
    <script src="/custom/js/jquery-3.7.1.min.js"></script>
    <link href="/custom/css/select2.min.css" rel="stylesheet"/>
    <script src="/custom/js/select2.min.js"></script>


    <div class="row">
        <div class="col-3">
            <div class="card">
                <div class="row">
                    <div class="col-md-9">
                        <h5 class="card-header">{{$student->name}}</h5>
                    </div>
                    <div class="col-md-3">
                        <a href="{{route('show-users')}}" class="btn btn-sm btn-primary m-1 mt-4 text-white"><i class="bx bxs-pencil"></i></a>
                    </div>
                </div>

                <div class="card-body">
                    <p><strong class="mx-2">Email:</strong> {{$student->user->email}}</p>
                    <p><strong class="mx-2">ID Number:</strong> {{$student->id_number}}</p>
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
                    <a class="btn btn-sm btn-info mx-3 text-white" data-bs-toggle="modal"
                       data-bs-target="#staticBackdrop">Add
                        New</a>
                    <table class="table">
                        <thead>

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
                                <td>{{$class->course->is_active?'Enrolled':'Un-enrolled'}}</td>
                                <td>
                        <span>
                            <a class="btn btn-sm btn-secondary text-white"
                               href="{{route('view-activities',['class' => $class->id, 'student' => $student->id])}}">View</a>
                            <a class="btn btn-sm btn-danger text-white"
                               href="{{route('unenroll-student',['class' => $class->course_id, 'student' => $student->id])}}">Un-enroll</a>
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
                                <option
                                    value="{{$class->id}}">{{$class->name}} {{$class->grade->name}} {{$class->subject->name}}</option>
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

{{--    <div class="modal fade" id="editModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"--}}
{{--         aria-labelledby="staticBackdropLabel" aria-hidden="true">--}}
{{--        <div class="modal-dialog">--}}
{{--            <div class="modal-content">--}}
{{--                <form action="{{route('edit-user')}}" method="patch">--}}
{{--                    @csrf--}}
{{--                    <div class="modal-header">--}}
{{--                        <h5 class="modal-title" id="staticBackdropLabel">Edit Student Details</h5>--}}
{{--                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>--}}
{{--                    </div>--}}
{{--                    <div class="modal-body">--}}
{{--                        <input type="hidden" value="{{$student->user_id}}" name="id">--}}
{{--                        <label>Email</label>--}}
{{--                        <input type="email" value="{{$student->user->email}}" class="form-control" name="email" required />--}}
{{--                        <label>Full Name</label>--}}
{{--                        <input type="text" value="{{$student->name}}" class="form-control" name="name" required />--}}
{{--                        <label>ID Number--}}
{{--                        <input type="text" value="{{$student->id_number}}" class="form-control" name="id_number" required /></label>--}}
{{--                        <label>Phone Number--}}
{{--                        <input type="text" value="{{$student->phone_number}}" class="form-control" name="phone_number" required /></label>--}}
{{--                        <label>Date of Birth <input type="date" value="{{$student->dob}}" class="form-control" name="dob" required></label>--}}
{{--                        <label class="col-6">Sex <select name="sex" class="form-control" required>--}}
{{--                                <option value="male" {{$student->sex=='male'?'selected':''}}>Male</option>--}}
{{--                                <option value="female" {{$student->sex=='female'?'selected':''}}>Female</option>--}}
{{--                            </select></label>--}}
{{--                    </div>--}}
{{--                    <div class="modal-footer">--}}
{{--                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>--}}
{{--                        <button type="submit" class="btn btn-primary">Submit</button>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}


    <script>
        $('#select22').select2({
            dropdownParent: $('#staticBackdrop'),
            containerCssClass: 'big-container',
            placeholder: 'Select an option'
        });
    </script>
@endsection
