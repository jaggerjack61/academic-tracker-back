<div>
    <div class="card">
        <h5 class="card-header">Teachers</h5>
        <div class="table-responsive text-wrap">
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
                    <th>Phone</th>
                    <th>Sex</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                @foreach($teachers as $teacher)
                    <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{$teacher->name}}</strong></td>
                        <td>{{$teacher->phone_number}}</td>
                        <td><span class="badge bg-label-primary me-1">{{$teacher->sex}}</span></td>
                        <td><span class="badge bg-label-primary me-1">{{$teacher->is_active ? 'Active' : 'Inactive'}}</span></td>
                        <td>
                        <span>
                            <a class="btn btn-sm btn-secondary text-white" href="{{route('view-teacher', ['teacher' => $teacher->id])}}">View</a>
                            @if($teacher->is_active)
                                <a href="{{route('toggle-user-status',$teacher->user_id)}}"
                                   class="btn btn-sm btn-danger text-white">Deactivate</a>
                            @else
                                <a href="{{route('toggle-user-status',$teacher->user_id)}}"
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
    <div class="card m-2">
        <div class="row">
            .
        </div>
    </div>

</div>
