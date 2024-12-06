@extends('cold.layout.main')
@section('title','Stock Inventory')
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('cold.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{route('cold_reports.show')}}">Reports</a></li>
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


<form method="post" id="expiry_form">
          @csrf

               <input type="hidden" class="form-control" id="variance_report" value="{{route('report.load_variance_lab')}}">

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









</div>
            @endsection

 @push('js')
       <script src="{{asset('assets/admin/js/inventory/reports/cold/variance.js')}}"> </script>

@endpush
