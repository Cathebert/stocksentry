@extends('layouts.main')
@section('title','Item Orders')
@push('style')
   
@endpush
@section('content')
 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Orders</li>
  </ol>
</nav>
<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong> Orders </strong></h5>
@include("inventory.orders.header")
<br>
        <form method="post" id="form_id">
          @csrf
         <input type="hidden" id="received_orders" value="{{route('orders.received')}}"/>
            <input type="hidden" id="show_orders_details" value="{{route('order.show_received')}}"/>
            <div class="row">
    <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
  <div class="input-group">
  <span class="input-group-text btn btn-secondary">ORDER #:</span>
  <input type="text" aria-label="First name" class="form-control" name="order_number">
  
</div>
  </div>
     <div class="col-md-6 col-sm-12 col-xs-12 form-group" hidden>
  <div class="input-group">
  <span class="input-group-text  btn btn-secondary">Receiving Date Range</span>
  <input type="date" aria-label="First name" class="form-control" name="start_date" id="start_date">
   <span class="input-group-text  btn btn-secondary">-</span>
  <input type="date" aria-label="Last name" class="form-control"  name="end_date" id="end_date" onchange="getDates()">
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
   <div class="col-md-3 col-sm-12 col-xs-12 form-group"  >
   <div class="input-group mb-3">
  <div class="input-group-prepend">
    <button class="btn btn-secondary" type="button">Laboratory</button>
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
  <select class="form-control" id="inputGroupSelect02" style="width: 75%" >
   
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
       <h5 class="card-title"><strong> New Orders  List </strong></h5>
        <div class="table-responsive">
        <table class="table table-sm table-striped" id="received_orders_table"  width="100%">
<thead class="thead-light">
    <tr>
       
     <th width="5%"> #</th>
         <th width="10%">Order #</th>
          <th width="20%">Laboratory</th>
          <th width="30%">Delivery Date</th>
            <th width="20%">Ordered By</th>
          <th width="20%">Received By</th>
            <th width="20%">Approved By</th>
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
     <script src="{{asset('assets/admin/js/inventory/received_orders.js') }}"></script>

  
   
   @endpush