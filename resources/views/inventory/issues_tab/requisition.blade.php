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
    <li class="breadcrumb-item active" aria-current="page">Requisitions</li>
  </ol>
</nav>
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Item Requisition</strong></h5>
  @include('inventory.issues_tab.header') 
  <div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
      
       
         
                 <form method="post"  id="requsition-form-data" >
                     <input type="hidden" id="get_requisitions" value="{{route('requisition.getRequests')}}" />
      <input type="hidden" id="view_approved_request" value="{{route('requests.view-approved')}}"/>
      <input type="hidden" id="view_consolidated_list" value="{{route('consolidate.history')}}"/>
      <input type="hidden" id="save_approved_request" value="{{route('requisition.save-approved')}}"/>
      <input type="hidden"  id="filter_url"   value="{{route('requisition.filter')}}"/>      
       <input type="hidden" id="get_sections" value="{{route('lab.selected-sections')}}"/>          
        <input type="hidden" id="mark_consolidate"   value="{{route('requisition.mark')}}"/> 
        <input type="hidden" id="view_marked_for_consolidation" value="{{route('requisition.view_marked')}}"/>     
                 
                 
    <div class="row" >

  <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
      <label for="lab_id">SR # </label>
  <input type="text" aria-label="First name" id="sr" class="form-control" name="sr" placeholder="" value="" oninput="getSRnumber()">
  
</div>

 
  


   <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
    <label for="lab_id">Requesting  Lab</label>
     <select class="form-control" id="lab_id" name="lab_id" style="width: 75%" onchange="getLab(this.value)"  required>
    <option value=""></option>

   @foreach ($laboratories as $lab)
   @if($lab->id==0)
      
       @else
        <option value="{{$lab->id}}"> {{$lab->lab_name}}</option>
        @endif
   @endforeach
</select>
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
    <a class="nav-link active" href="" id="show_consolidated_history"><i class="fa fa-list"></i>  View Consolidated List</a>
   
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
 
        <h5 class="card-title"><strong>Requisitions</strong></h5>
        <div id='real_table'></div>
        <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm" id="requisitions">
    <thead class="thead-light">
    <tr>
      <th scope="col">SR #</th>
      <th scope="col">Requesting Lab</th>
      <th scope="col">Requested Date</th>
      <th scope="col">Options</th>
     <th scope="col">Consolidate</th>
    </tr>
  </thead>
  <tbody>
</table>
 </div>
    
   
     </div>
     
     </div>
    </div>
  </div>
  <br>
 </div>
  </div>





 
  <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Items</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close_item_modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="check_quantity" value="{{route('inventory.check_quantity')}}"/>
         <div class="table-responsive">
        <table class="table table-sm" id="issue_items" width="100%">
<thead class="thead-light">
    <tr>
       <th scope="col"></th>
     <th scope="col">Code</th>
        <th scope="col">Generic Name</th>
         <th scope="col">Available </th>
          <th scope="col">Requested</th>
      <th scope="col">Brand</th>
  
       <th scope="col">Status</th>
      
    </tr>
  </thead>
  <tbody>
</table>
      </div>
        <div class="products items"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="test">Ok</button>
      </div>
    </div>
  </div>
</div>


<!----------------approved modal ----->
<div class="modal fade" id="approved" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Approved</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
           <div class="card">
  <div class="card-header text-success" >
    <strong>Approved Issues</strong>
  </div>
  <div class="card-body">
    <h5 class="card-title">Issues</h5>
     <div class="table-responsive">
        <table class="table table-sm table-striped" id="issue_approved_items"  width="100%">
<thead class="thead-light">
    <tr>
       
    
         <th width="5%">SIV #</th>
          <th width="20%">Issued Date</th>
          <th width="20%">Issued To</th>
          <th width="20%">Issued By</th>
            <th width="20%">Approved By</th>
            <th width="20%">Receipt</th>
             <th width="20%">Action</th>
           
  </thead>
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
  <div class="modal-dialog modal-xl" role="document" >
    <div class="modal-content" id="view_item_datails">

          
</div>
  </div>
  </div>
            @endsection

            @push('js')
                <script src="{{asset('assets/admin/js/inventory/issues/requisition.js') }}"></script>

@endpush