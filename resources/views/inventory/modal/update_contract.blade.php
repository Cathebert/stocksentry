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
        <h5 class="card-title"> <strong>Contracts</strong></h5>
        <form method="post" id="contract_form" action="{{route('contract.keepup')}}">
          @csrf
          <input type="hidden" class="form-control" id="sub_url" value="{{route('contract.keepup')}}">
          <input type="hidden" class="form-control" id="id" name="contract_id" value="{{$id}}" />
   <input type="hidden" class="form-control" id="supplier_id" name="supplier_id" value="{{$contract->supplier_id}}" />

  
 
<hr></br>
  <div class="row">
<div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Contract  Name</label>
    <input type="text" class="form-control" id="contract_name" name="contract_name" value="{{$contract->contract_name}}" >
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Contract  Number</label>
    <input type="text" class="form-control" id="contract_number" name="contract_number" value="{{$contract->contract_number}}" >
  </div>
   <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Contract Description</label>
    <input type="text" class="form-control" id="contract_descr" name="contract_desc" value="{{$contract->contract_description}}">
  </div>
   <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Contract Start Date</label>
    <input type="date" class="form-control" id="c_startdate" name="contract_startdate" value="{{$contract->contract_startdate}}" readonly>
  </div>
<div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Frequency</label>
    <input type="number" class="form-control" id="c_frequency" name="contract_frequency" value="{{$contract->frequency}}" min="1">
  </div>
 <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1"> Unit</label>
    <select class="form-control" id="c_unit" name="cont_unit" style="width: 75%"  onchange="changeExpiryDate(this.value)">
    
       <option value="1" {{(!empty($contract->contract_unit) &&$contract->contract_unit =='1') ? "Selected" : "" }}>Month</option>
         <option value="2" {{(!empty($contract->contract_unit) &&$contract->contract_unit =='2') ? "Selected" : "" }}>Year</option>
  </select>
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Contract End Date</label>
    <input type="date" class="form-control" id="c_enddate" name="contract_enddate" value="{{$contract->contract_enddate}}" onchange="changeContractDate()">
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Contract Next Update</label>
    <input type="date" class="form-control" id="c_nextdate" name="contract_nextdate"  readonly>
  </div>
  



</div>


  <button type="submit" class="btn btn-primary" id="submit" style="float:right">Save</button>
</form>
      </div>
    </div>
  </div>



</div>
</div>

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
 const start=$('#c_enddate').val();
  
    const frequency=$('#c_frequency').val();
   
 let contract_unit=$('#c_unit').val();  
  console.log('ContractUnit'+contract_unit)
  console.log('end Date'+start);
   console.log("frequency"+frequency);
  if(contract_unit==1){
   
const a = dayjs(start);
const b = a.add(frequency, 'M')
const c=dayjs(b).format('YYYY-MM-DD')
$('#c_nextdate').val(c);
  }
   if(contract_unit==2){
   
const a = dayjs(start);
const b = a.add(frequency, 'y')
const c=dayjs(b).format('YYYY-MM-DD')
$('#c_nextdate').val(c);
  }
  
  function changeExpiryDate(value){
  const end_date=$('#c_enddate').val();
  console.log(value)
    const frequency_period=$('#c_frequency').val();
    if(!frequency_period){
      $('#c_frequency').focus();
    }
 
  if(value==1){
  
const d = dayjs(end_date);
const m= d.add(frequency_period, 'M')
const g=dayjs(m).format('YYYY-MM-DD')
$('#c_nextdate').val(g);
 console.log(value+':'+g)
  }
   if(value==2){
   
const k = dayjs(start);
const l= k.add(frequency, 'y')
const u=dayjs(l).format('YYYY-MM-DD')
$('#c_nextdate').val(u);
 console.log(value+':'+u)
  }
  }
  function changeContractDate(){
  const value=$('#c_unit').val();
    const end_date=$('#c_enddate').val();
  console.log(value)
    const frequency_period=$('#c_frequency').val();
    if(!frequency_period){
      $('#c_frequency').focus();
    }
 
  if(value==1){
  
const d = dayjs(end_date);
const m= d.add(frequency_period, 'M')
const g=dayjs(m).format('YYYY-MM-DD')
$('#c_nextdate').val(g);
 console.log(value+':'+g)
  }
   if(value==2){
   
const k = dayjs(start);
const l= k.add(frequency, 'y')
const u=dayjs(l).format('YYYY-MM-DD')
$('#c_nextdate').val(u);
 console.log(value+':'+u)
  }
  }
  </script>