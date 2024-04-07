@extends('layouts.base')

@section('title')
    Subjects
@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">Subjects</h5>
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
                @foreach($subjects as $subject)
                    <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{$subject->name}}</strong></td>
                        <td>{{$subject->is_active ? 'Active' : 'Inactive'}}</td>
                        <td>
                        <span>
                            @if($subject->is_active)
                                <a href="{{route('toggle-subject-status',$subject->id)}}"
                                   class="btn btn-sm btn-danger text-white">Deactivate</a>
                            @else
                                <a href="{{route('toggle-subject-status',$subject->id)}}"
                                   class="btn btn-sm btn-success text-white">Activate</a>
                            @endif
                            <button type="button" class="btn btn-sm btn-primary text-white" data-bs-toggle="modal" data-bs-target="#editSubjectModal"
                                    onclick="editSubject({{ $subject->id }}, '{{ $subject->name }}')">Edit</button>
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
                <form method="post" action="{{route('create-subject')}}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">New Subject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <input type="text" class="form-control my-2" placeholder="Subject Name" name="name"
                               required/>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Subject Modal -->
    <div class="modal fade" id="editSubjectModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="editSubjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('edit-subject') }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editSubjectModalLabel">Edit Subject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" value="" id="edit-subject-id">
                        <input type="text" class="form-control my-2" placeholder="Subject Name" name="name" id="edit-subject-name"
                               required/>
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


    <!-- Script to populate edit modal with subject data -->
    <script>
        function editSubject(id, name) {
            document.getElementById('edit-subject-id').value = id;
            document.getElementById('edit-subject-name').value = name;
        }
    </script>


@endsection
