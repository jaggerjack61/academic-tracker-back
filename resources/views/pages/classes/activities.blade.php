@extends('layouts.base')

@section('title')
    Activites
@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">Activities</h5>
        <nav>
            <div class="nav nav-tabs flex-row" id="nav-tab" role="tablist">
                @foreach($activityTypes as $type)
                    <button class="btn btn-sm btn-primary mx-1 ms-3 {{$type->id == $first->id?'active':''}}" id="nav-home-tab{{$type->id}}"
                            data-bs-toggle="tab" data-bs-target="#nav-home{{$type->id}}"
                            type="button" role="tab" aria-controls="nav-home{{$type->id}}" aria-selected="{{$type->id == $first->id?'true':'false'}}">{{$type->name}}
                    </button>
                @endforeach


            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">

            {{--            Attendance--}}
            @foreach($activityTypes as $type)
            <div class="tab-pane fade show {{$type->id == $first->id?'active':''}}" id="nav-home{{$type->id}}" role="tabpanel" aria-labelledby="nav-home-tab{{$type->id}}">

                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Date{{$type->id}}</th>
                            <th>Status</th>
                            <th>Action</th>

                        </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        <tr>
                            <td>01/01/2023</td>
                            <td><span class="badge bg-label-primary me-1">Present</span></td>
                            <td>
                        <span>
                            <a class="btn btn-sm btn-success text-white">Mark Present</a>
                            <a class="btn btn-sm btn-danger text-white">Mark Absent</a>
                        </span>
                            </td>
                        </tr>
                        <tr>
                            <td>02/01/2023</td>
                            <td><span class="badge bg-label-primary me-1">Present</span></td>
                            <td>
                        <span>
                            <a class="btn btn-sm btn-success text-white">Mark Present</a>
                            <a class="btn btn-sm btn-danger text-white">Mark Absent</a>
                        </span>
                            </td>
                        </tr>
                        <tr>
                            <td>03/01/2023</td>
                            <td><span class="badge bg-label-danger me-1">Absent</span></td>
                            <td>
                        <span>
                            <a class="btn btn-sm btn-success text-white">Mark Present</a>
                            <a class="btn btn-sm btn-danger text-white">Mark Absent</a>
                        </span>
                            </td>
                        </tr>
                        <tr>
                            <td>04/01/2023</td>
                            <td><span class="badge bg-label-primary me-1">Present</span></td>
                            <td>
                        <span>
                            <a class="btn btn-sm btn-success text-white">Mark Present</a>
                            <a class="btn btn-sm btn-danger text-white">Mark Absent</a>
                        </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach

        </div>
    </div>



    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">New Teacher</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <input type="text" class="form-control my-2" placeholder="Teacher ID" name="teacher_id"
                               required/>
                        <input type="text" class="form-control my-2" placeholder="Full Name" name="full_name" required/>
                        <label for="sex">Sex</label>
                        <select class="form-control" required name="sex" id="sex">
                            <option>Male</option>
                            <option>Female</option>
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
