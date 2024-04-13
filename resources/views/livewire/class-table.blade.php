<div>
    <div class="card">
        <h5 class="card-header">Classes</h5>
        <div class="table-responsive text-nowrap">
            <div class="row">
                <div class="col-md-1">
                    <a class="btn btn-sm btn-info m-2 text-white" data-bs-toggle="modal"
                       data-bs-target="#staticBackdrop">Add
                        New</a>
                </div>
                <div class="col-md-2">
                    <select class="form-select mb-5" wire:model.live="paginate">
                        <option value="">Results per page</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="500">500</option>
                        <option value="1000">1000</option>
                    </select>

                </div>
                <div class="col-md-2">
                    <input wire:model.live="search" type="text" class="form-control mb-5 me-auto" placeholder="Search"/>
                </div>
            </div>
            <table class="table">
                <thead>

                <tr>
                    <th>Name</th>
                    <th>Grade</th>
                    <th>Subject</th>
                    <th>Teacher</th>
                    <th>Students</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                @foreach($classes as $class)
                    <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{$class->name}}</strong>
                        </td>
                        <td>{{$class->grade->name}}</td>
                        <td>
                            {{$class->subject->name}}
                        </td>
                        <td>{{optional($class->teacher)->teacherName()}}</td>
                        <td>{{$class->students->count()}}</td>
                        <td><span
                                class="badge bg-label-primary me-1">{{$class->is_active ? 'Active' : 'Inactive'}}</span>
                        </td>
                        <td>
                        <span>
                            <a class="btn btn-sm btn-primary text-white"
                               onclick="editClass(id='{{$class->id}}', name='{{$class->name}}', teacher='{{optional($class->teacher)->teacherID()}}', grade='{{$class->grade->id}}', subject='{{$class->subject->id}}')"
                               data-bs-toggle="modal" data-bs-target="#editModal">Edit</a>
                            <a href="{{route('view-class',['course' => $class->id])}}" class="btn btn-sm btn-secondary text-white">View</a>
                             @if($class->is_active)
                                <a href="{{route('toggle-class-status',['course' => $class->id])}}"
                                   class="btn btn-sm btn-danger text-white">Deactivate</a>
                            @else
                                <a href="{{route('toggle-class-status',['course' => $class->id])}}"
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
    <div class="m-2 p-2 bg-white rounded col-2"> {{$classes->count()}} Classes</div>

</div>
