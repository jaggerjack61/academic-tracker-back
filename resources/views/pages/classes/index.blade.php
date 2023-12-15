@extends('layouts.base')

@section('title')
    Classes
@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">Classes</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <a class="btn btn-sm btn-info mx-3 text-white" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Add
                    New</a>
                <tr>
                    <th>Name</th>
                    <th>Assigned Teacher</th>
                    <th>Students</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                <tr>
                    <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Form 3 Mathematics</strong></td>
                    <td>Mathew Chiganze</td>
                    <td>
                        35
                    </td>
                    <td><span class="badge bg-label-primary me-1">Active</span></td>
                    <td>
                        <span>
                            <a class="btn btn-sm btn-primary text-white">Edit</a>
                            <a class="btn btn-sm btn-secondary text-white">View</a>
                            <a class="btn btn-sm btn-danger text-white">Deactivate</a>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Form 4 English Language</strong></td>
                    <td>Shumba Inoruma</td>
                    <td>
                        38
                    </td>
                    <td><span class="badge bg-label-primary me-1">Active</span></td>
                    <td>
                        <span>
                            <a class="btn btn-sm btn-primary text-white">Edit</a>
                            <a class="btn btn-sm btn-secondary text-white">View</a>
                            <a class="btn btn-sm btn-danger text-white">Deactivate</a>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Form 5 English Language</strong></td>
                    <td>Shumba Inoruma</td>
                    <td>
                        43
                    </td>
                    <td><span class="badge bg-label-primary me-1">Active</span></td>
                    <td>
                        <span>
                            <a class="btn btn-sm btn-primary text-white">Edit</a>
                            <a class="btn btn-sm btn-secondary text-white">View</a>
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
                        <h5 class="modal-title" id="staticBackdropLabel">New Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <input type="text" class="form-control my-2" placeholder="Class Name" name="class_name"
                               required/>

                        <label for="teacher">Teacher</label>
                        <select class="form-control" required name="teacher" id="teacher">
                            <option>Mathew Chiganze</option>
                            <option>Shumba Inoruma</option>
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
