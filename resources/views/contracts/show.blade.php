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
               
                       <button  class="btn btn-primary" role="button" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus"></i>
                            Add </button>
                  
                </div>
            </div>
           

        </div>
        <hr>
        
  <div class="col-sm-12">
         @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif 
    <div class="card">

      <div class="card-body">
         
        <h5 class="card-title"> <strong> Search by</strong></h5>

<br>
        <form method="post" id="form_id">
          @csrf
         <input type="hidden" id="contract_url" value="{{route('contract.load')}}"/>
        <input type="hidden" id="shoW_modal" value="{{route('contract.show_modal')}}"/>
        <input type="hidden" id="view_contract" value="{{route('contract.view')}}"/>
        <input type="hidden" id="edit_contract" value="{{route('contract.edit')}}"/>
        <input type="hidden" id="update_contract" value="{{route('contract.update')}}"/>
        <input type="hidden" id="filter" value="{{route('contract.filter')}}"/>
        <input type="hidden" id="delete_contract" value="{{route('contract.delete')}}"/>
       
            <div class="row">
    <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
  <div class="input-group">
  <span class="input-group-text btn btn-secondary">Contract #:</span>
  <input type="text" aria-label="First name" class="form-control"  name="contract_number" id="contract_id" oninput="filterByNumber(this.value)">
  
</div>
  </div>
   
   

  <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
  <div class="input-group">
  <span class="input-group-text btn btn-secondary">Contract Name:</span>
  <input type="text" aria-label="First name" class="form-control" name="contract_name" id="contract_id" oninput="filterByName(this.value)">
  
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
	<div class="dropdown" style="text-align:right" >
  <button class="dropdown-toggle btn btn-outline-primary btn-sm" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
  <i class="fa fa-share"> Export As</i>
</button>

  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
  
      <a  class="dropdown-item" href="{{route('contract.download')}}"    id="contract_export" ><i class="fa fa-file-pdf"></i>  PDF File</a>
      <hr>
    <a class="dropdown-item" href="{{route('contract.excel')}}"  id="report_download_excel"  ><i class="fa fa-file-excel"></i>  Excel file</a>
    <hr>
   
    
  </div>
</div>
               
       <h5 class="card-title"><strong> Contract  List </strong></h5>
        <div class="table-responsive">
        <table class="table table-sm table-striped" id="contract"  width="100%">
<thead class="thead-light">
    <tr>
       
         <th width="5%"> #</th>
         <th width="5%">Contract #</th>
          <th width="10%">Contract Name</th>
          <th width="15%">Contract Description</th>
            <th width="10%">Contract Start </th>
              <th width="5%">Frequency</th>
               <th width="5%">Unit</th>
          <th width="5%">Contract End </th>
          <th width="5%">Contract Type</th>
            <th width="5%">Supplier /Contractor</th>
           <th width="10%">Status </th>
            <th width="50%">View/Update</th>
            <th width="10%">Edit/Delete</th>
</tr>
           
  </thead>
  
</table>
      </div>
       </div>
   
</div>
</div>
</div>
         <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-lg" role="document">
    <div class="modal-content">
     <div class="modal-header">

        <h5 class="modal-title">Add Contract</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button><br>
      </div>
      <div class="modal-body">
<div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
       @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif 
        <h5 class="card-title"> <strong>Contracts</strong></h5>
        <form method="post" id="contract_form" action="{{route('contract.save')}}">
          @csrf
          <input type="hidden" class="form-control" id="sub_url" value="{{route('contract.save')}}">
    
 

  
 
<hr></br>
  <div class="row">
<div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Contract  Name</label>
    <input type="text" class="form-control" id="contract_name" name="contract_name">
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Contract  Number</label>
    <input type="text" class="form-control" id="contract_number" name="contract_number">
  </div>
   <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Contract Description</label>
    <input type="text" class="form-control" id="contract_descr" name="contract_desc">
  </div>
   <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Contract Start Date</label>
    <input type="date" class="form-control" id="contract_startdate" name="contract_startdate">
  </div>

    

   <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Frequency</label>
    <input type="number" class="form-control" id="contract_frequency" name="contract_frequency" min="1">
  </div>
