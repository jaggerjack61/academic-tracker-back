@extends('layouts.base')

@section('title')
    {{$class->name}}
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
                        <h5 class="card-header">{{$class->name}}</h5>
                    </div>
                    <div class="col-md-3">
                        <a href="{{route('show-users')}}" class="btn btn-sm btn-primary m-1 mt-4 text-white"><i
                                class="bx bxs-pencil"></i></a>
                    </div>
                </div>

                <div class="card-body">
                    <p><strong class="mx-2">Teacher:</strong> {{optional($class->teacher)->name}}</p>
                    <p><strong class="mx-2">Grade:</strong> {{$class->grade->name}}</p>
                    <p><strong class="mx-2">Subject:</strong> {{$class->subject->name}}</p>

                </div>
            </div>
        </div>
        <div class="col-9">

            <div class="card">
                <h5 class="card-header">Students</h5>
                <div class="table-responsive text-nowrap">
                    <div class="row">
                        <div class="col-9">
                            <a class="btn btn-sm btn-info ms-3 me-1 text-white" data-bs-toggle="modal"
                               data-bs-target="#staticBackdrop">Add
                                New</a>
                            <a class="btn btn-sm btn-success mx-1 text-white" data-bs-toggle="modal"
                               data-bs-target="#staticBackdrop1">Copy Students</a>
                            <a class="btn btn-sm btn-warning mx-1 text-white" data-bs-toggle="modal"
                               data-bs-target="#staticBackdrop2">Move Students</a>
                        </div>
                        <div class="col-3">
                            <a href="{{route('view-class-activities',['course' => $class->id])}}"
                               data-bs-target="#staticBackdrop2">Activities</a>
                        </div>
                    </div>
                    <table class="table">
                        <thead>

                        <tr>
                            <th>Name</th>
                            <th>Sex</th>
                            <th>Date of Birth</th>

                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        @foreach($class->students as $student)
                            <tr>
                                {{--                                {{dd($student)}}--}}
                                <td>{{$student->student->name}}</td>
                                <td>{{$student->student->sex}}</td>
                                <td>{{$student->student->dob}}</td>

                                <td>
                        <span>
                            <a class="btn btn-sm btn-secondary text-white"
                               href="{{route('view-student-activities',['class' => $class->id, 'student' => $student->student_id])}}">View</a>
                            <a class="btn btn-sm btn-danger text-white"
                               href="{{route('unenroll-student',['class' => $class->id, 'student' => $student->student_id])}}">Un-enroll</a>
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


    <div class="modal fade" id="staticBackdrop1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('copy-class')}}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Copy Students to Other Classes</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" value="{{$class->id}}" name="currentCourse">
                        <label for="class">Students</label>
                        <select style="width: 100%" required name="courses[]" multiple="multiple" id="select221">
                            @foreach($classes as $course)
                                <option
                                    value="{{$course->id}}">{{$course->name}} {{$course->grade->name}} {{$course->subject->name}}</option>
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

    <div class="modal fade" id="staticBackdrop2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('move-class')}}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Move Students to Other Classes</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" value="{{$class->id}}" name="currentCourse">
                        <label for="class">Students</label>
                        <select style="width: 100%" required name="courses[]" multiple="multiple" id="select222">
                            @foreach($classes as $course)
                                <option
                                    value="{{$course->id}}">{{$course->name}} {{$course->grade->name}} {{$course->subject->name}}</option>
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


    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('enroll-class')}}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">New Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" value="{{$class->id}}" name="course_id">
                        <label for="class">Students</label>
                        <select style="width: 100%" required name="students[]" multiple="multiple" id="select22">
                            @foreach($students as $student)
                                <option
                                    value="{{$student->id}}">{{$student->name}}</option>
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

        $('#select221').select2({
            dropdownParent: $('#staticBackdrop1'),
            containerCssClass: 'big-container',
            placeholder: 'Select an option'
        });

        $('#select222').select2({
            dropdownParent: $('#staticBackdrop2'),
            containerCssClass: 'big-container',
            placeholder: 'Select an option'
        });
    </script>
@endsection
