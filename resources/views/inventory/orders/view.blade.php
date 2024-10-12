@extends('layouts.main')
@section('title','Inventory')
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
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Item Orders</strong></h5>
@include("inventory.orders.header")
  <div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
      
       
         
                 <form method="post"  id="requsition-form-data" >
      <form method="post" id="form_id">
          @csrf
          <input type="hidden" class="form-control" id="post_url" value="{{route('stock.receive')}}">
           <input type="hidden" class="form-control" id="showgrndetails" value="{{route('received.details')}}">
              <input type="hidden" class="form-control" id="item_search" value="{{route('items.search')}}">
            <input type="hidden" id="received_filters" value="{{route('items.filtered')}}"/>
            <input type="hidden" class="form-control" id="loadTable" value="{{route('item.getadded')}}">    

            <input type="hidden" id="orders" value="{{route('orders.load-new')}}"/>
            <input type="hidden" id="show_orders_details" value="{{route('order.show')}}"/>
            <input type="hidden" id="order_consolidate" value="{{route('order.consolidate')}}"/>
            <input type="hidden" id="marked_delivered" value="{{route('order.mark-received')}}"/>
            <input type="hidden" id="mark_orderconsolidate"   value="{{route('order.mark-consolidate')}}"/> 
        <input type="hidden" id="view_marked_for_consolidation" value="{{route('order.view_marked')}}"/>     
        <input type="hidden" id="view_consolidated" value="{{route('orders.consolidated')}}"/> 
        <input type="hidden" id="save_purchase" value="{{route('order.savepurchase')}}"/> 
    <div class="row" >

     <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
  <div class="input-group">
  <span class="input-group-text btn btn-secondary">ORDER #:</span>
  <input type="text" aria-label="First name" class="form-control" name="order_number">
  
</div>
  </div>
  


   <div class="col-md-3 col-sm-12 col-xs-12 form-group" hidden >
  <div class="input-group mb-3">
  <div class="input-group-prepend">
    <button class="btn  btn btn-secondary" type="button">Laboratory</button>
  </div>
  <select class="custom-select" id="inputGroupSelect03" aria-label="Supplier"name="supplier" onchange="getSupplier()">
@foreach ($suppliers as $supplier )
      <option value="{{$supplier->id}}">{{$supplier->lab_name}}</option>
@endforeach
  </select>
</div>
  </div>

<div class="col-md-3 col-sm-12 col-xs-12 form-group" id="req"  >
    
  </div>
<br>
  <div class="col-md-3 col-sm-12 col-xs-12 form-group" hidden>
  <div class="input-group">
  <span class="input-group-text">Issue Date</span>
  <input type="date" aria-label="First name" class="form-control" name="start_date">
   <span class="input-group-text">-</span>
  <input type="date" aria-label="Last name" class="form-control"  name="end_date">
</div>
  </div>
 


</div>

<hr><br>


</div>
</form>


</div>
   <br>
    <div class="row">
  
 <div class="col-sm-12">
   <div class="card">
  <ul class="nav justify-content-start">
  <li class="nav-item">
    <a class="nav-link active" href="" id="show_consolidated_history"><i class="fa fa-list"></i>  View Consolidated Orders</a>
   
  </li>
  
  
</ul> 
<ul class="nav justify-content-end">
  <li class="nav-item">
    <a class="nav-link active" href=""  id="view_marked"><i class="fa fa-server"></i> Consolidate Marked Orders</a>
    <a class="nav-link active" href="{{route('requisition.export')}}" hidden><i class="fa fa-share-square"></i> Export Expanded List</a>
  </li>
  
  
</ul>
 
    
      <div class="card-body">
      
 
   <br>
 
        <h5 class="card-title"><strong>New Orders  List</strong></h5>
        <div id='real_table'></div>
        <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm table-hover" id="order_items_table">
    <thead class="thead-light">
    <tr>
     <th width="5%"></th>
         <th width="5%">Order #</th>
          <th width="20%">Lab/Section</th>
          <th width="30%">Delivery Date</th>
          <th width="20%">Ordered By</th>
            <th width="20%">Approved By</th>
            <th width="20%">Action</th>
              <th width="20%">Consolidate</th>
    </tr>
  </thead>
  <tbody>
</table>
 </div>
    
   
     </div>
     
     </div>
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
              <script src="{{asset('assets/admin/js/inventory/orders.js') }}"></script>

@endpush