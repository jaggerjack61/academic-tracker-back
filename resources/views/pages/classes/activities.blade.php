@extends('layouts.base')

@section('title')
    Activites
@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">Activities</h5>
        <span>
            <a data-bs-toggle="modal" data-bs-target="#staticBackdrop" class="btn btn-sm btn-success text-white m-3">New Activity</a>
        </span>

        <nav>
            <div class="nav nav-tabs flex-row" id="nav-tab" role="tablist">
                @foreach($activityTypes as $type)
                    <button class="btn btn-sm btn-primary mx-1 ms-3 {{$type->id == $first?'active':''}}"
                            id="nav-home-tab{{$type->id}}"
                            data-bs-toggle="tab" data-bs-target="#nav-home{{$type->id}}"
                            type="button" role="tab" aria-controls="nav-home{{$type->id}}"
                            aria-selected="{{$type->id == $first?'true':'false'}}">{{$type->name}}
                    </button>
                @endforeach


            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">

            {{--            Attendance--}}
            @foreach($activityTypes as $type)
                <div class="tab-pane fade show {{$type->id == $first?'active':''}}" id="nav-home{{$type->id}}"
                     role="tabpanel" aria-labelledby="nav-home-tab{{$type->id}}">

                    <div class="table-responsive text-wrap">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Note</th>
                                <th>Out of</th>
                                <th>File</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Action</th>

                            </tr>
                            </thead>
                            <tbody>
                            @foreach($activities as $activity)
                                @if($type->id == $activity->activity_type_id)
                                    <tr>
                                        <td>{{$activity->name}}</td>
                                        <td>{{$activity->note}}</td>
                                        <td>{{$activity->total}}</td>
                                        <td>@if($activity->file)
                                            <a class="btn btn-sm btn-success" href="/{{$activity->file}}">Download</a>
                                            @else
                                            No File
                                            @endif
                                        </td>
                                        <td>{{$activity->due_date?:'No due date'}}</td>
                                        <td>{{$activity->is_active?'Active':'Inactive'}}</td>
                                        <td>
                                            <span>
                                                <a class="btn btn-sm btn-primary mb-1 text-white">Edit</a>
                                                @if($activity->type->type != 'static')
                                                <a class="btn btn-sm mb-1 btn-secondary text-white">View</a>

                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
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
                <form method="post" action="{{route('add-class-activity')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">New Activity</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" value="{{$class->id}}" name="course_id">
                        <input type="hidden" value="{{$class->teacher->teacher_id}}" name="teacher_id">
                        <input type="text" placeholder="Activity name eg Tuesday 4 January Homework" name="name"
                               class="form-control my-2" required />
                        <select class="form-control my-2" required name="activity_type_id">
                            <option value="">Select Activity Type</option>
                            @foreach($activityTypes as $type)
                                <option value="{{$type->id}}">{{$type->name}}</option>
                            @endforeach
                        </select>
                        <label>Note(optional)</label>
                        <textarea type="text" class="form-control my-2" name="note"></textarea>

                        <input type="number" step="1" class="form-control my-2" placeholder="Full mark(optional)"
                               name="total"/>
                        <label>Due Date(optional)</label>
                        <input class="form-control" type="date" name="due_date"/>
                        <label>Files(optional)</label>
                        <input class="form-control" type="file" name="file"/>

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
