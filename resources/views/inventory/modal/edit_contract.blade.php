<div class="modal-header">

        <h5 class="modal-title">Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
        <h5 class="card-title"> <strong>Edit Contract</strong></h5>
        <form method="post" id="contract_form" action="{{route('contract.save_edit') }}">
          @csrf
          <input type="hidden" class="form-control" id="sub_url" value="{{route('contract.save_edit')}}"/>
          <input type="hidden" class="form-control" id="contract_edit_id" name="contract_edit_id" value="{{$id}}"/>
    
 

  
 
<hr></br>
  <div class="row">
<div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Contract  Name</label>
    <input type="text" class="form-control" id="contract_name" name="contract_name" value="{{$contract->contract_name}}">
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Contract  Number</label>
    <input type="text" class="form-control" id="contract_number_edit" name="contract_number_edit" value="{{$contract->contract_number}}">
  </div>
   <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Contract Description</label>
    <input type="text" class="form-control" id="contract_descr_edit" name="contract_desc_edit" value="{{$contract->contract_description}}">
  </div>
   <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Contract Start Date</label>
    <input type="date" class="form-control" id="contract_startdate_edit" name="contract_startdate_edit" value="{{$contract->contract_startdate}}">
  </div>

    

   <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Frequency</label>
    <input type="number" class="form-control" id="contract_frequency_edit" name="contract_frequency_edit" min="1" value="{{$contract->frequency}}">
  </div>
<div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1"> Unit</label>
    <select class="form-control" id="cont_unit_edit" name="cont_unit_edit" style="width: 75%" onchange="calculateEndDateEdit(this.value)" >
      <option value="0"></option>
    <option value="1" {{(!empty($contract->contract_unit) && $contract->contract_unit ==1) ? "Selected" : "" }}>Month</option>
     <option value="2"{{(!empty($contract->contract_unit) && $contract->contract_unit ==2) ? "Selected" : "" }}>Year</option>
     

   
  </select>
  </div>

  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Contract End Date</label>
    <input type="date" class="form-control" id="contract_enddate_edit" name="contract_enddate_edit" readonly>
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1"> Contract Type</label>
    <select class="form-control" id="cont_type_edit" name="cont_type_edit" style="width: 75%" onchange="contractTypeEdit(this.value)">

  
     <option value="1" {{(!empty($contract->contract_type) && $contract->contract_type ==1) ? "Selected" : "" }}>Supplier</option>
   <option value="2"{{(!empty($contract->contract_type) && $contract->contract_type ==2) ? "Selected" : "" }}>Service</option>

   
  </select>
   <!--a class="btn btn-outline-primary" type="button" id="add" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-plus"></i></a-->
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group" id="show_supplier_edit">
    <label for="exampleInputPassword1"> Supplier</label>
    <select class="form-control" id="supplier_edit" name="supplier_edit" style="width: 75%" >
 <option value=""></option>
  @foreach($suppliers as $supplier)
     <option value="{{$supplier->id }}"{{$supplier->id==$contract->supplier_id ? 'selected': ''}}>{{ $supplier->supplier_name }}</option>
   @endforeach

   
  </select>
  </div>
 
  <div class="col-md-4 col-sm-12 col-xs-12 form-group" id="contractor_edit">
    <label for="exampleInputPassword1">Contractor</label>
    <input type="text" class="form-control" id="contractor_name_edit" name="contractor_name_edit" value="{{$contract->contractor_name??""}}"  oninput="getKeyPressed(this.value)">
 <input type ="hidden" class="form-control" id="service_name" name="service_name" >
  </div>
  
 
<h6> <strong > Contract Expiry Reminder</strong> </6>
  
    <label for="staticEmail" class="col-sm-3 col-form-label ">Select Emails(*) </label>
    <div class="col-sm-9">
     <select class="form-control" id="contract_list_edit" style="width: 100%" name="employee_involved[]" multiple required>
                                   
                                    @foreach($users as $user)
                          
                                  
                                        <option value="{{$user->id}}"{{in_array($user->id,$receipients)? 'selected':''}}>{{$user->email}}</option>
                                    
                                    @endforeach
                                </select>
    </div>
 
