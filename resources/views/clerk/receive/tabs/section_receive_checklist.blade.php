@extends('clerk.layout.main')
@section('title','Received Items Status')
@push('style')
   
@endpush
@section('content')
 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('user.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">All Receipts</li>
  </ol>
</nav>
<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>All Received Status </strong></h5>
@include("clerk.receive.tabs.section_receive_header")
<br>
        <form method="post" id="form_id">
          @csrf
          <input type="hidden" class="form-control" id="post_url" value="{{route('stock.receive')}}">
           <input type="hidden" class="form-control" id="showgrndetails" value="{{route('received.details')}}">
              <input type="hidden" class="form-control" id="item_search" value="{{route('items.search')}}">
               <input type="hidden" class="form-control" id="inventory_received" value="{{route('item.recieved')}}">
            <input type="hidden" id="received_filters" value="{{route('items.filtered')}}"/>
            <input type="hidden" class="form-control" id="loadTable" value="{{route('item.getadded')}}">    
         <input type="hidden" id="received_status" value="{{route('received.status')}}"/>
         <input type="hidden" id="view_received_status_details" value="{{route('received.status_view_details')}}"/>
            <div class="row">
    <div class="col-md-3 col-sm-12 col-xs-12 form-group" hidden>
  <div class="input-group">
  <span class="input-group-text">GNR</span>
  <input type="text" aria-label="First name" class="form-control" name="gnr_start">
   <span class="input-group-text btn btn-secondary">-</span>
  <input type="text" aria-label="Last name" class="form-control"  name="gnr_end" >
</div>
  </div>
  
   <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
   <div class="input-group mb-3">
  <div class="input-group-prepend">
    <button class="btn  btn btn-secondary" type="button">Supplier</button>
  </div>
  <select class="custom-select" id="inputGroupSelect03" aria-label="Supplier"name="supplier" onchange="getSupplier()">
@foreach ($suppliers as $supplier )
      <option value="{{$supplier->id}}">{{$supplier->supplier_name}}</option>
@endforeach
  </select>
</div>
  </div>
   <div class="col-md-3 col-sm-12 col-xs-12 form-group" hidden >
   <div class="input-group mb-3">
  <div class="input-group-prepend">
    <button class="btn btn-outline-secondary" type="button">Inventory Area</button>
  </div>
  <select class="custom-select" id="inputGroupSelect03" aria-label="">

      <option value="1">Store</option>
      <option value="2">Section</option>

  </select>
</div>
  </div>

   <div class="col-md-3 col-sm-12 col-xs-12 form-group" id="req" hidden >
    <label for="exampleInputEmail1">Requisition Number</label>
    <input type="text" class="form-control" id="req_number" >
  </div>

  
     <div class="col-md-3 col-sm-12 col-xs-12 form-group"  id="sup" hidden>
         <label for="exampleInputPassword1">Supplier</label>
                                                 <div class="input-group mb-3">
  <select class="form-control" id="inputGroupSelect02" nstyle="width: 75%" >
   
    <option value=""></option>
   @foreach ($suppliers as $supplier)
       <option value="{{$supplier->id}}">{{$supplier->supplier_name}}</option>
   @endforeach
  
  </select>

</div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $('#inputGroupSelect02').select2({
 placeholder: 'Select  Supplier',
  width: 'resolve',
   
    });
     
});
</script>
  
  <div class="col-md-3 col-sm-12 col-xs-12 form-group" hidden>
    <label for="exampleInputPassword1">Invoice Number</label>
    <input type="text" class="form-control" id="exampleInputPassword1" name="supplier_invoice_number">
  </div>
   <div class="col-md-3 col-sm-12 col-xs-12 form-group" hidden>
    <label for="exampleInputPassword1">GRN Number</label>
    <input type="text" class="form-control" id="grn_number"  >
  </div>
</div>
<hr></br>
<!---table!-->

 
  

</form>

      </div>
    </div>
  </div>



  <div class="col-sm-12">
     <br>
  
      <div class="card">
          
        
          
   

       
		<div class="card-body">
       <h5 class="card-title"><strong>Received Item List </strong></h5>
        <div class="table-responsive">
        <table class="table table-sm table-striped" id="received_items_status"  width="100%">
<thead class="thead-light">
    <tr>
       
 <th width="5%">GRN #</th>
          <th width="20%">Received Date</th>
          <th width="30%">Supplier</th>
         <th width="20%">Checked By</th>
          <th width="20%">Reviewed By</th>
            <th width="20%">Received By</th>
            <th width="20%">Action</th>
           
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
    
        <script src="{{asset('assets/admin/js/inventory/received_status.js') }}"></script>
   
   
   @endpush