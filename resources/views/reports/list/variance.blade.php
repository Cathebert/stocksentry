@extends('layouts.main')
@section('title','Stock Inventory')
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{route('reports.show')}}">Reports</a></li>
    <li class="breadcrumb-item active" aria-current="page">StockTake Variance</li>
  </ol>
</nav>
   <!-- Page Heading -->

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Variance Report</h1>
                         <div class="dropdow" style="text-align:right" >


</div>

                    </div>
<div class="dropdown" style="text-align:right" hidden >
  <button class="dropdown-toggle btn btn-outline-primary btn-sm" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
  <i class="fa fa-upload"> Export As</i>
</button>

  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

    <a class="dropdown-item" href="{{route('report.download',['action'=>'download'])}}" id="download"><i class="fa fa-download"></i> PDF File</a>
    <a class="dropdown-item" href="{{route('report.download',['action'=>'excel'])}}" id="excel"><i class="fa fa-share"></i>  Excel file</a>
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

               <input type="hidden" class="form-control" id="variance_report" value="{{route('report.variance_table')}}">

                 <input type="hidden" class="form-control" id="stock_take_details" value="{{route('report.variance_details')}}">
  <input type="hidden" class="form-control" id="download_url" value="{{route('report.variance_download')}}"/>
  <input type="hidden" id="variance_lab" name="variance_lab" value="{{route('report.variance_lab')}}"/>



            <div class="row">
    <div class="col-md-4 col-sm-12 col-xs-12 form-group"  >
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <label class="input-group-text" for="period">Location</label>
    <span class="input-group-text"><i class="fa fa-home" aria-hidden="true"></i></span>
  </div>
  <select class="custom-select" id="period" name="lab" onchange="getSelectedLab(this.value)">
    <option value="-1" selected> All</option>
      @foreach ($laboratories as $lab)
      @if($lab->id==0 || $lab->id==99)

@else
       <option value="{{$lab->id}}">{{$lab->lab_name}}</option>
 @endif

   @endforeach
</select>

</div>
  </div>

     <div class="col-md-6 col-sm-12 col-xs-12 form-group" hidden>
  <div class="input-group">
  <span class="input-group-text">Expiry Days Range:</span>
   <span class="input-group-text"><i class="fa fa-sort-numeric-asc" aria-hidden="true"></i></span>
  <select class="custom-select" id="days_range" name="period" onchange="getSelectedRange()">
   <option value="-1" selected>All</option>

    <option value="2"> >1 to < 30 days</option>
      <option value="3" > > 30  to < 60 days </option>
        <option value="4"> > 60  to < 90 days</option>

</select>
</div>

  </div>

</form>





      </div>

<!-- Content Row -->
                    <div class="row">

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
        <table class="table table-sm" id="variance_table" width="100%">
<thead class="thead-light">
    <tr>


       <th scope="col"></th>
     <th scope="col">Stock Date</th>
       <th scope="col">Lab Name</th>
        <th scope="col">Supervised By</th>
        <th scope="col">Approved By</th>
         <th scope="col">Action</th>

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
          <span >Report Name: <i>Inventory About to Expire_{{date('Y-m-d')}}</i></span>
      <div class="modal-body">
 <form method="post"  action="{{route('report.schedule_report',['type'=>'expiry'])}}" id="schedule_report_form" name="schedule_report_form">
  @csrf
  <div class="form-group row">
    <label for="staticEmail" class="col-sm-3 col-form-label text-danger">Frequency *</label>
    <div class="col-sm-6">
     <select class="form-control" name="frequency" id="frequency">
  <option value="1">Weekly</option>
  <option value="2">Monthly</option>
  <option value="3">Quarterly</option>
  <option value="3">Yearly</option>
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





</div>
            @endsection

 @push('js')
       <script src="{{asset('assets/admin/js/inventory/reports/variance.js')}}"> </script>

@endpush
