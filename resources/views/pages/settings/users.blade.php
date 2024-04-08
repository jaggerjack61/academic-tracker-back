@extends('layouts.base')

@section('title')
    Users
@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">Users</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <a class="btn btn-sm btn-info mx-3 text-white" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Add
                    New</a>
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
                        <td>{{$user->userable->dob}}</td>
                        <td>{{$user->userable->sex}}</td>
                        <td>{{$user->userable->id_number}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->userable->phone_number}}</td>
                        <td>{{$user->role->name}}</td>
                        <td>{{$user->userable->is_active ? 'Active' : 'Inactive'}}</td>
                        <td>
                        <span>
                            @if($user->userable->is_active)
                                <a href="{{route('toggle-user-status',$user->id)}}"
                                   class="btn btn-sm btn-danger text-white">Deactivate</a>
                            @else
                                <a href="{{route('toggle-user-status',$user->id)}}"
                                   class="btn btn-sm btn-success text-white">Activate</a>
                            @endif
                            <button type="button" class="btn btn-sm btn-primary text-white" data-bs-toggle="modal" data-bs-target="#editUserModal"
                                    onclick="editUser({{ $user->id }}, '{{ $user->name }}','{{$user->email}}', '{{$user->userable->phone_number}}', '{{$user->userable->id_number}}', '{{$user->userable->sex}}','{{$user->userable->dob}}')">Edit</button>
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


@endsection
