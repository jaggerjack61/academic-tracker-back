@extends('layouts.base')

@section('title')
    My Assignments - {{$course->name}}
@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">
            My Assignments - {{$course->name}} 
            <span class="badge bg-primary">{{$course->grade->name}}</span>
            <span class="badge bg-secondary">{{$course->subject->name}}</span>
        </h5>
        <div class="card-body">
            <a href="{{route('student-dashboard')}}" class="btn btn-sm btn-secondary mb-3">
                <i class="bx bx-arrow-back"></i> Back to Dashboard
            </a>
        </div>

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
            @foreach($activityTypes as $type)
                <div class="tab-pane fade show {{$type->id == $first?'active':''}}" id="nav-home{{$type->id}}"
                     role="tabpanel" aria-labelledby="nav-home-tab{{$type->id}}">

                    <div class="table-responsive text-wrap">
                        @if($type->type == 'value')
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Assignment Name</th>
                                    <th>Note</th>
                                    <th>My Mark</th>
                                    <th>Total</th>
                                    <th>Percentage</th>
                                    <th>File</th>
                                    <th>Due Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($logs as $activity)
                                    @if($type->id == $activity->activity->type->id)
                                        <tr>
                                            <td>{{$activity->activity->name}}</td>
                                            <td class="text-wrap">{{optional($activity->activity)->note}}</td>
                                            <td><strong>{{$activity->score ?? 'Not graded'}}</strong></td>
                                            <td>{{optional($activity->activity)->total}}</td>
                                            <td>
                                                @if($activity->score && optional($activity->activity)->total)
                                                    <span class="badge bg-info">
                                                        {{ number_format(($activity->score / $activity->activity->total) * 100, 1) }}%
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($activity->file)
                                                    <a class="btn btn-sm btn-success" href="/{{$activity->file}}" target="_blank">Download</a>
                                                @else
                                                    No File
                                                @endif
                                            </td>
                                            <td>{{optional($activity->activity)->due_date?:'No due date'}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if($logs->where('activity.type.id', $type->id)->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center">No assignments yet.</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        @elseif($type->type == 'boolean')
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Assignment Name</th>
                                    <th>Note</th>
                                    <th>Status</th>
                                    <th>File</th>
                                    <th>Due Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($logs as $activity)
                                    @if($type->id == $activity->activity->type->id)
                                        <tr>
                                            <td>{{$activity->activity->name}}</td>
                                            <td class="text-wrap">{{optional($activity->activity)->note}}</td>
                                            <td>
                                                <span class="badge {{$activity->score == 2 ? 'bg-success' : 'bg-danger'}}">
                                                    {{$activity->score == 1 ? $activity->activity->type->false_value : ''}}
                                                    {{$activity->score == 2 ? $activity->activity->type->true_value : ''}}
                                                </span>
                                            </td>
                                            <td>
                                                @if($activity->file)
                                                    <a class="btn btn-sm btn-success" href="/{{$activity->file}}" target="_blank">Download</a>
                                                @else
                                                    No File
                                                @endif
                                            </td>
                                            <td>{{optional($activity->activity)->due_date?:'No due date'}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if($logs->where('activity.type.id', $type->id)->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center">No assignments yet.</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        @elseif($type->type == 'static')
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Assignment Name</th>
                                    <th>Note</th>
                                    <th>File</th>
                                    <th>Due Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($logs as $activity)
                                    @if($type->id == $activity->activity->type->id)
                                        <tr>
                                            <td>{{$activity->activity->name}}</td>
                                            <td class="text-wrap">{{optional($activity->activity)->note}}</td>
                                            <td>
                                                @if($activity->file)
                                                    <a class="btn btn-sm btn-success" href="/{{$activity->file}}" target="_blank">Download</a>
                                                @else
                                                    No File
                                                @endif
                                            </td>
                                            <td>{{optional($activity->activity)->due_date?:'No due date'}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if($logs->where('activity.type.id', $type->id)->isEmpty())
                                    <tr>
                                        <td colspan="4" class="text-center">No assignments yet.</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
