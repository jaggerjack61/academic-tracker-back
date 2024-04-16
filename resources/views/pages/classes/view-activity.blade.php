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
                    <p><strong class="mx-2">Fule:</strong> <a href='/{{$activity->file}}'>Download</a><i class="bx bx-link"></i></p>

                </div>
            </div>
        </div>
        <div class="col-9">

            <div class="card">
                <h5 class="card-header">Students</h5>
                <div class="table-responsive text-nowrap">
                    <div class="row">
                        <div class="col-md-9">

                        </div>
                        <div class="col-md-3">
                            <a href="" class="btn btn-sm btn-primary"><i class="bx bx-check-double"></i>Submit All</a>
                        </div>
                    </div>
                    <table class="table">
                        <thead>

                        <tr>
                            <th>Name</th>
                            <th>{{$activity->type->type == 'value'?'Mark':''}}{{$activity->type->type == 'boolean'?'Status':''}}</th>
                            <th>Input</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        @foreach($activity->course->students as $student)
                            <tr>
                                {{--                                {{dd($student)}}--}}
                                <td>{{$student->student->name}}</td>
                                <td>@if($activity->type->type == 'value')
                                        {{$activity->score??'No mark yet'}}/{{$activity->total}}
                                    @elseif($activity->type->type == 'boolean')
                                        {{$activity->score == 2?'Marked as True':''}}
                                        {{$activity->score == 1?'Marked as False':''}}
                                        {{$activity->score == 0?'Not Marked Yet':''}}
                                    @endif
                                 </td>
                                <td>

                                @if($activity->type->type == 'value')
                                   <input type="text" class="form-control" name="student[]" />
                                @elseif($activity->type->type == 'boolean')
                                    <input type="checkbox" name="student[]" />
                                @endif
                                </td>
                                <td>
                        <span>
                            <a class="btn btn-sm btn-secondary text-white"
                               href="{{route('view-student-activities',['class' => '', 'student' => ''])}}">Submit</a>
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


@endsection
