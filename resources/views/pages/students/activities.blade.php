@extends('layouts.base')

@section('title')
    Activites
@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">Activities</h5>
        <nav>
            <div class="nav nav-tabs flex-row" id="nav-tab" role="tablist">

                    <button class="btn btn-sm btn-primary mx-1 ms-3 active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                            type="button" role="tab" aria-controls="nav-home" aria-selected="true">Attendance
                    </button>
                    <button class="btn btn-sm btn-primary mx-1" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                            type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Classwork
                    </button>
                    <button class="btn btn-sm btn-primary mx-1" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact"
                            type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Homework
                    </button>
                <button class="btn btn-sm btn-primary mx-1" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-tests"
                        type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Tests
                </button>

            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">

{{--            Attendance--}}
            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">

                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>

                        </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        <tr>
                            <td>01/01/2023</td>
                            <td><span class="badge bg-label-primary me-1">Present</span></td>
                            <td>
                        <span>
                            <a class="btn btn-sm btn-success text-white">Mark Present</a>
                            <a class="btn btn-sm btn-danger text-white">Mark Absent</a>
                        </span>
                            </td>
                        </tr>
                        <tr>
                            <td>02/01/2023</td>
                            <td><span class="badge bg-label-primary me-1">Present</span></td>
                            <td>
                        <span>
                            <a class="btn btn-sm btn-success text-white">Mark Present</a>
                            <a class="btn btn-sm btn-danger text-white">Mark Absent</a>
                        </span>
                            </td>
                        </tr>
                        <tr>
                            <td>03/01/2023</td>
                            <td><span class="badge bg-label-danger me-1">Absent</span></td>
                            <td>
                        <span>
                            <a class="btn btn-sm btn-success text-white">Mark Present</a>
                            <a class="btn btn-sm btn-danger text-white">Mark Absent</a>
                        </span>
                            </td>
                        </tr>
                        <tr>
                            <td>04/01/2023</td>
                            <td><span class="badge bg-label-primary me-1">Present</span></td>
                            <td>
                        <span>
                            <a class="btn btn-sm btn-success text-white">Mark Present</a>
                            <a class="btn btn-sm btn-danger text-white">Mark Absent</a>
                        </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

{{--            Classwork--}}
            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Mark</th>
                            <th>Action</th>

                        </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        <tr>
                            <td>01/01/2023</td>
                            <td>Differential Equations Exercise</td>
                            <td><span class="badge bg-label-primary me-1">15/25</span></td>
                            <td>
                        <span>
                            <a class="btn btn-sm btn-primary text-white">Edit</a>
                        </span>
                            </td>
                        </tr>
                        <tr>
                            <td>04/01/2023</td>
                            <td>Integral Equations Exercise</td>
                            <td><span class="badge bg-label-danger me-1">11/25</span></td>
                            <td>
                        <span>
                            <a class="btn btn-sm btn-primary text-white">Edit</a>
                        </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>

{{--            Homework--}}
            <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Mark</th>
                            <th>Action</th>

                        </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        <tr>
                            <td>02/01/2023</td>
                            <td>Differentiation</td>
                            <td><span class="badge bg-label-primary me-1">19/20</span></td>
                            <td>
                        <span>
                            <a class="btn btn-sm btn-primary text-white">Edit</a>
                        </span>
                            </td>
                        </tr>
                        <tr>
                            <td>03/01/2023</td>
                            <td>Integration</td>
                            <td><span class="badge bg-label-danger me-1">09/20</span></td>
                            <td>
                        <span>
                            <a class="btn btn-sm btn-primary text-white">Edit</a>
                        </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>

{{--            Tests--}}
            <div class="tab-pane fade" id="nav-tests" role="tabpanel" aria-labelledby="nav-tests-tab">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Mark</th>
                            <th>Action</th>

                        </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        <tr>
                            <td>02/01/2023</td>
                            <td>Differentiation & Integration Test 1</td>
                            <td><span class="badge bg-label-primary me-1">37/50</span></td>
                            <td>
                        <span>
                            <a class="btn btn-sm btn-primary text-white">Edit</a>
                        </span>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </div>

            </div>
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
