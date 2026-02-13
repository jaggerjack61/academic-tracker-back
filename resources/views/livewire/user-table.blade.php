<div>
    <div class="card">
        <h5 class="card-header">Users</h5>
        <div class="table-responsive text-nowrap">
            <div class="row">
                <div class="col-md-1">
                    <a class="btn btn-sm btn-info m-2 text-white" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Add
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
                    <th>Sex</th>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                @foreach($users as $user)
                    <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{$user->name}}</strong></td>
                        <td>{{optional($user->profile)->dob}}</td>
                        <td>{{optional($user->profile)->sex}}</td>
                        <td>{{optional($user->profile)->id_number}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{optional($user->profile)->phone_number}}</td>
                        <td>{{$user->role->name}}</td>
                        <td>{{optional($user->profile)->is_active ? 'Active' : 'Inactive'}}</td>
                        <td>
                        <span>
                            @if(optional($user->profile)->is_active)
                                <a href="{{route('toggle-user-status',$user->id)}}"
                                   class="btn btn-sm btn-danger text-white">Deactivate</a>
                            @else
                                <a href="{{route('toggle-user-status',$user->id)}}"
                                   class="btn btn-sm btn-success text-white">Activate</a>
                            @endif
                            <button type="button" class="btn btn-sm btn-primary text-white" data-bs-toggle="modal" data-bs-target="#editUserModal"
                                    onclick="editUser({{ $user->id }}, '{{ $user->name }}','{{$user->email}}', '{{optional($user->profile)->phone_number}}', '{{optional($user->profile)->id_number}}', '{{optional($user->profile)->sex}}','{{optional($user->profile)->dob}}')">Edit</button>
                        </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="m-2 p-2 bg-white rounded col-3"> {{$users->count()}} Users</div>



    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{route('create-user')}}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <input type="text" class="form-control my-2" placeholder="Full Name" name="name"
                               required/>
                        <input type="text" class="form-control my-2" placeholder="Email" name="email"
                               required/>
                        <input type="text" class="form-control my-2" placeholder="Phone Number" name="phone_number"
                               required/>
                        <input type="text" class="form-control my-2" placeholder="Identification Number" name="id_number"
                               required/>
                        <select name="sex" class="form-control my-2" required>
                            <option value="">Select sex</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                        <label for="dob">Date of Birth</label>
                        <input type="date" class="form-control my-2" required name="dob" id="dob"/>
                        <select name="role_id" class="form-control my-2" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{$role->id}}">{{$role->name}}</option>
                            @endforeach
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

    <div class="modal fade" id="editUserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('edit-user') }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" value="" id="edit-user-id">
                        <input type="text" class="form-control my-2" placeholder="Full Name" name="name" id="edit-user-name"
                               required/>
                        <input type="text" class="form-control my-2" placeholder="Email" name="email" id="edit-user-email"
                               required/>
                        <input type="text" class="form-control my-2" placeholder="Phone Number" name="phone_number" id="edit-user-phone_number"
                               required/>
                        <input type="text" class="form-control my-2" placeholder="Identification Number" name="id_number" id="edit-user-id_number"
                               required/>
                        <select name="sex" class="form-control my-2" required id="edit-user-sex">
                            <option value="">Select sex</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                        <label for="dob">Date of Birth</label>
                        <input type="date" class="form-control my-2" required name="dob" id="edit-user-dob"/>
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


    <!-- Script to populate edit modal with user data -->
    <script>
        function editUser(id, name, email, phone_number, id_number, sex, dob) {
            document.getElementById('edit-user-id').value = id;
            document.getElementById('edit-user-name').value = name;
            document.getElementById('edit-user-email').value = email;
            document.getElementById('edit-user-phone_number').value = phone_number;
            document.getElementById('edit-user-id_number').value = id_number;
            document.getElementById('edit-user-sex').value = sex;
            document.getElementById('edit-user-dob').value = dob;
        }
    </script>


</div>
