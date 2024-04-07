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
                @foreach($terms as $term)
                    <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{$term->name}}</strong></td>
                        <td>{{$term->start}}</td>
                        <td>{{$term->end}}</td>
                        <td>{{$term->is_active ? 'Active' : 'Inactive'}}</td>
                        <td>
                        <span>
                            @if($term->is_active)
                                <a href="{{route('toggle-term-status',$term->id)}}"
                                   class="btn btn-sm btn-danger text-white">Deactivate</a>
                            @else
                                <a href="{{route('toggle-term-status',$term->id)}}"
                                   class="btn btn-sm btn-success text-white">Activate</a>
                            @endif
                            <button type="button" class="btn btn-sm btn-primary text-white" data-bs-toggle="modal" data-bs-target="#editTermModal"
                                    onclick="editTerm({{ $term->id }}, '{{ $term->name }}','{{ $term->start }}','{{ $term->end }}')">Edit</button>
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
                <form method="post" action="{{route('create-term')}}">
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

    <div class="modal fade" id="editTermModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="editTermModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('edit-term') }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTermModalLabel">Edit Term</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" value="" id="edit-term-id">
                        <input type="text" class="form-control my-2" placeholder="Term Name" name="name" id="edit-term-name"
                               required/>
                        <label for="start">Start Date</label>
                        <input type="date" class="form-control" required name="start" id="edit-term-start"/>
                        <label for="end">End Date</label>
                        <input type="date" class="form-control" required name="end" id="edit-term-end"/>
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


    <!-- Script to populate edit modal with term data -->
    <script>
        function editTerm(id, name, start, end) {
            document.getElementById('edit-term-id').value = id;
            document.getElementById('edit-term-name').value = name;
            document.getElementById('edit-term-start').value = start;
            document.getElementById('edit-term-end').value = end;
        }
    </script>


@endsection
