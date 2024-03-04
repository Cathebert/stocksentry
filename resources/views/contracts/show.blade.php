 @extends('layouts.main')
@section('title','Contracts')
@push('style')
   
@endpush
@section('content')
 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Contracts</li>
  </ol>
</nav>

 

<div class="row" >
   <div class="title_right" style="margin-right:30px; padding-right:30px">
                <div class="form-group pull-right top_search">
               
                        <a href="#" class="btn btn-primary" onclick="showContractAdd()"><i class="fa fa-plus"></i>
                            Add </a>
                  
                </div>
            </div>
        </div>
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong> Search by</strong></h5>

<br>
        <form method="post" id="form_id">
          @csrf
         <input type="hidden" id="contract_url" value="{{route('contract.load')}}"/>
            <input type="hidden" id="shoW_modal" value="{{route('contract.show_modal')}}"/>
            <div class="row">
    <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
  <div class="input-group">
  <span class="input-group-text btn btn-secondary">Contract #:</span>
  <input type="text" aria-label="First name" class="form-control"  name="contract_name" id="contract_id">
  
</div>
  </div>
   
   

  <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
  <div class="input-group">
  <span class="input-group-text btn btn-secondary">Contract Name:</span>
  <input type="text" aria-label="First name" class="form-control" name="contract_name" id="contract_id">
  
</div>
  </div>
  
     <div class="col-md-3 col-sm-12 col-xs-12 form-group"  id="sup" hidden>
         <label for="exampleInputPassword1">Supplier</label>
                                                 <div class="input-group mb-3">
  <select class="form-control" id="inputGroupSelect02" style="width: 75%" >
   
    <option value=""></option>
   @foreach ($suppliers as $supplier)
       <option value="{{$supplier->id}}">{{$supplier->supplier_name}}</option>
   @endforeach
  
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
    <input type="text" class="form-control" id="grn_number"  >
  </div>
</div>
<hr></br>
<!---table!-->

 
  

</form>

      </div>
    </div>
  </div>



  <div class="col-sm-12">
     <br>
  
      <div class="card">
          
        
          
   

       
		<div class="card-body">
       <h5 class="card-title"><strong> Contract  List </strong></h5>
        <div class="table-responsive">
        <table class="table table-sm table-striped" id="contract"  width="100%">
<thead class="thead-light">
    <tr>
       
     <th width="5%"> #</th>
         <th width="5%">Contract #</th>
          <th width="10%">Contract Name</th>
          <th width="30%">Contract Description</th>
            <th width="10%">Contract Start </th>
          <th width="10%">Contract End </th>
          <th width="10%">Sub. Type</th>
            <th width="10%">Supplier</th>
           <th width="10%">Status </th>
            <th width="10%">Action</th>
			</tr>
           
  </thead>
  
</table>
      </div>
       </div>
   
</div>
</div>
</div>
         <div class="modal" tabindex="-1" id="inforg" role="dialog" >
  <div class="modal-dialog modal-lg" role="document" >
    <div class="modal-content" id="receive_item">

    </div>
    </div>
    </div>      
</div>

            @endsection

    @push('js')
      
       

              <script src="{{asset('assets/admin/js/inventory/contract.js') }}"></script>
 
   
   @endpush