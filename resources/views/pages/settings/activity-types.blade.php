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
                    <th>Description</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                @foreach($activityTypes as $activityType)
                    <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{$activityType->name}}</strong></td>
                        <td>{{$activityType->description}}</td>
                        <td>{{$activityType->type =='boolean' ? 'Binary' : 'Score'}}</td>
                        <td>{{$activityType->is_active ? 'Active' : 'Inactive'}}</td>
                        <td>
                        <span>
                            @if($activityType->is_active)
                                <a href="{{route('toggle-activity-type-status',$activityType->id)}}"
                                   class="btn btn-sm btn-danger text-white">Deactivate</a>
                            @else
                                <a href="{{route('toggle-activity-type-status',$activityType->id)}}"
                                   class="btn btn-sm btn-success text-white">Activate</a>
                            @endif
                            <button type="button" class="btn btn-sm btn-primary text-white" data-bs-toggle="modal" data-bs-target="#editActivityTypeModal"
                                    onclick="editActivityType({{ $activityType->id }}, '{{ $activityType->name }}', '{{$activityType->description}}','{{$activityType->type}}')">Edit</button>
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
                <form method="post" action="{{route('create-activity-type')}}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">New Activity Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <input type="text" class="form-control my-2" placeholder="Activity Type Name" name="name"
                               required/>
                        <input type="text" class="form-control my-2" placeholder="Activity type description" name="description"
                               required/>
                        <select class="form-control my-2" name="type" required>
                            <option value="">Select Activity Type</option>
                            <option value="boolean">Binary</option>
                            <option value="value">Score</option>
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

    <div class="modal fade" id="editActivityTypeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="editActivityTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('edit-activity-type') }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editActivityTypeModalLabel">Edit ActivityType</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" value="" id="edit-activityType-id">
                        <input type="text" class="form-control my-2" placeholder="Activity type name" name="name" id="edit-activityType-name"
                               required/>
                        <input type="text" class="form-control my-2" placeholder="Activity type description" name="description" id="edit-activityType-description"
                               required/>
                        <select class="form-control my-2" name="type" id="edit-activityType-type" required>
                            <option value="">Select Activity Type</option>
                            <option value="boolean">Binary</option>
                            <option value="value">Score</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Button to trigger edit modal -->


    <!-- Script to populate edit modal with activityType data -->
    <script>
        function editActivityType(id, name, description, type) {
            document.getElementById('edit-activityType-id').value = id;
            document.getElementById('edit-activityType-name').value = name;
            document.getElementById('edit-activityType-description').value = description;
            document.getElementById('edit-activityType-type').value = type;
        }
    </script>


@endsection
