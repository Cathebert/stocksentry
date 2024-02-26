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
    <li class="breadcrumb-item active" aria-current="page">Adjustments</li>
  </ol>
</nav>


<!--content start-->

<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Stock Adjustment</strong></h5>
      
   @include('provider.inventory.inventory_tabs.lab_inventory_header')  
<div class="row" >
      <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Stock Adjustment </strong></h5>
<form method="post" id="consume_form">
          @csrf
          <input type="hidden" class="form-control" id="inventory_update_all" value="{{route('inventory.update_all')}}">
           <input type="hidden" class="form-control" id="update_selected" value="{{route('inventory.selected_adjust')}}">
             
               <input type="hidden" class="form-control" id="inventory_received" value="{{route('item.recieved')}}">
  
  <input type="hidden" class="form-control" id="load_inventory" value="{{route('inventory.adjust')}}"> 
  <input type="hidden" class="form-control" id="item_search" value="{{route('inventory.search_adjustment')}}">
 <input type="hidden" id="view_adjustment_url" value="{{route('inventory.view_adjusted')}}"/>
 <input type="hidden" id="approve_selected" value="{{route('inventory.adjusted_approve')}}"/>
<input type="hidden"id="reload" value="{{route('inventory.load_adjusted')}}"/>
            
            <div class="row">
    <div class="col-md-8 col-sm-12 col-xs-12 form-group"  >
<div class="input-group mb-3">
  <div class="input-group-prepend">

    <span class="input-group-text btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></span>
  </div>
 <input type="text" aria-label="First name" class="form-control"  name="searchtext" placeholder="Search Item by Name or Batch Number" id="search_item">
  <div id="suggestion-box"></div>
</div>
  </div>
  
     <div class="col-md-4 col-sm-12 col-xs-12 form-group" >
  <div class="input-group">
  <span class="input-group-text">Date:</span> 
  <input type="date" aria-label="First name" class="form-control" id="start_date" name="date" value="{{date('Y-m-d')}}" readonly>
  
</div>
  </div>
  
</form>

</div>
 <h6 id="status" style="text-align:center"> </h6>
</div>
 <script type="text/javascript">
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
  </script>
<hr></br>


</form>

      </div>
      <br>
      <div class="dropdown" style="text-align:right" >
     
    <button type="button" class="btn btn-primary" id="view_adjustment">View Adjustments</button>
      </div>
     

   
    
      <br>
    </div>
        <!---- table start ---->
               <div class="table-responsive">
        <table class="table table-sm" id="adjust_inventories" width="100%">
<thead class="thead-light">
    <tr>
        <th scope="col"></th>
        <th scope="col">Code</th>
        <th scope="col">Brand</th>
        <th scope="col">Batch Number</th>
        <th scope="col">Generic Name</th>
        <th scope="col">UOM </th>
        <th scope="col">Available </th>
        <th scope="col">Adjustment</th>
        <th scope="col">Type</th>
        <th scope="col"></th>
    </tr>
  </thead>
  <tbody>
</table>
      </div>
      <form>
       <div class="row">

       <label for="exampleFormControlTextarea1">Note: <span class="text-danger">*</span></label>
    <textarea class="form-control"  rows="3" name="notes" id="notes" style="background-color:#f5f2f0"></textarea>

</div>
<br>
 <button type="button" class="btn btn-primary" id="adjust">Submit</button>
</form>
 
</div>
</div>
    <div class="modal" tabindex="-1" id="inforg" role="dialog" >
  <div class="modal-dialog modal-xl" role="document" >
    <div class="modal-content" id="view_item_datails">
            @endsection

  @push('js')
     <script src="{{asset('assets/admin/js/inventory/stock_adjustment.js')}}"> </script>
     @endpush