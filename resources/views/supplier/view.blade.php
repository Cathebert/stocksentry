@extends('layouts.main')
@section('title', 'View Suppliers')
@section('content')

<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Suppliers</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Suppliers</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            <h4 class="m-0">Suppliers List</h4>
        </div>
        <div class="card-body">
            @php
            $i=1;
            @endphp
            <div class="table-responsive">
                <table class="table  table-striped" id="suppliersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Supplier Name</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Contract Expiry</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Loop through suppliers data to populate the table --}}
                        @foreach($suppliers as $supplier)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $supplier->supplier_name }}</td>
                                <td>{{ $supplier->contact_person }}</td>
                                <td>{{ $supplier->email }}</td>
                                <td>{{ $supplier->phone_number ?: 'Unavailable' }}</td>
                                <td>{{ $supplier->contract_expiry }}</td>
                                <td class="d-flex justify-content-between">
                                    <!-- Edit Button -->
                                    <a href="#" class="btn btn-primary btn-sm mx-1" data-toggle="modal" data-target="#editSupplierModal{{ $supplier->id }}">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>

                                    <!-- Edit Supplier Modal -->
                                    <div class="modal fade" id="editSupplierModal{{ $supplier->id }}" tabindex="-1" role="dialog" aria-labelledby="editSupplierModalLabel{{ $supplier->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editSupplierModalLabel{{ $supplier->id }}">Edit Supplier</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="post" action="{{ route('supplier.update', $supplier->id) }}" id="edit_supplier_form">
                                                        @csrf
                                                        @method('put')
                                    
                                                        <div class="row">
                                                            <div class="col-md-6 form-group">
                                                                <label for="supplier_name">Supplier Name</label>
                                                                <input type="text" class="form-control" id="supplier_name" name="supplier_name" value="{{ $supplier->supplier_name }}">
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="contact_person">Contact Person</label>
                                                                <input type="text" class="form-control" id="contact_person" name="contact_person" value="{{ $supplier->contact_person }}">
                                                            </div>
                                                        </div>
                                    
                                                        <div class="row">
                                                            <div class="col-md-6 form-group">
                                                                <label for="address">Address</label>
                                                                <input type="text" class="form-control" id="address" name="address" value="{{ $supplier->address }}">
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="email">Email</label>
                                                                <input type="email" class="form-control" id="email" name="email" value="{{ $supplier->email }}" >
                                                            </div>
                                                        </div>
                                    
                                                        <div class="row">
                                                            <div class="col-md-6 form-group">
                                                                <label for="phone_number">Phone Number</label>
                                                                <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ $supplier->phone_number }}">
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="contract_expiry">Contract Expiry</label>
                                                                <input type="date" class="form-control" id="contract_expiry" name="contract_expiry" value="{{ $supplier->phone_number }}">
                                                            </div>
                                                        </div>
                                    
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
                                    <button class="btn btn-danger btn-sm mx-1" data-toggle="modal" data-target="#deleteSupplierModal{{ $supplier->id }}">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>

                                    <!-- Delete Supplier Modal -->
                                    <div class="modal fade" id="deleteSupplierModal{{ $supplier->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteSupplierModalLabel{{ $supplier->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteSupplierModalLabel{{ $supplier->id }}">Confirm Deletion</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete {{ $supplier->supplier_name }}?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('supplier.destroy', $supplier->id) }}" method="POST">
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
            $('#suppliersTable').DataTable();
        });
    </script>
@endpush

@endsection
