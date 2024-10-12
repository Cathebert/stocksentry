@extends('clerk.layout.main')
@section('title','Section Inventory Receive')
@push('style')
   
@endpush
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('user.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Received Items</li>
  </ol>
</nav>

<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Receive Items </strong></h5>
        @include('clerk.receive.tabs.section_receive_header')
<form method="post" id="receving_form">
   
          <input type="hidden" class="form-control" id="post_url" value="{{route('stock.receive')}}">
           <input type="hidden" class="form-control" id="fetch_data" value="{{route('items.fetch')}}">
              <input type="hidden" class="form-control" id="item_search" value="{{route('items.search')}}">
               <input type="hidden" class="form-control" id="recieve_url" value="{{route('item.selected')}}">
                <input type="hidden" class="form-control" id="print_url" value="{{route('item.print')}}">
      <input type="hidden" class="form-control" id="loadTable" value="{{route('item.getadded')}}">
       <input type="hidden" name="save_form_item" id="save_form_item" value="{{route('items.save')}}"/>
 <input type="hidden" id="delete_item" value="{{route('item.item_delete')}}"/>
      <input type="hidden" id="delete_all" value="{{route('item.item_delete_all')}}"/>
 
            <div class="row">
  <div class="col-md-2 col-sm-12 col-xs-12 form-group" >
    <label for="receiving_date">Receiving Date</label>
    <input type="date" class="form-control" id="receiving_date" value="{{ date('Y-m-d') }}" name="receiving_date" required>
    
  </div>
   <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
    <label for="lab_id">Receiving Lab</label>
     <select class="form-control" id="lab_id" name="lab_id" style="width: 75%" onchange="InitializeForm()" required>

 
   @foreach ($laboratories as $lab)
       <option value="{{$lab->id}}" selected>{{$lab->lab_name}}</option>
   @endforeach
</select>
</div>
@if($has_section=="yes")
   <div class="col-md-3 col-sm-12 col-xs-12 form-group" id="req"  >
    <label for="section_id">Receiving Unit</label>
     <select class="form-control" id="section_id" name="section_id" style="width: 75%"  required>
    <option value=""></option>
     @foreach ($sections as $sec)
       <option value="{{$sec->id}}">{{$sec->section_name}}</option>
   @endforeach
</select>
  </div>
  @endif
   <div class="col-md-3 col-sm-12 col-xs-12 form-group"  hidden  >
    <label for="siv_number">Source SIV</label>
    <input type="text" class="form-control" id="siv_number" name="siv_number">
    
  </div>
     <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
         <label for="exampleInputPassword1">Supplier</label>
                                                 <div class="input-group mb-3">
  <select class="form-control" id="supplier_id" name="supplier_id" style="width: 75%" required>
   
 <option value=""></option>
   @foreach ($suppliers as $supplier)
       <option value="{{$supplier->id}}">{{$supplier->supplier_name}}</option>
   @endforeach
  
  </select>

</div>

</div>

<script type="text/javascript">
  $(document).ready(function() {
    $('#supplier_id').select2({
 placeholder: 'Select  Supplier',
  width: 'resolve',
   
    });
      $('#lab_id').select2({
 placeholder: 'Select  Lab/Store',
  width: 'resolve',
   
    });

      $('#section_id').select2({
 placeholder: 'Select  Lab Section',
  width: 'resolve',
   
    });
     
});
</script>
  
  <div class="col-md-3 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">PO Ref</label>
    <input type="text" class="form-control" id="exampleInputPassword1" name="po_ref">
  </div>
   <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
    <label for="exampleInputPassword1">GRN Number</label>
    <input type="text" class="form-control" id="grn_number"  name="grn_number" maxlength="8" >
  </div>

  </div>
  
</div>
<hr>
<div class="row">
   <div class="col-md-3 col-sm-12 col-xs-12 form-group"  >
    <label for="exampleInputPassword1">Checked Off By</label>
    <select class="form-control" id="checked_off_by" name="checked_off_by" style="width: 75%">
     <option value=""></option>
  @foreach($users as $user)
    <option value="{{ $user->id }}">{{$user->name.' '.$user->last_name}}</option>
    @endforeach
 
  </select>
  </div>
   <div class="col-md-3 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Check Off Date</label>
    <input type="date" class="form-control" id="check_off_date" name="check_off_date" value="{{ date('Y-m-d')}}" >
  </div>
     <div class="col-md-3 col-sm-12 col-xs-12 form-group"  >

       <label for="exampleFormControlTextarea1">Comment: <span class="text-danger"></span></label>
    <textarea class="form-control"  rows="3" name="check_off_comment" id="check_off_comment" style="background-color:#fffff"></textarea>

</div>
</div>
<div class="row">
     <div class="col-md-3 col-sm-12 col-xs-12 form-group"  >
    <label for="exampleInputPassword1">Reviewed By</label>
    <select class="form-control" id="reviewed_by" name="reviewed_by" style="width: 75%">
     <option value=""></option>
  @foreach($users as $user)
    <option value="{{ $user->id }}">{{$user->name.' '.$user->last_name}}</option>
    @endforeach
 
  </select>
  </div>
   <div class="col-md-3 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Reviewed Date</label>
    <input type="date" class="form-control" id="reviewed_date" name="reviewed_date" value="{{ date('Y-m-d')}}" >
  </div>
   <div class="col-md-3 col-sm-12 col-xs-12 form-group"  >

       <label for="exampleFormControlTextarea1">Reviewer Comment: <span class="text-danger"></span></label>
    <textarea class="form-control"  rows="3" name="reviewer_comment" id="reviewer_comment" style="background-color:#fffff"></textarea>

</div>
</div>
</form>
</div>
<hr></br>
<!---table!-->

 
  


<form action="" autocomplete="off" class="form-horizontal" method="post" accept-charset="utf-8">
        <div class="input-group">
            <input name="searchtext" value="" class="form-control" type="text" placeholder="Search for Items" id="search_item">
         
             <div id="suggestion-box"></div>
            <span class="input-group-btn">&nbsp;
               <button class="btn btn-success " type="submit" id="add_item" disabled >
                
                   <span  role="status" aria-hidden="true" id="show_loader" ></span>
 Add
               </button>
            </span>
        </div>
    </form>
      </div>
    </div>
  



 
     <br>
  <div class="print"></div>
      <div class="card-body" id="dtable">
          <div class="card" >
          <div></div>
         <div>
           <button type="button" class="btn  btn btn-primary" id="save_received" style="float:right;margin-right:93%; margin-top:2%"  disabled><i class="fa fa-save"></i> Save</button>
          <button type="button" class="btn" onclick="removeAll()" style="color:#9F0000; float:right; margin-left:1%"  ><i class="fa fa-close"></i> Remove All</button>
   </div>
        
     </div>       
     

<hr>

        <h5 class="card-title"><strong>Items </strong></h5>
        <div class="table-responsive">
        <table class="table table-sm" id="received_items"  width="100%">
<thead class="thead-light">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Name</th>
      <th scope="col">Unit</th>
      <th scope="col">Quantity</th>
       <th scope="col">Batch #</th>
        <th scope="col">Expiry Date</th>
          <th scope="col">Cost</th>
          <th scope="col">Total</th>
          <th scope="col">Action</th>
    </tr>
  </thead>
</table>
      </div>
       </div>
       <form class="form-inline" style="float:right; margin-right:6%">
  <div class="form-group mb-2">
    <label for="staticEmail2" class="sr-only">Total:</label>
    <input type="text" readonly class="form-control-plaintext"style="font-weight: bold;" id="staticEmail2" value="Total:">
  </div>
  <div class="form-group mx-sm-1 mb-1">
    <label for="cost" class="sr-only">Cost</label>
    <input type="text" class="form-control form-control-lg" id="cost" style="font-weight: bold;direction: rtl;" placeholder="0.00" readonly disabled >
  
  </div>
  
</form>

</div>

</div>
 
</div>
 
 <div class="modal" tabindex="-1" id="inforg" role="dialog" >
  <div class="modal-dialog modal-xl" role="document" >
    <div class="modal-content" id="receive_item">
</div>
</div>
</div>

<script type="text/javascript">
  $("#search_item").focus();
  </script>


            @endsection

    @push('js')
    
     <script src="{{asset('assets/admin/js/inventory/receive_inventory.js') }}"></script>
     
   @endpush