<div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1"> Unit</label>
    <select class="form-control" id="cont_unit" name="cont_unit" style="width: 75%" onchange="calculateEndDate(this.value)" >
      <option value="0"></option>
    <option value="1">Month</option>
     <option value="2">Year</option>

   
  </select>
  </div>

  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Contract End Date</label>
    <input type="date" class="form-control" id="contract_enddate" name="contract_enddate" readonly>
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1"> Contract Type</label>
    <select class="form-control" id="cont_type" name="cont_type" style="width: 75%" onchange="contractType(this.value)">

  
     <option value="1">Supplier</option>
   <option value="2">Service</option>

   
  </select>
   <!--a class="btn btn-outline-primary" type="button" id="add" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus"></i></a-->
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group" id="show_supplier">
    <label for="exampleInputPassword1"> Supplier</label>
    <select class="form-control" id="supplier" name="supplier" style="width: 75%" >
 <option value="" selected></option>
   @foreach ($suppliers as $supplier )
     <option value="{{$supplier->id}}">{{ $supplier->supplier_name }}</option>
   @endforeach

   
  </select>
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group" id="contractor" hidden>
    <label for="exampleInputPassword1">Contractor</label>
    <input type="text" class="form-control" id="contractor_name" name="contractor_name" >
  </div>
<h6 > <strong > Contract Expiry Reminder</strong> </6>
    <div class="form-group row" id="hello">
    <label for="staticEmail" class="col-sm-3 col-form-label ">Select Email(s)</label>
    <div class="col-sm-9">
     <select class="form-control" id="contract_list" style="width: 100%" name="employee_involved[]" multiple required>
                                   
                                    @foreach($users as $user)
                                
                                        <option value="{{$user->id}}">{{$user->email}}</option>
                                    @endforeach
                                </select>
    </div>
  </div>




</div>
<script type="text/javascript">
  $(document).ready(function() {
    $('#contract_list').select2({
 placeholder: 'Select Email ',
      allowClear: true,
  dropdownParent: $('#exampleModal .modal-content')
   
    });
    
    
   
});
</script>


<script type="text/javascript">
  $(document).ready(function() {
    $('#supplier').select2({
 placeholder: 'Select Supplier',
      allowClear: true,
  dropdownParent: $('#exampleModal .modal-content')
   
    });
    
    
   
});
</script>
  <button type="submit" class="btn btn-primary" id="submit" style="float:right">Save</button>
</form>
      </div>
    </div>
  </div>


    </div>
    </div>
    </div>      
</div>
 <div class="modal" tabindex="-1" id="inforg" role="dialog" >
  <div class="modal-dialog modal-xl" role="document" >
    <div class="modal-content" id="receive_item">

  </div>        
</div>

</div>
<script type="text/javscript">
function calculateEndDate(value){
   const start=$('#contract_startdate').val();
   if(!start){
    $('#contract_startdate').focus();
    return;
   }
    const frequency=$('#contract_frequency').val();
    if(!frequency){
      $('#contract_frequency').focus();
    }
  if(value==1){
   
const a = dayjs(start);
const b = a.add(frequency, 'M')
const c=dayjs(b).format('YYYY-MM-DD')
$('#contract_enddate').val(c);
  }
   if(value==2){
   
const a = dayjs(start);
const b = a.add(frequency, 'y')
const c=dayjs(b).format('YYYY-MM-DD')
$('#contract_enddate').val(c);
  }
}

function contractType(value){
  if(value==2){
    document.getElementById('show_supplier').hidden=true
     document.getElementById('contractor').hidden=false
    

  }
  else{
     document.getElementById('show_supplier').hidden=false
     document.getElementById('contractor').hidden=true
  }
}
 
</script>

            @endsection

    @push('js')
      
       

    <script src="{{asset('assets/admin/js/inventory/contract.js') }}"></script>
 
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
   
<script>dayjs().format()</script>
   @endpush