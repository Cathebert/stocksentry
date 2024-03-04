 @extends('layouts.main')
@section('title','Received Items')
@push('style')
   
@endpush
@section('content')
 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">All Receipts</li>
  </ol>
</nav>
<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>All Received Status </strong></h5>
@include("inventory.receive_tabs.header")
<br>
        <form method="post" id="form_id">
          @csrf
          <input type="hidden" class="form-control" id="post_url" value="{{route('stock.receive')}}">
           <input type="hidden" class="form-control" id="showgrndetails" value="{{route('received.details')}}">
              <input type="hidden" class="form-control" id="item_search" value="{{route('items.search')}}">
               <input type="hidden" class="form-control" id="inventory_received" value="{{route('item.recieved')}}">
            <input type="hidden" id="received_filters" value="{{route('items.filtered')}}"/>
            <input type="hidden" class="form-control" id="loadTable" value="{{route('item.getadded')}}">    
         
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
       
    
        <th width="5%">ULN #</th>
        <th width="10%">Item Name</th>
          <th width="4%">Code #</th>
          <th width="4%">Category #</th>
         <th width="4%">Batch #</th>
         <th width="6%">Supplied By </th>
         <th width="4%">Pack Size</th>
        <th width="10%">Any Expired </th>
        <th width="10%">Received at correct Temp.</th>
        <th width="10%">Suitable for use? (Y/N)</th>
			</tr>
           
  </thead>
     <tbody>
        @foreach ( $items as $item)
              <td> {{$item->uln }}  </td>
        <td>{{$item->item_name  }}   </td>
        <td> {{$item->code  }}  </td> 
        <td>{{$item->catalog_number}}   </td>
        <td>{{$item->batch_number}}   </td>
          <td>{{$item->supplier_name}}   </td>
        <td> {{$item->warehouse_size}}  </td>
        <td>{{$item->any_expired}}   </td>
        <td>{{$item->correct_temp}}   </td>
        <td> {{$item->suitable_for_use}}  </td>  
        @endforeach
    

</tbody>
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
       <script>
        $(document).ready(function () {
            $('#received_items_status').DataTable();
        });
    </script>
   
   @endpush