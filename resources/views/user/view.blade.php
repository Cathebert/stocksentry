@extends('layouts.main')
@section('title', 'View Users')
@section('content')

<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Users</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Users</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            <h4 class="m-0 ">Users List</h4>
        </div>
        <div class="card-body">
            @php
            $i=1;

            @endphp
            <div class="table-responsive">
                <table class="table  table-striped" id="usersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Lab Name</th>
                            <th>Lab Location</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Loop through users data to populate the table --}}
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $user->name }} {{ $user->last_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone_number ?: 'Unavailable' }}</td>
                                <td>{{ optional($user->laboratory)->lab_name }}</td>
                                <td>{{ optional($user->laboratory)->lab_location }}</td>
                                <td class="d-flex justify-content-between">
                                    <!-- Edit Button -->
                                    
                                <a href="#" class="btn btn-primary btn-sm mx-1" data-toggle="modal" data-target="#editUserModal{{ $user->id }}">
                                    <i class="fa fa-edit"></i> Edit
                                </a>


                                    <!-- Edit User Modal -->
                                    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">Edit User</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="post" action="{{ route('user.update', $user->id) }}" id="edit_user_form">
                                                        @csrf
                                                        @method('put')

                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <label for="first_name">First Name</label>
                                                                <input type="text" class="form-control" id="first_name" name="name" value="{{ $user->name }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="last_name">Last Name</label>
                                                                <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $user->last_name }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="email">Email</label>
                                                                <input type="text" class="form-control" id="email" name="email" value="{{ $user->email }}">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <label for="phone_number">Phone Number</label>
                                                                <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ $user->phone_number }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="occupation">Position</label>
                                                                <input type="text" class="form-control" id="user_position" name="occupation" value="{{ $user->occupation }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="type">User Role</label>
                                                                <select class="form-control" id="user_role" name="user_type" style="width: 75%">
                                                                    @if(auth()->user()->authority==1)
                                                                        <option value="1" {{ $user->user_type == 1 ? 'selected' : '' }}>Admin</option>
                                                                        <option value="2" {{ $user->user_type == 2 ? 'selected' : '' }}>Lab Manager</option>
                                                                        <option value="4" {{ $user->user_type == 4 ? 'selected' : '' }}>Section Manager</option>
                                                                        <option value="3" {{ $user->user_type == 3 ? 'selected' : '' }}>User</option>
                                                                    @elseif(auth()->user()->authority==2)
                                                                        <option value="4" {{ $user->user_type == 4 ? 'selected' : '' }}>Section Manager</option>
                                                                        <option value="3" {{ $user->user_type == 3 ? 'selected' : '' }}>User</option>
                                                                    @elseif(auth()->user()->authority==4)
                                                                        <option value="3" {{ $user->user_type == 3 ? 'selected' : '' }}>User</option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>

                                                        @if(auth()->user()->authority==1)
                                                            <div class="row">
                                                                <div class="col-md-4 form-group" id="labs">
                                                                    <label for="laboratory_id">Laboratory</label>
                                                                    <select class="form-control" id="lab_id" name="lab_id" style="width: 75%" onchange="getSections(this.value)">
                                                                        @foreach ($labs as $lab)
                                                                            <option value="{{ $lab->id }}" {{ $user->lab_id == $lab->id ? 'selected' : '' }}>{{ $lab->lab_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4 form-group" id="req"></div>
                                                            </div>
                                                        @endif

                                                        @if(auth()->user()->authority == 2 && $has_section= "yes")
                                                            <div class="row">
                                                                <div class="col-md-4 form-group">
                                                                    <label for="laboratory_id">Laboratory</label>
                                                                    <select class="form-control" id="lab_id" name="lab_id" style="width: 75%" readonly>
                                                                        <option value="{{ $lab_details->id }}" selected>{{ $lab_details->lab_name }}</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-4 form-group">
                                                                    <label for="inputsection">Section</label>
                                                                    <select class="form-control" id="inputsection" name="section_id" style="width: 75%">
                                                                        @foreach ($sections as $section)
                                                                            <option value="{{ $section->id }}" {{ $user->section_id == $section->id ? 'selected' : '' }}>{{ $section->section_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

            
                                    
                                        <!-- Delete Button -->
                                        <button class="btn btn-danger btn-sm mx-1" data-toggle="modal" data-target="#deleteUserModal{{ $user->id }}">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    
                                    

                                    <!-- Delete User Modal -->
                                    <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel{{ $user->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteUserModalLabel{{ $user->id }}">Confirm Deletion</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete {{ $user->name }} {{ $user->last_name }}?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('user.destroy', $user->id) }}" method="POST">
                                                        @csrf
                                                        @method('post')
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                            @php
                                $i++;
                            @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</div>

<!-- DataTables JS -->
@push('js')
    <script>
        $(document).ready(function () {
            $('#usersTable').DataTable();
        });
    </script>
@endpush

@endsection
