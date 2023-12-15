@extends('layouts.base')

@section('title')
    Teachers
@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">Teachers</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <a class="btn btn-sm btn-info mx-3 text-white" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Add
                    New</a>
                <tr>
                    <th>Teacher ID</th>
                    <th>Name</th>
                    <th>Sex</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                <tr>
                    <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>TC230005E</strong></td>
                    <td>Letwin Moyo</td>
                    <td><span class="badge bg-label-primary me-1">Female</span></td>
                    <td>Active</td>
                    <td>
                        <span>
                            <a class="btn btn-sm btn-primary text-white">Edit</a>
                            <a class="btn btn-sm btn-secondary text-white" href="{{route('view-teacher')}}">View</a>
                            <a class="btn btn-sm btn-danger text-white">Deactivate</a>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>TC230005E</strong></td>
                    <td>Shumba Inoruma</td>
                    <td><span class="badge bg-label-primary me-1">Male</span></td>
                    <td>Active</td>
                    <td>
                        <span>
                            <a class="btn btn-sm btn-primary text-white">Edit</a>
                            <a class="btn btn-sm btn-secondary text-white" href="{{route('view-teacher')}}">View</a>
                            <a class="btn btn-sm btn-danger text-white">Deactivate</a>
                        </span>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>



    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">New Teacher</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <input type="text" class="form-control my-2" placeholder="Teacher ID" name="teacher_id"
                               required/>
                        <input type="text" class="form-control my-2" placeholder="Full Name" name="full_name" required/>
                        <label for="sex">Sex</label>
                        <select class="form-control" required name="sex" id="sex">
                            <option>Male</option>
                            <option>Female</option>
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

@endsection
