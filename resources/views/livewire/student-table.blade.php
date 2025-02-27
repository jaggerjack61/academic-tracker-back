<div>
    <div class="card">
        <h5 class="card-header">Students</h5>
        <div class="table-responsive text-nowrap">
            <div class="row">
                <div class="col-md-1">
                    <a href="{{route('show-users')}}"  class="btn btn-sm btn-info m-2 text-white" >Add
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
                        <option value="1000">5000</option>
                    </select>

                </div>
                <div class="col-md-2">
                    <input wire:model.live="search" type="text" class="form-control mb-5 me-auto" placeholder="Search" />
                </div>
            </div>
            <table class="table">
                <thead>

                <tr>
                    <th>Name</th>
                    <th>Date of Birth</th>
                    <th>Phone Number</th>

                    <th>Sex</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                @foreach($students as $student)
                    <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{$student->name}}</strong>
                        </td>
                        <td>{{$student->dob}}</td>
                        <td>
                            {{$student->phone_number}}
                        </td>

                        <td>{{$student->sex}}</td>
                        <td><span class="badge bg-label-primary me-1">{{$student->is_active ? 'Active' : 'Inactive'}}</span></td>
                        <td>
                        <span>
                            <a href="{{route('show-users')}}"  class="btn btn-sm btn-primary text-white">Edit</a>
                            <a href="{{route('view-student', ['student' => $student->id])}}" class="btn btn-sm btn-secondary text-white">View</a>
                             @if($student->is_active)
                                <a href="{{route('toggle-user-status',$student->user_id)}}"
                                   class="btn btn-sm btn-danger text-white">Deactivate</a>
                            @else
                                <a href="{{route('toggle-user-status',$student->user_id)}}"
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
    <div class="m-2 p-2 bg-white rounded col-3"> {{$students->count()}} Students</div>
</div>
