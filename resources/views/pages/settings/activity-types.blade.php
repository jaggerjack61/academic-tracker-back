@extends('layouts.base')

@section('title')
    Activity Types
@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">Activity Types</h5>
        <div class="table-responsive text-wrap">
            <a class="btn btn-sm btn-info mx-3 text-white" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Add
                New</a>
            <table class="table">
                <thead>

                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Image Icon</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                @foreach($activityTypes as $activityType)
                    <tr>
                        <td  class="text-nowrap"><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{$activityType->name}}</strong></td>
                        <td>{{$activityType->description}}</td>
                        <td class="text-nowrap">{{$activityType->type =='boolean' ? 'binary' : ''}}{{$activityType->type =='value' ? 'score' : ''}}{{$activityType->type =='static' ? 'static' : ''}}
                            {{$activityType->type =='boolean' ?$activityType->true_value.'|'.$activityType->false_value :''}}
                            </td>
                            <td><img class="rounded" src="/{{$activityType->image}}" style="width:50px" alt="icon"/></td>
                        <td>{{$activityType->is_active ? 'Active' : 'Inactive'}}</td>
                        <td class="text-nowrap">
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
                <form method="post" action="{{route('create-activity-type')}}" enctype="multipart/form-data">
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
                        <select id="inputType" class="form-control my-2" name="type" required>
                            <option value="">Select Activity Type</option>
                            <option value="boolean">Binary</option>
                            <option value="value">Score</option>
                            <option value="static">Static</option>
                        </select>
                        <div id="inputContainer"></div>
                        <label>Image</label>
                        <input type="file" class="form-control my-2" name="file" />
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
                <form method="post" action="{{ route('edit-activity-type') }}"  enctype="multipart/form-data">
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
{{--                        <select id="inputType1" class="form-control my-2" name="type" id="edit-activityType-type" required>--}}
{{--                            <option value="">Select Activity Type</option>--}}
{{--                            <option value="boolean">Binary</option>--}}
{{--                            <option value="value">Score</option>--}}
{{--                            <option value="static">Static</option>--}}
{{--                        </select>--}}
{{--                        <div id="inputContainer1"></div>--}}
                        <label>Image</label>
                        <input type="file" class="form-control my-2" name="file" />
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

        const inputTypeSelect = document.getElementById('inputType');
        const inputContainer = document.getElementById('inputContainer');

        inputTypeSelect.addEventListener('change', function() {
            const selectedType = this.value;
            inputContainer.innerHTML = ''; // Clear previous input

            let inputElement;
            let inputElement1;
            if (selectedType === 'boolean') {
                inputElement = document.createElement('input');
                inputElement.type = 'text';
                inputElement.placeholder = 'Positive or True Value';
                inputElement.classList.add("form-control");
                inputElement.classList.add("my-2");
                inputElement.name = 'true_value';
                inputElement.required = true;
                inputElement1 = document.createElement('input');
                inputElement1.type = 'text';
                inputElement1.placeholder = 'Negative or False Value';
                inputElement1.classList.add("form-control");
                inputElement1.classList.add("my-2");
                inputElement1.name = 'false_value';
                inputElement1.required = true;

            }

            if (inputElement && inputElement1) {
                inputContainer.appendChild(inputElement);
                inputContainer.appendChild(inputElement1);
            }
        });


        // const inputTypeSelect1 = document.getElementById('inputType1');
        // const inputContainer1= document.getElementById('inputContainer1');
        //
        // inputTypeSelect1.addEventListener('change', function() {
        //     const selectedType = this.value;
        //     inputContainer1.innerHTML = ''; // Clear previous input
        //
        //     let inputElement3;
        //     let inputElement4;
        //     if (selectedType === 'boolean') {
        //         inputElement3 = document.createElement('input');
        //         inputElement3.type = 'text';
        //         inputElement3.placeholder = 'Positive or True Value';
        //         inputElement3.classList.add("form-control");
        //         inputElement3.classList.add("my-2");
        //         inputElement3.name = 'true_value';
        //         inputElement3.required = true;
        //         inputElement4 = document.createElement('input');
        //         inputElement4.type = 'text';
        //         inputElement4.placeholder = 'Negative or False Value';
        //         inputElement4.classList.add("form-control");
        //         inputElement4.classList.add("my-2");
        //         inputElement4.name = 'false_value';
        //         inputElement4.required = true;
        //
        //     }
        //
        //     if (inputElement3 && inputElement4) {
        //         inputContainer1.appendChild(inputElement3);
        //         inputContainer1.appendChild(inputElement4);
        //     }
        // });
    </script>


@endsection
