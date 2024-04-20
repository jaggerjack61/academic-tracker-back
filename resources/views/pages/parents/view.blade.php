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
                        <h5 class="card-header">{{$parent->name}}</h5>
                    </div>
                    <div class="col-md-3">
                        <a href="{{route('show-users')}}" class="btn btn-sm btn-primary m-1 mt-4 text-white"><i class="bx bxs-pencil"></i></a>
                    </div>
                </div>

                <div class="card-body">
                    <p><strong class="mx-2">Email:</strong> {{$parent->user->email}}</p>
                    <p><strong class="mx-2">ID Number:</strong> {{$parent->id_number}}</p>
                    <p><strong class="mx-2">Phone:</strong> {{$parent->phone_number}}</p>
                    <p><strong class="mx-2">DOB:</strong> {{$parent->dob}}</p>
                    <p><strong class="mx-2">Sex:</strong> {{$parent->sex}}</p>
                </div>
            </div>
        </div>
        <div class="col-9">

            <div class="card">
                <h5 class="card-header">Students</h5>
                <div class="table-responsive text-nowrap">
                    <a class="btn btn-sm btn-info mx-3 text-white" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Add
                        New</a>
                    <table class="table">
                        <thead>

                        <tr>
                            <th>Name</th>
                            <th>ID Number</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        @foreach($parent->relationships as $relationship)
                            <tr>
                                <td>{{$relationship->student->name}}</td>
                                <td>{{$relationship->student->id_number}}</td>
                                <td>
                        <span>
                            <a class="btn btn-sm btn-primary text-white"
                               href="{{route('view-student',['student' => $relationship->student_id])}}"
                            >View</a>
                            <a class="btn btn-sm btn-warning text-white"
                               href="{{route('remove-relationship',['relationship' => $relationship->id])}}"
                            >Remove</a>


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
                    <form action="{{route('add-relationship')}}" method="post">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">New Relationships</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" value="{{$parent->id}}" name="parent_id">
                            <label for="class">Parents</label>
                            <select style="width: 100%" required name="students[]" multiple="multiple" id="select22">
                                @foreach($students as $student)
                                    <option
                                        value="{{$student->id}}">{{$student->name}} {{$student->id_number}}</option>
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
    {{--                        <input type="hidden" value="{{$parent->user_id}}" name="id">--}}
    {{--                        <label>Email</label>--}}
    {{--                        <input type="email" value="{{$parent->user->email}}" class="form-control" name="email" required />--}}
    {{--                        <label>Full Name</label>--}}
    {{--                        <input type="text" value="{{$parent->name}}" class="form-control" name="name" required />--}}
    {{--                        <label>ID Number--}}
    {{--                        <input type="text" value="{{$parent->id_number}}" class="form-control" name="id_number" required /></label>--}}
    {{--                        <label>Phone Number--}}
    {{--                        <input type="text" value="{{$parent->phone_number}}" class="form-control" name="phone_number" required /></label>--}}
    {{--                        <label>Date of Birth <input type="date" value="{{$parent->dob}}" class="form-control" name="dob" required></label>--}}
    {{--                        <label class="col-6">Sex <select name="sex" class="form-control" required>--}}
    {{--                                <option value="male" {{$parent->sex=='male'?'selected':''}}>Male</option>--}}
    {{--                                <option value="female" {{$parent->sex=='female'?'selected':''}}>Female</option>--}}
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
