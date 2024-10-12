 @extends('cold.layout.main')
@section('title','Consumption Report')
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('cold.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{route('cold_reports.show')}}">Reports</a></li>
    <li class="breadcrumb-item active" aria-current="page">Consumption Report</li>
  </ol>
</nav>
   <!-- Page Heading -->
   
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Consumption Report</h1>
                         <div class="dropdow" style="text-align:right" >
 

</div>

                    </div>
<div class="dropdown" style="text-align:right" >
  <button class="dropdown-toggle btn btn-outline-primary btn-sm" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
  <i class="fa fa-upload"> Export As</i>
</button>

  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
  
    <a class="dropdown-item" href=""  id="report_download"  ><i class="fa fa-download"></i> PDF File</a>
    <a class="dropdown-item" href="" id="report_download_excel"><i class="fa fa-share"></i>  Excel file</a>
    <hr>
   
    
  </div>
</div>
                   <div class="row" >
      <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Filters </strong></h5>
<form method="post" id="expiry_form">
          @csrf
          
  <input type="hidden" class="form-control" id="consumption_report" value="{{route('lab_manager_report.consumption_table')}}">
  <input type="hidden" class="form-control" id="get_details" value="{{route('lab_manager_report.loadmore')}}"/>
  <input type="hidden" class="form-control" id="filter" value="{{route('lab_manager_report.consumption-filter')}}"/>
  <input type="hidden" class="form-control" id="frequency_change" value="{{route('change_frequency')}}"/>
  <input type="hidden" class="form-control" id="generate_report" value="{{route('lab_manager_report.consumptiondownload')}}"/>        
            <div class="row">
    <div class="col-md-4 col-sm-12 col-xs-12 form-group"  >
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <label class="input-group-text" for="period">Period</label>
    <span class="input-group-text"><i class="fa fa-calendar-check" aria-hidden="true"></i></span>
  </div>
  <select class="custom-select" id="period" name="period" onchange="getSelected(this.value)">
     <option value="-1" selected>All</option>
    <option value="0" > Today</option>
    <option value="1">Yesterday</option>
    <option value="2">This Week</option>
    <option value="3">This Month</option>
    <option value="4">This Quarter</option>
    <option value="5">This Year</option>
    <option value="6">Previous Week</option>
    <option value="7">Previous Month</option>
    <option value="8">Previous Quarter</option>
    <option value="9">Previous Year</option>
    <option value="10">Custom</option>
</select>

</div>
<script type="text/javascript">
 $('#period').select2({
 placeholder: 'Select Period ',
      allowClear: true,

   
    });
     

</script>
  </div>
  

  <!----custom field---->
 <div class="col-md-4 col-sm-12 col-xs-12 form-group" id="custom" hidden>
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <span class="input-group-text">Custom</span>
    <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
  </div>
  <input type="date" class="form-control" aria-label="start" name="start" id="start" placeholder="start">
   <span class="input-group-text">-</span>
   <input type="date" class="form-control" aria-label="start" name="end" id="end" onchange="getLastDate()">
</div>

</div>
  <!----end--->
    <div class="col-md-4 col-sm-12 col-xs-12 form-group"  hidden>
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <label class="input-group-text" for="period">Location</label>
    <span class="input-group-text"><i class="fa fa-home" aria-hidden="true"></i></span>
  </div>
  <select class="custom-select" id="period" name="lab" onchange="getSelected()">
    <option value="-1" selected> All</option>
      @foreach ($laboratories as $lab)
   @if($lab->id!=0)
       <option value="{{$lab->id}}">{{$lab->lab_name}}</option>
       @endif
   @endforeach
</select>

</div>

</form>





      </div>

<!-- Content Row -->
                    <div class="row" >

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-4 mb-4" hidden>
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Quantity Expiring</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><a  style="text-decoration:none" id="quantity"> {{$quantity??'0' }} </a></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-server fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>



     <div class="col-xl-3 col-md-4 mb-4" hidden>
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Value of Expiring quantity</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="value">{{ $value??"0" }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-money fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                                <!---- table start ---->
               <div class="table-responsive">
        <table class="table table-bordered table-striped" id="consumption_table" width="100%">
<thead class="thead-light">
    <tr>
      
       <th scope="col"></th>
     <th scope="col">Item Name</th>
        <th scope="col">Catalog #</th>
        <th scope="col">Unit Issue</th>
          <th scope="col">Consumed</th>
        
        
      
    </tr>
  </thead>
  <tbody>
</table>
      </div>





<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Schedule Report</h5>
   
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button><br>
         
      </div>
          <span >Report Name: <i>Consumption_{{date('Y-m-d')}}</i></span>
      <div class="modal-body">
 <form method="post"  action="{{route('report.schedule_report',['type'=>'consumption'])}}" id="schedule_report_form" name="schedule_report_form">
  @csrf
  <div class="form-group row">
    <label for="staticEmail" class="col-sm-3 col-form-label text-danger">Frequency *</label>
    <div class="col-sm-6">
     <select class="form-control" name="frequency" id="frequency" onchange="changeFrequency(this.value)">
 <option value="0">Daily</option>
  <option value="1">Weekly</option>
  <option value="2">Monthly</option>
  <option value="3">Quarterly</option>
  <option value="4">Yearly</option>
</select>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputPassword" class="col-sm-3 col-form-label text-danger">Start date *</label>
    <div class="col-sm-6">
      <input type="date" class="form-control" id="start_date"  value="{{date('Y-m-d')}}" name="start_date" required readonly>
    </div>
  </div>

  <div class="form-group row">
    <label for="inputPassword" class="col-sm-3 col-form-label text-danger">Time *</label>
    <div class="col-sm-6">
      <input type="time" class="form-control" id="time"  value="" name="time" required>
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





</div>
            @endsection

 @push('js')
       <script src="{{asset('assets/moderator/reports/consumption.js')}}"> </script>
  
@endpush