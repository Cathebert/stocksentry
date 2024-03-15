<div class="modal-header">
        <h5 class="modal-title">Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
<div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Contracts</strong></h5>
        <form method="post" id="supplier_form" action="{{route('contract.save')}}">
          @csrf
          <input type="hidden" class="form-control" id="supplier_url" value="{{route('contract.save')}}">
    
 

  
 
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

<div class="col-md-4 col-sm-12 col-xs-12 form-group" id="contractor">
    <label for="exampleInputPassword1">Contractor</label>
    <input type="text" class="form-control" id="contractor_name" name="contractor_name" >
  </div>


</div>


  <button type="submit" class="btn btn-primary" id="submit" style="float:right">Save</button>
</form>
      </div>
    </div>
  </div>



</div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Subscription Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{route('sub.type')}}">
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Name:</label>
            <input type="text" class="form-control" id="recipient-name" name="sub_name">
          </div>
          <div class="form-group">
            <label for="message-text" class="col-form-label">Description:</label>
            <textarea class="form-control" id="message-text" name="description"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Send message</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">

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

  }
  else{
     document.getElementById('show_supplier').hidden=false
  }
}
  </script>