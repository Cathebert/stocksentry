@extends('provider.layout.main')
@section('title','Add Supplier')
@section('content')


 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('moderator.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Suppliers</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add</li>
  </ol>
</nav>
  
<div class="row" >
  <div class="col-sm-12">
     <br>
  
      <div class="card">
          
        
          
   

       
		<div class="card-body">
            <input type="hidden" id="lab_list_url" value="{{route('lab.load')}}"/>
       <input type="hidden" id="edit_modal" value="{{route('lad.edit')}}"/>
            <h5 class="card-title"><strong>Laboratory List </strong></h5>
        <div class="table-responsive">
        <table class="table table-sm table-striped" id="lab_list"  width="100%">
<thead class="thead-light">
    <tr>
       
         <th width="5%"></th>
         <th width="5%">Lab Name</th>
          <th width="20%">Lab Location</th>
          <th width="30%">Email</th>
          <th width="20%">Phone Number</th>
           <th width="20%">Address</th>
            <th width="20%">Action</th>
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
       <script src="{{asset('assets/admin/js/supplier/add_supplier.js') }}"></script>
  
  
  
@endpush