 @extends('layouts.main')
@section('title','Inventory')
@push('style')
   
@endpush
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{route('inventory.bincard')}}">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">All Inventory</li>
  </ol>
</nav>


<!--content start-->

<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>All Inventory</strong></h5>
      
  
<div class="row" >
      <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong> Inventory By Location </strong></h5>
<form method="post" id="consume_form">
          @csrf
          <input type="hidden" class="form-control" id="inventory_update_all" value="{{route('inventory.update_all')}}">
          <input type="hidden" class="form-control" id="update_selected" value="{{route('inventory.selected_update')}}">
          <input type="hidden" class="form-control" id="item_search" value="{{route('items.search')}}">
          <input type="hidden" class="form-control" id="inventory_received" value="{{route('item.recieved')}}">
  
          <input type="hidden" class="form-control" id="load_inventory" value="{{route('inventory.load_all')}}"> 
          <input type="hidden" id="more_details" value="{{route('inventory.more')}}"/>
          <input type="hidden" id="about" value="{{route('inventory.lab_inventory')}}" />
          <input type="hidden" id="location_inventory" value="{{route('inventory.bylocation')}}"/>
          <input type="hidden" id="back" value="{{ route('inventory.all') }}" />
          <div class="row">
    <div class="col-md-4 col-sm-12 col-xs-12 form-group"  >
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <label class="input-group-text" for="period">Location</label>
    <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
  </div>
  <select class="custom-select" id="location" name="location" onchange="getSelected(this.value)">
    <option value="99"selected> All</option>
    @foreach ($labs as $lab)
      <option value="{{$lab->id}}" >{{$lab->lab_name}}</option>
    @endforeach
    
    
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
        <table class="table table-bordered table-striped table-sm table-hover" id="all_inventories" width="100%">
<thead class="thead-light">
    <tr>
       <th scope="col"></th>
        <th scope="col">Item Name</th>
     <th scope="col">Code</th>
         <th scope="col">Batch Number</th>
        <th scope="col"> UOM </th>
         <th scope="col"> Available </th>
       
      
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
    <script src="{{asset('assets/admin/js/inventory/inventory.js')}}"> </script>
   
   @endpush