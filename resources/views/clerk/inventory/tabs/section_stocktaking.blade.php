 @extends('clerk.layout.main')
@section('title','StockSentry')
@push('style')
   
@endpush
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
   <li class="breadcrumb-item"><a href="{{route('user.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{route('section.bincard_inventory')}}">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Stock Take</li>
  </ol>
</nav>


<!--content start-->

<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Stock Take</strong></h5>
      
   @include('clerk.inventory.tabs.section_inventory_header')   
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
      <input type="hidden" class="form-control" id="inventory_taking" value="{{route('section_inventory.stocktake')}}"> 
         <input type="hidden" class="form-control" id="inventory_save_all" value="{{route('stock.saveall')}}"> 
    <input type="hidden" class="form-control" id="selected_location" value="{{route('section.filter_stocktake')}}"/>
       <input type="hidden" id="expected" value="{{$count}}"/>

 
            
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
    <div class="col-md-4 col-sm-12 col-xs-12 form-group"   >
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <label class="input-group-text btn btn-secondary" for="period">Stock Take Area</label>
    <span class="input-group-text"><i class="fa fa-home" aria-hidden="true"></i></span>
  </div>
  <select class="form-control" id="storage_area" name="storage_area" onchange="SelectArea(this.value)">
    <option value="0" selected> All</option>
    <option value="1" > Store</option>
    <option value="3" > Section </option>
   
     
  </select>
</div>
  </div>
  
     <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
  <div class="input-group ">
  <span class="input-group-text btn btn-secondary">Date:</span> 
  <input type="date" aria-label="First name" class="form-control" id="start_date" name="start_date" value="{{date('Y-m-d')}}">
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

@foreach ($users as $user )
      <option value="{{$user->id}}">{{$user->name.' '.$user->last_name}}</option>
@endforeach
  </select>
</div>
  </div>   


    <div class="col-md-4 col-sm-4 col-xs-12 form-group" hidden>
   <div class="input-group mb-3">
  <div class="input-group-prepend">
    <span class="input-group-text btn btn-secondary">Inventory  Area</span>
  </div>
  <select class="custom-select" id="inputGroupSelect03" aria-label="Example select with button addon">

@foreach ($area as $section )
      <option value="{{$section->id}}">{{$section->section_name}}</option>
@endforeach
  </select>
</div>
  </div> 
                            <div class="col-md-4 col-sm-4 col-xs-12 form-group" >
   <div class="input-group mb-3">
  <div class="input-group-prepend">
    <span class="input-group-text btn btn-secondary">Employees Involved</span>
  </div>
                               <select class=" form-control" id="employees" style="width: 50%" name="employee_involved[]" multiple >
                                    
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

  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink" hidden>
  
    <a class="dropdown-item" href="#" onclick="inputFile()"><i class="fa fa-download" hidden></i> Import CVS File</a>
    <a class="dropdown-item" href="{{ route('stock.export') }}"><i class="fa fa-share"></i> Export Inventory</a>
  </div>
</div>
  </div>
      </div>
</div>
 <h6 id="status" style="text-align:center"> </h6>

 
 




</form>

      

       
<hr></br>
  
        <!---- table start ---->
       <div class="table-responsive">
        <table class="table table-sm" id="inventories_taking" width="100%">
<thead class="thead-light">
    <tr>
       <th scope="col"></th>
     <th scope="col">Code</th>
       <th scope="col">Brand</th>
        <th scope="col">Batch Number</th>
        <th scope="col">Generic Name</th>
        <th scope="col">UOM </th>
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
     
  </div>
            @endsection
 @push('js')
   
   <script src="{{asset('assets/admin/js/inventory/stock_take.js')}}"> </script>
  
@endpush