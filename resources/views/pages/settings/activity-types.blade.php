@extends('layouts.base')

@section('title')
    Activity Types
@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">Activity Types</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <a class="btn btn-sm btn-info mx-3 text-white" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Add
                    New</a>
                <tr>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                @foreach($activityTypes as $activityType)
                    <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{$activityType->name}}</strong></td>
                        <td>{{$activityType->start}}</td>
                        <td>{{$activityType->end}}</td>
                        <td>{{$activityType->is_active ? 'Active' : 'Inactive'}}</td>
                        <td>
                        <span>
                            @if($activityType->is_active)
                                <a href="{{route('toggle-activityType-status',$activityType->id)}}"
                                   class="btn btn-sm btn-danger text-white">Deactivate</a>
                            @else
                                <a href="{{route('toggle-activityType-status',$activityType->id)}}"
                                   class="btn btn-sm btn-success text-white">Activate</a>
                            @endif
                        </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>



    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{route('create-activityType')}}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">New Activity Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <input type="text" class="form-control my-2" placeholder="Activity Type Name" name="name"
                               required/>
                        <label for="start">Start Date</label>
                        <input type="date" class="form-control" required name="start" id="start"/>
                        <label for="end">End Date</label>
                        <input type="date" class="form-control" required name="end" id="end"/>
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
