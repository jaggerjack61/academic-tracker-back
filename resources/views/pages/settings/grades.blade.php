@extends('layouts.base')

@section('title')
    Grades
@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">Grades</h5>
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
                @foreach($grades as $grade)
                    <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{$grade->name}}</strong></td>
                        <td>{{$grade->is_active ? 'Active' : 'Inactive'}}</td>
                        <td>
                        <span>
                            @if($grade->is_active)
                                <a href="{{route('toggle-grade-status',$grade->id)}}"
                                   class="btn btn-sm btn-danger text-white">Deactivate</a>
                            @else
                                <a href="{{route('toggle-grade-status',$grade->id)}}"
                                   class="btn btn-sm btn-success text-white">Activate</a>
                            @endif
                            <button type="button" class="btn btn-sm btn-primary text-white" data-bs-toggle="modal" data-bs-target="#editGradeModal"
                                    onclick="editGrade({{ $grade->id }}, '{{ $grade->name }}')">Edit</button>
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
                <form method="post" action="{{route('create-grade')}}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">New Grade</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                            <input type="text" class="form-control my-2" placeholder="Grade Name" name="name"
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

    <div class="modal fade" id="editGradeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="editGradeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('edit-grade') }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editGradeModalLabel">Edit Grade</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" value="" id="edit-grade-id">
                        <input type="text" class="form-control my-2" placeholder="Grade Name" name="name" id="edit-grade-name"
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


    <!-- Script to populate edit modal with grade data -->
    <script>
        function editGrade(id, name) {
            document.getElementById('edit-grade-id').value = id;
            document.getElementById('edit-grade-name').value = name;
        }
    </script>


@endsection
