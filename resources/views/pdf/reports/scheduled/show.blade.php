 @extends('layouts.main')
@section('title','Scheduled Reports')
@section('content')
<div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">System</a></li>
    <li class="breadcrumb-item active" aria-current="page">Scheduled</li>
  </ol>
</nav>


<div class="row" >
  <div class="col-sm-12">
     <br>
      <div class="dropdow" style="text-align:right" >
  <button  class="btn btn-primary" role="button" data-toggle="modal" data-target="#exampleModal" >
  <i class="fa fa-plus"> Schedule</i>
</button>

</div>
      <div class="card">
          
        
       
   <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Schedule Report</h5>
   
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button><br>
         
      </div>
       
      <div class="modal-body">
 <form method="post"  action="{{route('scheduled.save')}}" id="schedule_report_form" name="schedule_report_form">
  @csrf
  <div class="form-group row">
    <label for="staticEmail" class="col-sm-3 col-form-label text-danger">Frequency *</label>
    <div class="col-sm-6">
     <select class="form-control" name="frequency" id="frequency" onchange="changeText(this.value)">
  <option value="1">Weekly</option>
  <option value="2" selected>Monthly</option>
  <option value="3">Quarterly</option>
  <option value="3">Yearly</option>
</select>
    </div>
    <label for="staticEmail" class="col-sm-3 col-form-label" id="report_infor">Report will be generated and sent on a monthly basis.</label>
  </div>
  <div class="form-group row">
    <label for="staticEmail" class="col-sm-3 col-form-label text-danger">Report Type *</label>
    <div class="col-sm-6">
     <select class="form-control" name="report_type" id="report_type" >
  <option value="1">Consumption Report</option>
  <option value="2">Stock Level Report</option>
  <option value="3">Requisition Report</option>
  <option value="4">Stock Disposal Report</option>
  <option value="5">Issue Report</option>
</select>
    </div>
    
  </div>
  <div class="form-group row">
    <label for="inputPassword" class="col-sm-3 col-form-label text-danger">Start date *</label>
    <div class="col-sm-6">
      <input type="date" class="form-control" id="inputPassword"  value="{{date('Y-m-d')}}" name="start_date" required>
    </div>
  </div>
  <h6 > <strong > Recipient Details </strong> </6>
    <div class="form-group row">
    <label for="staticEmail" class="col-sm-3 col-form-label text-danger">Emails *</label>
    <div class="col-sm-9">
     <select class="form-control" id="email_list" style="width: 50%" name="employee_involved[]" multiple  required>
                                   
                                    @foreach($users as $user)
                                
                                        <option value="{{$user->id}}">{{$user->email}}</option>
                                    @endforeach
                                </select>
    </div>
  </div>
  <script type="text/javascript">
  $(document).ready(function() {
    $('#email_list').select2({
 placeholder: 'Select  ',
      allowClear: true,
  dropdownParent: $('#exampleModal .modal-content')
   
    });
    
    
   
});



</script>
    <div class="form-group row">
    <label for="staticEmail" class="col-sm-3 col-form-label">Attach as</label>
    <div class="col-sm-6">
     <div class="form-check">
  <input class="form-check-input" type="radio" name="attach_as" id="exampleRadios1" value="1" checked>
  <label class="form-check-label" for="exampleRadios1">
    PDF
  </label>
</div>
<div class="form-check">
  <input class="form-check-input" type="radio" name="attach_as" id="exampleRadios2" value="2">
  <label class="form-check-label" for="exampleRadios2">
    Excel
  </label>
</div>

    </div>
  </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" >Save changes</button>
      </div>
    </div>
  </div>
</div>

</form>




<!-------end modal-------------------->

       
		<div class="card-body">
            <input type="hidden" id="scheduled_url" value="{{route('scheduled.load')}}"/>
       <input type="hidden" id="deactivate" value="{{route('scheduled.deactivate')}}"/>
     
            <h5 class="card-title"><strong>Scheduled Reports </strong></h5>
        <div class="table-responsive">
        <table class="table table-sm table-striped" id="scheduled_list"  width="100%">
<thead class="thead-light">
    <tr>
         <th width="5%">#</th>
          <th width="20%"> Report Name</th>
           <th width="20%">Frequency</th>
            <th width="20%">Scheduled By</th>
         <th width="20%"> Next Schedule On</th>
          <th width="20%">Type</th>
          <th width="20%">Recipient(s)</th>
           <th width="10%">File Format</th>
            <th width="10%">Status</th>
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
       <script src="{{asset('assets/admin/js/inventory/reports/scheduled.js') }}"></script>
  
  
  
@endpush