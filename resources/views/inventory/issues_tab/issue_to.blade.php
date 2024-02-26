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
    <li class="breadcrumb-item active" aria-current="page">Receive</li>
  </ol>
</nav>
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Receive Issued</strong></h5>
  @include('inventory.issues_tab.header') 
<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Received  Items </strong></h5>
<form method="post" id="form_id">
          @csrf
          <input type="hidden" class="form-control" id="received_issues" value="{{route('received.issues')}}">
           <input type="hidden" class="form-control" id="showgrndetails" value="{{route('received.details')}}">
              <input type="hidden" class="form-control" id="item_search" value="{{route('items.search')}}">
               <input type="hidden" class="form-control" id="inventory_received" value="{{route('item.recieved')}}">
                 <input type="hidden" class="form-control" id="accept_issue" value="{{route('issue.accept')}}">
             <input type="hidden" id="get_by_siv" value="{{route('issue.search_by_number')}}"/>
             <input type ="hidden" id="get_date_url" value="{{route('issue.search_by_date_range')}}"/>
             <input type="hidden" id="get_sent_lab_url" value="{{route('issue.search_by_lab_sent')}}"/>
   <input type="hidden" id="view_issue_siv" value="{{route('issue.view')}}"/>

 
            <input type="hidden" class="form-control" id="loadTable" value="{{route('item.getadded')}}">    <div class="row">
    <div class="col-md-4 col-sm-12 col-xs-12 form-group" >
  <div class="input-group">
  <span class="input-group-text btn btn-secondary">Stock Transfer #</span>
  <input type="text" aria-label="First name" class="form-control" name="siv_start" id="siv_start" oninput="getByStockTransferNumber(this.value)">
   
</div>
  </div>
     <div class="col-md-4 col-sm-12 col-xs-12 form-group" >
  <div class="input-group">
  <span class="input-group-text btn btn-secondary">Issue Date</span>
  <input type="date" aria-label="First name" class="form-control" name="issue_start_date" id='issue_start_date'>
   <span class="input-group-text btn btn-secondary">-</span>
  <input type="date" aria-label="Last name" class="form-control"  name="issue_end_date" id='issue_end_date' onchange="getByDateRange()">
</div>
  </div>
   <div class="col-md-4 col-sm-12 col-xs-12 form-group" >
   <div class="input-group mb-3">
  <div class="input-group-prepend">
    <button class="btn btn-secondary" type="button">Issued From</button>
  </div>
  <select class="custom-select" id="inputGroupSelect03" aria-label="Example select with button addon" onchange="getByLabSent(this.value)">
    <option value=""></option>
  @foreach ($laboratories as $lab)
   @if($lab->id!=auth()->user()->laboratory_id )
   
       <option value="{{$lab->id}}">{{$lab->lab_name}}</option>
       @endif
   @endforeach
  </select>
</div>
  </div>
   <div class="col-md-3 col-sm-12 col-xs-12 form-group" hidden>
   <div class="input-group mb-3">
  <div class="input-group-prepend">
    <button class="btn btn-secondary" type="button">Inventory Area</button>
  </div>
  <select class="custom-select" id="inputGroupSelect03" aria-label="Example select with button addon">

      <option value="1">Store</option>
      <option value="2">Section</option>

  </select>
</div>
  </div>

   <div class="col-md-3 col-sm-12 col-xs-12 form-group" id="re" hidden >
    <label for="exampleInputEmail1">Requisition Number</label>
    <input type="text" class="form-control" id="req_number" name="req_number">
  </div>

  
     <div class="col-md-3 col-sm-12 col-xs-12 form-group"  id="sup" hidden>
         <label for="exampleInputPassword1">Supplier</label>
                                                 <div class="input-group mb-3">
  <select class="form-control" id="inputGroupSelect02" name="supplier" style="width: 75%" >
   
    <option value=""></option>
  
  
  </select>

</div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $('#inputGroupSelect02').select2({
 placeholder: 'Select  Supplier',
  width: 'resolve',
   
    });
   
     
     
});
</script>
  
  <div class="col-md-3 col-sm-12 col-xs-12 form-group" hidden>
    <label for="exampleInputPassword1">Invoice Number</label>
    <input type="text" class="form-control" id="exampleInputPassword1" name="supplier_invoice_number">
  </div>
   <div class="col-md-3 col-sm-12 col-xs-12 form-group" hidden>
    <label for="exampleInputPassword1">GRN Number</label>
    <input type="text" class="form-control" id="grn_number"  name="grn_number" >
  </div>
</div>

<!---table!-->

 
  

</form>

      </div>
   
 


  <div class="col-sm-12">
     <br>
  
    
          <div class="card">
            <h5 class="card-title"><strong>Items </strong></h5>
          <div class="card-body">
          <nav class="navbar navbar-expand-lg  navbar-light bg-primary" hidden>

  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      
         
    <a type="button" class="btn shadow-none  btn-lg" style="outline: none !important; box-shadow: none;color:white"  href="#"><i class="fa fa-check"></i> Accept</a>
      </li>
     
      <li class="nav-item">
         
    <button type="button" class="btn shadow-none  btn-lg" style="outline: none !important; box-shadow: none;color:white"><i class="fa fa-eye"></i> View</button>
      </li>
       <li class="nav-item">
         
    <button type="button" class="btn shadow-none  btn-lg" style="outline: none !important; box-shadow: none;color:white"><i class="fa fa-print"></i> Print</button>
      </li>
    
      <li class="nav-item dropdown" >
         <a class="nav-link dropdown-toggle text-lg" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-file"></i>   Export To
        </a>
         <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="#">PDF</a>
          <a class="dropdown-item" href="#">Excel</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Print</a>
        </div>
      </li>
    
    </ul>
  </div>
</nav>
   

        
        <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm" id="issue_items_table"  width="100%">
<thead class="thead-light">
    <tr>
       
    
         <th width="5%"> #</th>
          <th width="20%">Issue Date</th>
          <th width="30%">Issue From</th>
          <th width="20%">Issue To</th>
            <th width="20%">Action</th>
           
  </thead>
</table>
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
            @endsection

            @push('js')
                       <script src="{{asset('assets/admin/js/inventory/issues/get-issued_to.js') }}"></script>
                <script src="{{asset('assets/admin/js/inventory/issues/approve-issued.js') }}"></script>

@endpush