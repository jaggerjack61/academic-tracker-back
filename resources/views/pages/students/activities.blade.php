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
                        @if($type->type == 'value')
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Note</th>
                                    <th>Mark</th>
                                    <th>Total</th>
                                    <th>File</th>
                                    <th>Due Date</th>
                                    <th>Action</th>

                                </tr>
                                </thead>
                                <tbody>
                                {{--                            {{dd($logs)}}--}}
                                @foreach($logs as $activity)
                                    @if($type->id == $activity->activity->type->id)
                                        <tr>
                                            <td>{{$activity->activity->name}}</td>
                                            <td class="text-wrap">{{optional($activity->activity)->note}}</td>
                                            <td>{{$activity->score}}</td>
                                            <td>{{optional($activity->activity)->total}}</td>
                                            <td>@if($activity->file)
                                                    <a class="btn btn-sm btn-success" href="/{{$activity->file}}">Download</a>
                                                @else
                                                    No File
                                                @endif
                                            </td>
                                            <td>{{optional($activity->activity)->due_date?:'No due date'}}</td>
                                            <td>
                                            <span>
                                                <a class="btn btn-sm btn-primary mb-1 text-white">View Class</a>
{{--                                                @if($activity->type->type != 'static')--}}
                                                {{--                                                    <a href="{{route('view-class-activity', ['activity' => $activity->id])}}" class="btn btn-sm mb-1 btn-secondary text-white">View</a>--}}

                                                {{--                                                @endif--}}
                                            </span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        @elseif($type->type == 'boolean')
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Note</th>
                                    <th>Status</th>
                                    <th>File</th>
                                    <th>Due Date</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                {{--                            {{dd($logs)}}--}}
                                @foreach($logs as $activity)
                                    @if($type->id == $activity->activity->type->id)
                                        <tr>
                                            <td>{{$activity->activity->name}}</td>
                                            <td class="text-wrap">{{optional($activity->activity)->note}}</td>
                                            <td>{{$activity->score == 1?$activity->activity->type->false_value:''}}
                                                {{$activity->score == 2?$activity->activity->type->true_value:''}}</td>
                                            <td>@if($activity->file)
                                                    <a class="btn btn-sm btn-success" href="/{{$activity->file}}">Download</a>
                                                @else
                                                    No File
                                                @endif
                                            </td>
                                            <td>{{optional($activity->activity)->due_date?:'No due date'}}</td>
                                            <td>
                                            <span>
                                                <a class="btn btn-sm btn-primary mb-1 text-white">View Class</a>
{{--                                                @if($activity->type->type != 'static')--}}
                                                {{--                                                    <a href="{{route('view-class-activity', ['activity' => $activity->id])}}" class="btn btn-sm mb-1 btn-secondary text-white">View</a>--}}

                                                {{--                                                @endif--}}
                                            </span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        @elseif($type->type == 'static')
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Note</th>
                                    <th>File</th>
                                    <th>Due Date</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                {{--                            {{dd($logs)}}--}}
                                @foreach($logs as $activity)
                                    @if($type->id == $activity->activity->type->id)
                                        <tr>
                                            <td>{{$activity->activity->name}}</td>
                                            <td class="text-wrap">{{optional($activity->activity)->note}}</td>

                                            <td>@if($activity->file)
                                                    <a class="btn btn-sm btn-success" href="/{{$activity->file}}">Download</a>
                                                @else
                                                    No File
                                                @endif
                                            </td>
                                            <td>{{optional($activity->activity)->due_date?:'No due date'}}</td>
                                            <td>
                                            <span>
                                                <a class="btn btn-sm btn-primary mb-1 text-white">View Class</a>
{{--                                                @if($activity->type->type != 'static')--}}
                                                {{--                                                    <a href="{{route('view-class-activity', ['activity' => $activity->id])}}" class="btn btn-sm mb-1 btn-secondary text-white">View</a>--}}

                                                {{--                                                @endif--}}
                                            </span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        @endif
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