<div class="col-md-4 col-sm-12 col-xs-12 form-group" id="contractor_edit" hidden>
    <label for="exampleInputPassword1">Contractor</label>
    <input type="text" class="form-control" id="contractor_name_edit" name="contractor_name_edit" >
  </div>


</div>
  <button type="submit" class="btn btn-primary" id="submit" style="float:right">Save</button>
</form>
      </div>
    </div>
  </div>



</div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $('#contract_list_edit').select2({
 placeholder: 'Select Email ',
      allowClear: true,
  dropdownParent: $('#inforg')
   
    });
    
    
   
});
</script>

<script type="text/javascript">

function calculateEndDateEdit(value){
   const start_p=$('#contract_startdate_edit').val();
   if(! start_p){
    $('#contract_startdate_edit').focus();
    return;
   }
    const frequency_p=$('#contract_frequency_edit').val();
    if(!frequency_p){
      $('#contract_frequency_edit').focus();
    }
  if(value==1){
   
const h = dayjs( start_p);
const i = h.add(frequency_p, 'M')
const j=dayjs(i).format('YYYY-MM-DD')
$('#contract_enddate_edit').val(j);
  }
   if(value==2){
   
const k = dayjs( start_p);
const l= k.add(frequency_p, 'y')
const m=dayjs(l).format('YYYY-MM-DD')
$('#contract_enddate_edit').val(m);
  }
}

function contractTypeEdit(value){
  if(value==2){
    document.getElementById('show_supplier_edit').hidden=true
     document.getElementById('contractor_edit').hidden=false
     document.getElementById('service_name').value="";
     document.getElementById('contractor_name_edit').value="";

  }
  else{
     document.getElementById('show_supplier_edit').hidden=false
      document.getElementById('contractor_edit').hidden=true
  }
}
function getKeyPressed(value){
document.getElementById('service_name').value=value;
}
</script>
<div class="modal fade" id="subModal" tabindex="-1" role="dialog" aria-labelledby="subModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-titl" id="subModalLabel">Add Subscription Category</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="sub_close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{route('sub.type')}}" id="sub_form">
          <input type="hidden" id="sub_save" value="{{route('sub.type')}}" />
          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Name:</label>
            <input type="text" class="form-control" id="recipient-name" name="sub_name">
          </div>
          <div class="form-group">
            <label for="message-text" class="col-form-label">Description:</label>
            <textarea class="form-control" id="message-text" name="sub_description"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        
        <button type="button" class="btn btn-primary" id="add_subscript">Save</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">


 $(document).ready(function(){

/// initialized
 
 
  
   const start_ed=$('#contract_startdate_edit').val();
   if(!start_ed){
    $('#contract_startdate_edit').focus();
    return;
   }
   const cont_unit_edit=$("#cont_unit_edit").val();
    const frequency_ed=$('#contract_frequency_edit').val();
    if(!frequency_ed){
      $('#contract_frequency_edit').focus();
    }
  if(cont_unit_edit==1){
   
const n = dayjs(start_ed);
const k = n.add(frequency_ed, 'M')
const j=dayjs(k).format('YYYY-MM-DD')
$('#contract_enddate_edit').val(j);
  }
   if(cont_unit_edit==2){
   
const y= dayjs(start_ed);
const q = y.add(frequency_ed, 'y')
const z=dayjs(q).format('YYYY-MM-DD')
$('#contract_enddate_edit').val(z);
  }
  });
  var show_edit=$('#contractor_name_edit').val();

  
   if(show_edit){
    document.getElementById('show_supplier_edit').hidden=true
     document.getElementById('contractor_edit').hidden=false

  }
  else{
     document.getElementById('show_supplier_edit').hidden=false
      document.getElementById('contractor_edit').hidden=true
  }
  </script>