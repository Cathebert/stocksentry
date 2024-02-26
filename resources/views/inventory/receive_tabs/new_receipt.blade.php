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
    <li class="breadcrumb-item active" aria-current="page">New Receipt</li>
  </ol>
</nav>


<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>New Receipt </strong></h5>
@include("inventory.receive_tabs.header")
<br>
        <form method="post" id="receving_form">
   
      <input type="hidden" class="form-control" id="post_url" value="{{route('stock.receive')}}">
      <input type="hidden" class="form-control" id="fetch_data" value="{{route('items.fetch')}}">
      <input type="hidden" class="form-control" id="item_search" value="{{route('items.search')}}">
      <input type="hidden" class="form-control" id="recieve_url" value="{{route('item.selected')}}">
      <input type="hidden" class="form-control" id="print_url" value="{{route('received.details')}}">
      <input type="hidden" class="form-control" id="loadTable" value="{{route('item.getadded')}}">
      <input type="hidden" name="save_form_item" id="save_form_item" value="{{route('items.save')}}"/>
      <input type="hidden" id="get_sections" value="{{route('lab.sections')}}"/>
      <input type="hidden" id="delete_item" value="{{route('item.item_delete')}}"/>
      <input type="hidden" id="delete_all" value="{{route('item.item_delete_all')}}"/>
            <div class="row">
  <div class="col-md-2 col-sm-12 col-xs-12 form-group" >
    <label for="receiving_date">Receiving Date</label>
    <input type="date" class="form-control" id="receiving_date" value="{{ date('Y-m-d') }}" name="receiving_date" required>
    
  </div>
   <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
    <label for="lab_id">Receiving Store/Lab</label>
     <select class="form-control" id="lab_id" name="lab_id" style="width: 75%" onchange="InitializeForm(this.value)" required>
    <option value=""></option>
 
   @foreach ($laboratories as $lab)
       <option value="{{$lab->id}}" >{{$lab->lab_name}}</option>
   @endforeach
</select>
</div>
   <div class="col-md-3 col-sm-12 col-xs-12 form-group" id="req"  hidden>
    
  </div>
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
    <input type="text" class="form-control" id="po_ref" name="po_ref">
  </div>
   <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
    <label for="exampleInputPassword1">GRN Number</label>
    <input type="text" class="form-control" id="grn_number"  name="grn_number" >
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
  </div>



 
     <br>
  <div class="print"></div>
      <div class="card-body" id="dtable">
          <div class="card">
          <div></div>
         <div>
           <button type="button" class="btn  btn-sm btn-primary" id="save_received" style="float:right;margin-right:90%; margin-top:1%"  disabled><i class="fa fa-save"></i> Save</button>
          <button type="button" class="btn" onclick="removeAll()" style="color:#9F0000; float:right; margin-left:1%"  ><i class="fa fa-close"></i> Remove All</button>
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

   