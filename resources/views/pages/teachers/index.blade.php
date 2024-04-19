@extends('layouts.base')

@section('title')
    Teachers
@endsection

@section('content')

    <livewire:teacher-table/>


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
