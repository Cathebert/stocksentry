@extends('cold.layout.main')
@section('title','StockSentry')
@push('style')
   
@endpush
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
   <li class="breadcrumb-item"><a href="{{route('cold.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{route('cold.bincard_inventory')}}">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Stock Take</li>
  </ol>
</nav>


<!--content start-->

<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Stock Take</strong></h5>
      
   @include('cold.inventory.tabs.cold_inventory_header')   
<div class="row" >
      <div class="col-sm-12">
    
    <div class="card">

      <div class="card-body">
        
       <h5 class="card-title"> <strong>Stock Taking</strong></h5>
<form method="post" id="stock_save_form">
          @csrf
          <input type="hidden" class="form-control" id="inventory_update_all" value="{{route('inventory.update_all')}}">
           <input type="hidden" class="form-control" id="save_selected" value="{{route('inventory.selected_save')}}">
              <input type="hidden" class="form-control" id="item_search" value="{{route('items.search')}}">
               <input type="hidden" class="form-control" id="inventory_received" value="{{route('item.recieved')}}">
      <input type="hidden" class="form-control" id="inventory_taking" value="{{route('inventory.stock')}}"> 
         <input type="hidden" class="form-control" id="inventory_save_all" value="{{route('stock.saveall')}}"> 
<input type="hidden" id="expected" value="{{$count}}"/>
<input type="hidden" id="edit_inventory_modal" value="{{ route('inventory.edit_modal') }}"/>
<input type="hidden" id="item_locate" value="{{route('stock.item_location')}}"/>
<input type="hidden" id="download_item" value="{{route('stock.download_item_selected')}}"/>
 
            
            <div class="row">
    <div class="col-md-4 col-sm-12 col-xs-12 form-group"  hidden >
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <label class="input-group-text btn btn-secondary" for="period">Period</label>
    <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
  </div>
  <select class="custom-select" id="period" name="period" onchange="getSelected()">
    <option value=""selected> Select Period</option>
   
    <option value="1"> Weekly</option>
    <option value="2"> Monthly</option>
    <option value="3">Quarterly</option>
      <option value="4">Yearly</option>
  </select>
</div>
  </div>
  
     <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
  <div class="input-group ">
  <span class="input-group-text btn btn-secondary">Date:</span> 
  <input type="date" aria-label="First name" class="form-control" id="start_date" name="start_date" value="{{date('Y-m-d')}}" readonly>
   <span class="input-group-text btn btn-secondary"  hidden>-</span>
  <input type="date" aria-label="Last name" class="form-control " id="end_date" name="end_date" hidden>
</div>
</div>
  <div class="col-md-4 col-sm-4 col-xs-12 form-group" >
   <div class="input-group mb-3">
  <div class="input-group-prepend">
    <span class="input-group-text btn btn-secondary">Supervisor</span>
  </div>
  <select class="custom-select" id="supervisor" aria-label="Example select with button addon" name="supervisor">
 <option value=""></option>
@foreach ($users as $user )
      <option value="{{$user->id}}">{{$user->name.' '.$user->last_name}}</option>
@endforeach
  </select>
</div>
  </div>   


    
                           <div class="col-md-8 col-sm-4 col-xs-12 form-group" >
                                <label for="fullname">Employees Involved <span class="text-danger"></span></label>
                                <select class="form-control" id="employees" style="width: 50%" name="employee_involved[]" multiple >
                                    
                                    @foreach($users as $employee)
                                
                                        <option value="{{$employee->id}}">{{$employee->name.' '.$employee->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            

</div>
  <script type="text/javascript">
  $(document).ready(function() {
    $('#employees').select2({
 placeholder: 'Select  employees Involved',
      allowClear: true,

   
    });
 
     
});
</script>
</form>

 
  <div class="dropdown" style="text-align:right" >
  <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
  <i class="fa fa-cogs"></i>
  </a>

  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
  
    <a class="dropdown-item" href="#" onclick="inputFile()" hidden><i class="fa fa-download"></i> Import CVS File</a>
    <a class="dropdown-item" href="{{ route('stock.export') }}"><i class="fa fa-share"></i> Export Inventory</a>
  </div>
</div>
  </div>
      </div>
</div>
 
<h3  style="text-align:right" hidden><span class="badge badge-secondary">{{ $count }}</span></h3>
 
 




</form>

      

 
  <div class="col-sm-12">
     <br>
    <div class="card">
 <div class="card-body">
<form class="form-inline" method="get" id="download_item" action="{{route('stock.download_item_selected')}}" >
  <label class="my-1 mr-2" for="download_item">Lab(s) </label>
  <select class="custom-select my-1 mr-sm-2" id="items_list" onchange="getItems(this.value,this.name)" style="width: 70%"  name="labs[]" multiple>
<option value="-1"></option>
  @foreach ( $labs as $lab)
     <option value="{{ $lab->id }}">{{$lab->lab_name}}</option>
  @endforeach
  <option value="999">Other</option>
  </select>
  
 

  
&nbsp;&nbsp;
  <button type="submit" class="btn btn-primary my-1" id="submit"><i class="fa fa-download"></i></button>
</form>
</div>
 </div>
 </div>
<script type="text/javascript">
    $(document).ready(function() {
  $('#items_list').select2({
 placeholder: 'Select lab(s)',
      allowClear: true,
     });
     });
</script>   
</br>
 
        <!---- table start ---->
       <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm table-hover" id="inventories_taking" width="100%">
<thead class="thead-light">
    <tr>
        <th scope="col"></th>
     <th scope="col">Item Name</th>
       <th scope="col">Code</th>
        <th scope="col">Batch Number</th>
        <th scope="col">UOM </th>
        <th scope="col">Expiry</th>
          <th scope="col">Location </th>
          <th scope="col">Edit Entry </th>
        <th scope="col">Physical Count</th>
       <th scope="col">Action</th>

      
    </tr>
  </thead>
  <tbody>
</table>
      </div>
	    </div>
     


 <div class="modal-footer">
     
        <button type="button" class="btn btn-primary" id="save_all"  onclick="saveAll()">Save All</button>
      </div>

<!----------Table end --------->
      </div>
     
  <!--------moddal-------------------->
    <div class="modal" tabindex="-1" id="inforg" role="dialog" >
  <div class="modal-dialog modal-lg" role="document" >
    <div class="modal-content" id="receive_item">

    </div>
    </div>
    </div>

<!--------end modal------------->   
     
  </div>
            @endsection
 @push('js')
  <script src="{{asset('assets/admin/vendors/select2/dist/js/select2.full.min.js') }}"></script>
   <script src="{{asset('assets/admin/js/inventory/stock_take.js')}}"> </script>
  
@endpush