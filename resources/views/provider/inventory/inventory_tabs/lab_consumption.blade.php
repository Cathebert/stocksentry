 @extends('provider.layout.main')
@section('title','Lab Inventory')
@push('style')
   
@endpush
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('moderator.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Consumption</li>
  </ol>
</nav>


<!--content start-->

<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Update Consumption</strong></h5>
      
   @include('provider.inventory.inventory_tabs.lab_inventory_header')  
<div class="row" >
      <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Update Consumption </strong></h5>
<form method="post" id="consume_form">
          @csrf
          <input type="hidden" class="form-control" id="inventory_update_all" value="{{route('inventory.update_all')}}">
          <input type="hidden" class="form-control" id="update_selected" value="{{route('inventory.selected_update')}}">
          <input type="hidden" class="form-control" id="item_search" value="{{route('items.search')}}">
          <input type="hidden" class="form-control" id="inventory_received" value="{{route('item.recieved')}}">
            <input type="hidden" id="check_quantity" value="{{route('inventory.check_quantity')}}"/>
          <input type="hidden" class="form-control" id="load_inventory" value="{{route('inventory.load')}}"> 
            <input type="hidden" id="modal_dispose_url" value="{{route('disposal.list')}}"/>
 
            
            <div class="row">
    <div class="col-md-4 col-sm-12 col-xs-12 form-group"  >
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <label class="input-group-text" for="period">Period</label>
    <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
  </div>
  <select class="custom-select" id="period" name="period" onchange="getSelected()" disabled>
    
    <option value="1" selected>Today</option>
    <option value="2" >This Week</option>
    <option value="3">This Month</option>
  </select>
</div>
  </div>
  
     <div class="col-md-6 col-sm-12 col-xs-12 form-group" hidden >
  <div class="input-group">
  <span class="input-group-text">Custom Range:</span> 
  <input type="date" aria-label="First name" class="form-control" id="start_date" name="start_date">
   <span class="input-group-text">-</span>
  <input type="date" aria-label="Last name" class="form-control " id="end_date" name="end_date">
</div>
  </div>
  
</form>
<div class="dropdown" style="text-align:right" >
 <button type="button" class="btn btn-primary" id="update_all"  onclick="updateAll()">Update All</button>
   
      </div>
      <br>
</div>
 <h6 id="status" style="text-align:center"> </h6>
</div>
 <script type="text/javascript">
 
    $('#status').html("As of <strong>"+ getTodaysDate()+"</strong>")
 
 function getSelected (){
  var value=$('#period').val();

  
if(value==1){

$('#status').html("As of <strong>"+ getTodaysDate()+"</strong>")
 }
 if(value==2){
  const getWeekBehind=()=>{

var v= new Date(new Date().setDate(new Date().getDate() - 7));
  return v.toUTCString().slice(5, 16);
 
  }
   $('#status').html("From <strong>"+ getWeekBehind()+"</strong> - <strong>"+getTodaysDate()+"</strong>")
 }
 if(value==3){
  let date_today = new Date();

let firstDay =  moment().startOf('month').format('DD MMM YYYY');
let lastDay =  moment().endOf('month').format('DD MMM YYYY');
//let month=firstDay.toUTCString().slice(5, 16);
 $('#status').html("From <strong>"+firstDay+ "</strong>- <strong>"+lastDay+"</strong>")

 }

 function getTodaysDate(){
   const date = new Date();
  return date.toUTCString().slice(5, 16);;
 }
  }
   function getTodaysDate(){
   const date = new Date();
  return date.toUTCString().slice(5, 16);;
 }
  </script>
<hr></br>


</form>

      </div>
    </div>
        <!---- table start ---->
               <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm table-hover" id="update_inventories" width="100%">
<thead class="thead-light">
    <tr>
       <th scope="col"></th>
       <th scope="col">Item Name</th>
       <th scope="col">Code</th>
       <th scope="col">Batch Number</th>
       <th scope="col">Catalog #</th>
           <th scope="col">Expiry</th>
       <th scope="col">Last Update</th>
        <th scope="col">Next Update</th>
        <th scope="col">UOM </th>
        <th scope="col">Available </th>
        <th scope="col">Consumed</th>
  
       <th scope="col">Action</th>
      
    </tr>
  </thead>
  <tbody>
</table>
      </div>
        
      </div>

    </div>
            @endsection

    @push('js')
    <script src="{{asset('assets/admin/js/inventory/update_inventory.js')}}"> </script>
   
   @endpush