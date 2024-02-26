  @extends('provider.layout.main')
@section('title','Lab Inventory Order')
@push('style')
   
@endpush
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('moderator.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Items Order</li>
  </ol>
</nav>


<!--content start-->

<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Order Items</strong></h5>
      
   @include('provider.issues.tabs.issue_header') 
  <div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
      
       
         
                 <form method="post"  id="issue-form-data" >
                 <input type="hidden" class="form-control" id="save_issued_url" value="{{route('requests.store')}}">
               
                  <input type="hidden" id="selected_items" value="{{ route('requests.getSelectedItems') }}"/>
                
                  <input type="hidden" id="showpending_requests" value="{{route('requests.pending')}}"/>
                  <input type="hidden" id="view_request" value="{{route('requests.view')}}"/>
                  <input type="hidden" id="view_requisition_list" value="{{route('requests.view_list')}}" />
                  <input type="hidden" id="showapproved_requests" value="{{route('requests.approved')}}"/>
                  <input type="hidden" id="showitemList" value="{{route('requests.showOrderItems')}}"/>
                <input type="hidden" id="check_quantity"    value="{{route('inventory.check_quantity')}}"/>
                 
                 
    <div class="row" >

  <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
      <label for="lab_id">SR # </label>
  <input type="text" aria-label="First name" id="sr_number" class="form-control" name="sr_number" placeholder="" value="{{$sr_number}}" readonly>
  
</div>


   <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
        <label for="lab_id"> Lab</label>
  <select class="form-control" id="lab_id" name="lab_id" style="width: 75%" required onchange="checkLab()">
   

   @foreach ($laboratories as $lab)
   @if($lab->id==auth()->user()->laboratory_id)
       <option value="{{$lab->id}}" {{ $lab->id==auth()->user()->laboratory_id ? "Selected" : ""  }}>{{$lab->lab_name}}</option>
   @endif
   @endforeach
</select>
</div>


  @if(!empty($sections) && count($sections)>0)

<div class="col-md-3 col-sm-12 col-xs-12 form-group"  id="sec"  hidden>
    <label for="section_id"> Section</label>
     <select class="form-control" id="section_id" name="section_id" style="width: 75%"  required >
   
     @foreach ($sections as $sec)
       <option value="{{$sec->id}}">{{$sec->section_name}}</option>
   @endforeach
</select>
  </div>
@endif

 <div class="col-md-2 col-sm-12 col-xs-12 form-group" >
    <label for="receiving_date">Date</label>
    <input type="date" class="form-control" id="request_date" value="{{ date('Y-m-d') }}" name="request_date"  required>
    
  </div>


</div>

<hr><br>


</div>
</form>


</div>
   
    <div class="row">
      
 <div class="col-sm-12">
  
    <div class="card">
   
      <div class="card-body">
           <div class="btn-group  btn-group-sm" role="group" aria-label="Basic example" style="float:left">
        <button type="button" class="btn btn-secondary btn-sm " id="add_new_request" style="" ><i class="fa fa-plus" ></i> Order Items</button>&nbsp;&nbsp;
          <button type="button" class="btn btn-success btn-sm " id="save_issue" style="" disabled><i class="fa fa-save"></i> Save</button>&nbsp;&nbsp;
      
     </div>
      <br>
 <div class="btn-group  btn-group-sm" role="group" aria-label="Basic example" style="float:right">
    @if(auth()->user()->authority==2 ||auth()->user()->authority==1 )
 <button type="button" class="btn  btn-primary btn-sm " style="text-color:white" style="" id='pending_request_approval'  ><i class="fa fa-hourglass-half"></i> Pending Approvals  <span class="badge badge-pill badge-danger" id="requests-badge">{{$requests}}</span></button>&nbsp;&nbsp;
 
 @endif
 <button type="button" class="btn  btn-success btn-sm " style="text-color:white" id="approved_request" style=""   ><i class="fa fa-check"></i> Approved  <span class="badge badge-pill badge-danger" id="appro">{{$approved}}</span></button>&nbsp;&nbsp;
<button type="button" class="" style="text-color:white "id="requisition_list" style=""  ><i class="fa fa-list"></i> Requisition list  </button>&nbsp;&nbsp;
</div>

   <br>
 
        <h5 class="card-title"><strong>Ordered Items</strong></h5>
        <div id='real_table'></div>
        <div class="table-responsive">
        <table class="table table-sm" id="items">
    <thead class="thead-light">
    <tr>
      <th scope="col">CODE #</th>
      <th scope="col">Item</th>
      <th scope="col">UOM</th>
      <th scope="col">Quantity</th>
      <th scope="col">Batch #</th>
      <th scope="col">Expiry</th>
      <th scope="col">Cost</th>
      <th scope="col">Total</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>
</table>
 </div>
    
          <form class="form-inline" style="float:right; margin-right:16%;">
  <div class="form-group mb-2">
    <label for="staticEmail2" class="sr-only">Total:</label>
    <input type="text" readonly class="form-control-plaintext"style="font-weight: bold;" id="staticEmail2" value="Total:">
  </div>
  <div class="form-group " >
    <label for="cost" class="sr-only">Cost</label>
    <input type="text" class="form-control form-control-sm" id="cost" style="font-weight: bold;direction: rtl;" placeholder="0.00" readonly disabled >
  </div>
  
</form>
     </div>
     
     </div>
    </div>
  </div>
  <br>
 </div>
  </div>





 
  <!-- Modal -->


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
       
    
         <th width="5%">Stock Transfer #</th>
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
    
 <div class="modal" tabindex="-1" id="inforg" role="dialog" >
  <div class="modal-dialog modal-xl" role="document" >
    <div class="modal-content" id="request_datails">
</div>
</div>

</div>
 </div>
            @endsection

    @push('js')
   <script src="{{asset('assets/moderator/requests/request.js') }}"></script>
    @endpush