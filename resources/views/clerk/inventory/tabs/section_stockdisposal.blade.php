@extends('clerk.layout.main')
@section('title','Inventory')
@push('style')
   
@endpush
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
     <li class="breadcrumb-item"><a href="{{route('user.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{route('section.bincard_inventory')}}">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Disposal</li>
  </ol>
</nav>


<!--content start-->

<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Disposals</strong></h5>
      
   @include('clerk.inventory.tabs.section_inventory_header')  
<div class="row" >
      <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Disposals </strong></h5>
<form method="post" id="consume_form">
          @csrf
          <input type="hidden" class="form-control" id="disposal_url" value="{{route('inventory.disposal_show')}}">
           <input type="hidden" class="form-control" id="disposal_selected" value="{{route('inventory.selected_dispose')}}"/>
           <input type="hidden" class="form-control" id="run_disposal" value="{{route('inventory.run-disposal')}}"/>
            <input type="hidden" id="check_quantity" value="{{route('inventory.check_quantity')}}"/>
            <input type="hidden" id="modal_dispose_url" value="{{route('disposal.list')}}"/>
            <input type="hidden" id="items_disposal_list" value="{{route('disposal.load')}}"/>
            <input type="hidden" id="approve_disposal"  value="{{route('disposal.approve')}}"/>
             <input type="hidden" id="deny_disposal"  value="{{route('disposal.cancel')}}"/>
 
            
            <div class="row">

  
     <div class="col-md-6 col-sm-12 col-xs-12 form-group" >
  <div class="input-group">
  <span class="input-group-text btn btn-secondary">Date:</span> 
  <input type="date" aria-label="First name" class="form-control" id="start_date" name="start_date" value="{{date('Y-m-d')}}" disabled>
   
</div>
  </div>
  

   <div class="col-md-4 col-sm-12 col-xs-12 form-group"  hidden>
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <label class="input-group-text" for="period">Disposed By</label>
    <span class="input-group-text"><i class="fa fa-chain-broken" aria-hidden="true"></i></span>
  </div>
  <select class="custom-select" id="period" name="period" onchange="getSelected()">
    <option value=""selected> Select Period</option>
    <option value="1" >Today</option>
    <option value="2">This Week</option>
    <option value="3">This Month</option>
  </select>
</div>
  </div>
</form>

</div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary"  id="add_new"><i class="fa fa-plus" aria-hidden="true"></i> Select Items to Dispose</button>
        <button type="button" class="btn btn-danger" onclick="showDisposedListModal()"><i class="fa fa-trash" aria-hidden="true" ></i> Disposed Items</button>
      </div>
</div>
 
<hr></br>


</form>

      </div>
    </div>
        <!---- table start ---->
               <div class="table-responsive">
        <table class="table table-sm" id="update_disposals" width="100%">
<thead class="thead-light">
    <tr>
       <th scope="col">#</th>
     <th scope="col">Item Name</th>
       <th scope="col">Code</th>
         <th scope="col">Batch Number</th>
        <th scope="col">Catalog #</th>
        <th scope="col">UOM </th>
        <th scope="col">Quantity Available</th>
        <th scope="col">Reason</th>
         <th scope="col">Quantity To Dispose</th>
          
  
      
      
    </tr>
  </thead>
  <tbody>
</table>
      </div>
 
      </div>
      <br>
     
 <div class="modal-footer">
        <button type="button" id="dispose" class="btn btn-primary" onclick="RunItemsDisposal()" hidden>Dispose Items</button>
       
      </div>

<div class="modal" tabindex="-1" id="inforg" role="dialog" >
  <div class="modal-dialog modal-xl" role="document" >
    <div class="modal-content" id="view_item_datails">

</div>
</div>
    </div>
     
    
            @endsection

    @push('js')
        <script src="{{asset('assets/admin/js/inventory/disposal_inventory.js')}}"> </script>
   @endpush