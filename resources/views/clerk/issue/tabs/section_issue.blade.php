@extends('clerk.layout.main')
@section('title','Inventory')
@push('style')
   
@endpush
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('user.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Issue</li>
  </ol>
</nav>
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Item Issue</strong></h5>
  @include('clerk.issue.tabs.section_issue_header') 
  <div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
      
       
         
                 <form method="post"  id="issue-form-data" >
   <input type="hidden" class="form-control" id="save_issued_url" value="{{route('inventory.save-issued')}}">
                  <input type="hidden" id="get_receiver" value="{{route('user.receiver')}}"/>
                  <input type="hidden" id="data_url" value="{{route('issue.getItems')}}" />
                  <input type="hidden" id="issue_approvals" value="{{route('issue.approve')}}"/>
                   <input type="hidden" id="save_approve_issued_item" value="{{route('issue.approve_save')}}"/>
                  <input type="hidden" id="void_url" value="{{route('issue.void_issue')}}"/>
                    <input type="hidden" id="items_approved_take" value="{{route('issue.approved')}}"/>
                <input type="hidden" id="view_approvals" value="{{route('issue.showapprovals')}}" />
                <input type="hidden" id="view_approved" value="{{route('issue.showapproved')}}"/>
                <input type="hidden" id="approved" value="{{route('issue.approved')}}"/>
                 <input type="hidden" id="view_issue_siv" value="{{route('issue.view')}}"/>
                 <input type="hidden" id="selected" value="{{ route('inventory.getSelectedItems') }}"/>
                 
    <div class="row" >

  <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
      <label for="lab_id">Stock Transfer  # </label>
  <input type="text" aria-label="First name" id="siv" class="form-control" name="siv" placeholder="" value="{{ $sr_number }}"  readonly >
  
</div>


   <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
        <label for="lab_id">Issue From Lab</label>
  <select class="form-control" id="from_lab_id" name="from_lab_id" style="width: 75%"required>
    <option value=""></option>

   @foreach ($laboratories as $lab)
   @if($lab->id==auth()->user()->laboratory_id)
       <option value="{{$lab->id}}" {{ $lab->id==auth()->user()->laboratory_id ? "Selected" : ""  }}>{{$lab->lab_name}}</option>
  @endif
       @endforeach
</select>
</div>


   <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
    <label for="lab_id">Issue to Lab</label>
     <select class="form-control" id="to_lab_id" name="to_lab_id" style="width: 75%" onchange="getReceiver(this.value)"  required>
    <option value=""></option>

   @foreach ($laboratories as $lab)
   @if($lab->id!=auth()->user()->laboratory_id)
       <option value="{{$lab->id}}">{{$lab->lab_name}}</option>
       @endif
   @endforeach
</select>
</div>




<div class="col-md-3 col-sm-12 col-xs-12 form-group" id="req"  >
    <label for="section_id">Received by:</label>
     <select class="form-control" id="received_by" name="recieved_by" style="width: 75%"  required>
    <option value=""></option>
    
</select>
  </div>
 <div class="col-md-2 col-sm-12 col-xs-12 form-group" >
    <label for="receiving_date">Issue Date</label>
    <input type="date" class="form-control" id="issue_date" value="{{ date('Y-m-d') }}" name="issue_date"  required readonly>
    
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
        <button type="button" class="btn btn-secondary btn-sm " id="add_new_issue" style="" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fa fa-plus" ></i>Add</button>&nbsp;&nbsp;
          <button type="button" class="btn btn-success btn-sm " id="save_issue" style="" disabled><i class="fa fa-save"></i> Save</button>&nbsp;&nbsp;
      
     </div>
      <br>
 <div class="btn-group  btn-group-sm" role="group" aria-label="Basic example" style="float:right">
    @if(auth()->user()->authority==2 ||auth()->user()->authority==1 )
 <button type="button" class="btn  btn-primary btn-sm " style="text-color:white"id="save_issue" style="" onclick="showApprovals()"><i class="fa fa-hourglass-half"></i>Pending Approvals  <span class="badge badge-pill badge-danger" ></span></button>&nbsp;&nbsp;
 
 @endif
 <button type="button" class="btn  btn-success btn-sm " style="text-color:white"id="show_approved" style="" ><i class="fa fa-check"></i>Approved <span class="badge badge-pill badge-danger"></span></button>&nbsp;&nbsp;
</div>

   <br>
 
        <h5 class="card-title"><strong>Issues</strong></h5>
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
  <div class="form-group mx-sm-1 mb-1">
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
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Enter Quantities to issue Items</h5>
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
          <th scope="col">Batch #</th>
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
       
        <button type="button" class="btn btn-primary" id="test">Ok</button>
      </div>
    </div>
  </div>
</div>


<!----------------approved modal ----->
 
 
</div> 
 

        
       <script type="text/javascript">
     var checked= [];
   var arr=[]


  </script> 
  
    <div class="modal" tabindex="-1" id="inforg" role="dialog" >
  <div class="modal-dialog modal-xl" role="document" >
    <div class="modal-content" id="view_item_datails">

    </div>
    </div>
</div>
   

            @endsection

            @push('js')
 <script src="{{asset('assets/admin/js/inventory/issues/add-issue.js') }}"></script>

@endpush