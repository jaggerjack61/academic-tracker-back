@extends('layouts.base')

@section('title')
    {{$activity->name}}
@endsection

@section('content')

    <div class="row">
        <div class="col-3">
            <div class="card">
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="card-header">{{$activity->name}}</h5>
                    </div>

                </div>

                <div class="card-body">
                    <p><strong class="mx-2">Teacher:</strong> {{$activity->teacher->name}}</p>
                    <p><strong class="mx-2">Total:</strong> {{$activity->total}}</p>
                    <p><strong class="mx-2">Note:</strong> {{$activity->note}}</p>
                    <p><strong class="mx-2">Due Date:</strong> {{$activity->due_date}}</p>
                    <p><strong class="mx-2">File:</strong> <a href='/{{$activity->file}}'>{{$activity->file?'Download':''}}</a><i
                            class="bx bx-link"></i></p>

                </div>
            </div>
        </div>
        <div class="col-9">

            <div class="card">
                <form action="{{route('add-class-activity-log')}}" method="post">
                    @csrf
                    <input type="hidden" name="activity_id" value="{{$activity->id}}">
                    <h5 class="card-header">Students</h5>
                    <div class="table-responsive text-nowrap">
                        <div class="row">
                            <div class="col-md-9">

                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-sm btn-primary"><i class="bx bx-check-double"></i>Submit
                                    All
                                </button>
                            </div>
                        </div>
                        <table class="table">
                            <thead>

                            <tr>
                                <th>Name</th>
                                <th>{{$activity->type->type == 'value'?'Mark':''}}{{$activity->type->type == 'boolean'?'Status':''}}</th>
                                <th>Input</th>
                            </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">

                            @foreach($activity->course->students as $student)

                                <tr>
                                    {{--                                {{dd($student)}}--}}
                                    <td>{{$student->student->name}}</td>
                                    <td>
                                        @foreach($activity->logs as $log)
                                            @if($log->student_id == $student->student_id)
                                                @if($activity->type->type == 'value')
                                                    {{$log->score??'No mark yet'}}/{{$activity->total}}
                                                @elseif($activity->type->type == 'boolean')
                                                    {{$log->score == 2?$log->activity->type->true_value:''}}
                                                    {{$log->score == 1?$log->activity->type->false_value:''}}

                                                @endif
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>

                                        @if($activity->type->type == 'value')
                                            <input type="text" class="form-control" name="student[]"/>
                                        @elseif($activity->type->type == 'boolean')
                                            <input type="checkbox" name="student[{{$student->student_id}}]"
                                            @foreach($activity->logs as $log)
                                                @if($log->student_id == $student->student_id)
                                                    {{$log->score == 2?'checked':''}}
                                                    @endif
                                                @endforeach
                                            />
                                        @endif

                                    </td>

                                </tr>

                            @endforeach


                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
