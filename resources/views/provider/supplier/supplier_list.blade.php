 @extends('provider.layout.main')
@section('title','Suppliers')
@section('content')
<div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('moderator.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Supplier</a></li>
    <li class="breadcrumb-item active" aria-current="page">All Supplier</li>
  </ol>
</nav>


<div class="row" >
  <div class="col-sm-12">
     <br>
  
      <div class="card">
          
        
          
   

       
		<div class="card-body">
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