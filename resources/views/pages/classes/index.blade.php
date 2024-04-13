@extends('layouts.base')

@section('title')
    Classes
@endsection

@section('content')
    <script src="/custom/js/jquery-3.7.1.min.js"></script>
    <link href="/custom/css/select2.min.css" rel="stylesheet"/>
    <script src="/custom/js/select2.min.js"></script>

    <livewire:class-table/>





    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('create-class')}}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">New Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <input type="text" class="form-control my-2" placeholder="Class Name" name="name"
                               required/>

                        <label for="teacher">Teacher</label>
                        <select style="width: 100%" name="teacher_id" id="teacher">
                            <option value="">Select a teacher</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                        <label for="grade">Grade</label>
                        <select class="form-control" required name="grade_id" id="grade">
                            <option value="">Select a grade</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                            @endforeach
                        </select>
                        <label for="subject">Subject</label>
                        <select class="form-control" required name="subject_id" id="subject">
                            <option value="">Select a subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
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

    <div class="modal fade" id="editModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('edit-class')}}" method="post">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Edit Class</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit-id" name="id"/>
                        <label for="edit-name">Name</label>
                        <input type="text" id="edit-name" class="form-control my-2" placeholder="Class Name" name="name"
                               required/>

                        <label for="edit-teacher">Teacher</label>
                        <select style="width: 100%" name="teacher_id" id="edit-teacher">
                            <option value="">Select a teacher</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                        <label for="edit-grade">Grade</label>
                        <select class="form-control" required name="grade_id" id="edit-grade">
                            <option value="">Select a grade</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                            @endforeach
                        </select>
                        <label for="edit-subject">Subject</label>
                        <select class="form-control" required name="subject_id" id="edit-subject">
                            <option value="">Select a subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
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

    <script>
        $('#teacher').select2({
            dropdownParent: $('#staticBackdrop'),
            containerCssClass: 'big-container',
        });
        $('#edit-teacher').select2({
            dropdownParent: $('#editModal'),
            containerCssClass: 'big-container',
        });
        function editClass(id, name, teacher, grade, subject) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-teacher').value = teacher;
            document.getElementById('edit-grade').value = grade;
            document.getElementById('edit-subject').value = subject;

        }
    </script>

@endsection
