@extends('layouts.base')

@section('title')
    View Student
@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">Chipo Moyo</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <a class="btn btn-sm btn-info mx-3 text-white" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Add
                    New</a>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                <tr>

                    <td>Form 3 Mathematics</td>
                    <td>Enrolled</td>
                    <td>
                        <span>
                            <a class="btn btn-sm btn-primary text-white">Edit</a>
                            <a class="btn btn-sm btn-secondary text-white" href="{{route('view-activities')}}">View</a>
                            <a class="btn btn-sm btn-danger text-white">Dis-enroll</a>
                        </span>
                    </td>
                </tr>
                <tr>

                    <td>Form 3 Physics</td>
                    <td>Enrolled</td>
                    <td>
                        <span>
                            <a class="btn btn-sm btn-primary text-white">Edit</a>
                            <a class="btn btn-sm btn-secondary text-white" href="{{route('view-activities')}}">View</a>
                            <a class="btn btn-sm btn-danger text-white">Dis-enroll</a>
                        </span>
                    </td>
                </tr>
                <tr>

                    <td>Form 3 English</td>
                    <td>Enrolled</td>
                    <td>
                        <span>
                            <a class="btn btn-sm btn-primary text-white">Edit</a>
                            <a class="btn btn-sm btn-secondary text-white" href="{{route('view-activities')}}">View</a>
                            <a class="btn btn-sm btn-danger text-white">Dis-enroll</a>
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

                        <label for="class">Classes</label>
                        <select class="form-control" required name="class" id="class">
                            <option>Form 2 English</option>
                            <option>Form 3 English</option>
                            <option>Form 4 English</option>
                            <option>Form 5 English</option>
                            <option>Form 2 Mathematics</option>
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
