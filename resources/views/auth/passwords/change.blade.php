@extends('layouts.base')

@section('title')
    Change Password
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Change Password</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('change-password') }}">
                            @csrf
                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">Old Password</label>

                                <div class="col-md-6">

                                    <input type="password" class="form-control" required name="old_password" autofocus />


                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">New Password</label>

                                <div class="col-md-6">

                                    <input type="password" class="form-control" required name="password" />


                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password" class="col-md-4 col-form-label text-md-end">Confirm Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="confirm_password" required />


                                </div>
                            </div>

                            <div class="row mb-3">
                                <button type="submit" class="btn btn-primary">
                                    Change
                                </button>
                            </div>




                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
