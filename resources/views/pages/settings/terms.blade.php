@extends('layouts.base')

@section('title')
    Terms
@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">Terms</h5>
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
                <tr>
                    <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Term 1 2023</strong></td>
                    <td>01/01/2023</td>
                    <td>01/04/2023</td>
                    <td>Active</td>
                    <td>
                        <span>
                            <a class="btn btn-sm btn-primary text-white">Edit</a>
                            <a class="btn btn-sm btn-danger text-white">Deactivate</a>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Term 2 2023</strong></td>
                    <td>01/04/2023</td>
                    <td>01/07/2023</td>
                    <td>Active</td>
                    <td>
                        <span>
                            <a class="btn btn-sm btn-primary text-white">Edit</a>
                            <a class="btn btn-sm btn-danger text-white">Deactivate</a>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Term 3 2023</strong></td>
                    <td>01/08/2023</td>
                    <td>01/12/2023</td>
                    <td>Active</td>
                    <td>
                        <span>
                            <a class="btn btn-sm btn-primary text-white">Edit</a>
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
                        <h5 class="modal-title" id="staticBackdropLabel">New Term</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <input type="text" class="form-control my-2" placeholder="Term Name" name="name"
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
