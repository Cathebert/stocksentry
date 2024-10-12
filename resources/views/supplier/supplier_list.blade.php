@extends('layouts.main')
@section('title','Suppliers')
@section('content')
<div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Supplier</a></li>
    <li class="breadcrumb-item active" aria-current="page">All Supplier</li>
  </ol>
</nav>


<div class="row" >
  <div class="col-sm-12">
     <br>
  
      <div class="card">
          
        
          
   

       
		<div class="card-body">
						<div class="dropdown" style="text-align:right" >
  <button class="dropdown-toggle btn btn-outline-primary btn-sm" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
  <i class="fa fa-share"> Export As</i>
</button>

  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
  
      <a  class="dropdown-item" href="{{route('supplier.download',['type'=>'pdf'])}}"    id="user_pdf" ><i class="fa fa-file-pdf"></i>  PDF File</a>
      <hr>
    <a class="dropdown-item" href="{{route('supplier.download',['type'=>'excel'])}}"  id="user_excel"  ><i class="fa fa-file-excel"></i>  Excel file</a>
    <hr>
   
    
  </div>
  </div>
            <input type="hidden" id="supplier_list_url" value="{{route('supplier.load')}}"/>
       <input type="hidden" id="edit_modal" value="{{route('supplier.edit')}}"/>
       <input type="hidden" id="delete_supplier" value="{{route('supplier.destroy')}}"/>
    
     
            <h5 class="card-title"><strong>User List </strong></h5>
        <div class="table-responsive">
        <table class="table table-sm table-striped" id="suppliersTable"  width="100%">
<thead class="thead-light">
    <tr>
                             <th>ID</th>
                            <th>Supplier Name</th>
                             <th>Address</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Contract Expiry</th>
                            <th>Action</th>
	</tr>
           
  </thead>
</table>
      </div>
       </div>
   
</div>
</div>
</div>
         <div class="modal" tabindex="-1" id="inforg" role="dialog" >
  <div class="modal-dialog modal-lg" role="document" >
    <div class="modal-content" id="receive_item">

    </div>
    </div>
    </div>      

            </div>
          
            @endsection
            @push('js')
       <script src="{{asset('assets/admin/js/supplier/suppliers.js') }}"></script>
  
  
  
@endpush