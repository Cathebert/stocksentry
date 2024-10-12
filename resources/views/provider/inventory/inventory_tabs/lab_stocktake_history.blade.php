@extends('provider.layout.main')
@section('title','Stock History')
@push('style')
   
@endpush
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('moderator.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{route('lab.bincard_inventory')}}">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Stock Take History</li>
  </ol>
</nav>


<!--content start-->

<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>All Stock Taken</strong></h5>
      
  
<div class="row" >
      <div class="col-sm-12">
    
    <div class="card" hidden>
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong> Stock Taken </strong></h5>
<form method="post" id="consume_form">
          @csrf
        <input type="hidden" class="form-control" id="stock_taken_history_url" value="{{route('stock.history_load')}}">
       <input type="hidden" id="viewStockTakenDetails" value="{{route('stock.view_details')}}"/>
       <input type="hidden" id="approve_stock" value="{{route('stock.approve_stock_taken')}}"/>
            <input type="hidden" id="cancel"     value="{{route('stock.cancel_stock_taken')}}"/>
       
          <div class="row">
    <div class="col-md-4 col-sm-12 col-xs-12 form-group" hidden >
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <label class="input-group-text" for="period">Location</label>
    <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
  </div>
  <select class="custom-select" id="location" name="location" onchange="getSelected()">
    <option value=""selected> All</option>
   
    
    
  </select>
</div>
  </div>
  
     <div class="col-md-6 col-sm-12 col-xs-12 form-group" hidden>
  <div class="input-group">
  <span class="input-group-text">Custom Range:</span> 
  <input type="date" aria-label="First name" class="form-control" id="start_date" name="start_date">
   <span class="input-group-text">-</span>
  <input type="date" aria-label="Last name" class="form-control " id="end_date" name="end_date">
</div>
  </div>
  
</form>

</div>
 
</div>
 
<hr>


</form>

      </div>
    </div>
    </br>
        <!---- table start ---->
               <div class="table-responsive">
        <table class="table table-sm table-hover" id="stock_history" width="100%">
<thead class="thead-light">
    <tr>
       <th scope="col"></th>
       <th scope="col">Stock Date</th>
       <th scope="col">Captured By</th>
       <th scope="col">Supervisor</th>
         <th scope="col">View</th>
        <th scope="col">Action</th>
        
       
      
    </tr>
  </thead>
  <tbody>
</table>
      </div>
       <div class="modal-footer" hidden>
        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="close_modal_n">Close</button>
        <button type="button" class="btn btn-primary" id="update_all"  onclick="updateAll()">Update All</button>
      </div>
      </div>

               <div class="modal" tabindex="-1" id="inforg" role="dialog" >
  <div class="modal-dialog modal-lg" role="document" >
    <div class="modal-content" id="view_item_datails">

          
</div>
    </div>
     </div>
      </div>
            @endsection

    @push('js')
    <script src="{{asset('assets/admin/js/inventory/stock_taken_history.js')}}"> </script>
   
   @endpush