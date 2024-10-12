@extends('layouts.main')
@section('title','Dashboard')
@section('content')



 <div class="container-fluid">

                    <!-- Page Heading -->
                  

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Earnings (Monthly) Card Example -->
                           <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Stock In Hand</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><a href="{{route('inventory.bincard')}}" style="text-decoration:none"> {{number_format($item)??'0' }} </a></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                           <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Laboratories</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $labs??"0" }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-flask fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                          <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                 <a href="{{ route('view.orders') }}"> Pending Orders  To Supplier</a></div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><a href="{{ route('view.orders') }}">{{$to_supplier  }}</a></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Pending Requests to store -->
                             <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-secondary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                               <a href="{{ route('issue.requisition') }}"> Pending Orders  To Store</a></div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><a href="{{ route('view.orders') }}">{{ $requests }}</a></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    

                    <!-- Content Row -->

                    <div class="row">

                        <!-- Area Chart -->
                        <div class="col-xl-8 col-lg-7">
                             <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                   <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Report</a></li>
    <li class="breadcrumb-item active" aria-current="page" id="transform">Consumption Report</li>
  </ol>
</nav>
                                   
                                    <div class="dropdown " hidden>
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Filter:</div>
                                            <a class="dropdown-item" href="#">Today</a>
                                            <a class="dropdown-item" href="#">Yesterday</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">This Week</a>
                                             <a class="dropdown-item" href="#">This Month</a>
                                              <a class="dropdown-item" href="#">This Quarter</a>
                                               <a class="dropdown-item" href="#">This Year</a>
                                               <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="#">Previous Week</a>
                                                 <a class="dropdown-item" href="#">Previous Month</a>
                                                  <a class="dropdown-item" href="#">Previous Quarter</a>
                                                   <a class="dropdown-item" href="#">Previous Year</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="#">Custom</a>
                                               
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
 <div class="card-body">
                                  
                                    <div class="chart-area" style="width:100%">
                                        <canvas id="myAreaChart"></canvas>
                                        
                                    </div>

                                   
                                </div>
                                  <div class="card-footer text-muted">
<ul class="nav justify-content-start nav-fill">
  <li class="nav-item">
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <label class="input-group-text" for="report">Choose Report</label>
  </div>
  <select class="custom-select" id="reportType" onchange="getReport(this.value)">
    <option value="0" selected>Consumption Report</option>
    <option value="1">Stock Level Report</option>
    <option value="2">Requisition Report</option>
    <option value="3">Orders Report</option>
  </select>
</div>


  </li>
  <li class="nav-item">
   
  </li>
  <li class="nav-item">
    <div class="input-group mb-3" id="period_selection">
  <div class="input-group-prepend" >
    <label class="input-group-text" for="period">Select Period</label>
  </div>
  <select class="custom-select" id="period"  onchange="getPeriod(this.value)">
    <option  value="0">Today</option>
    <option value="1">Yesterday</option>
    <option value="2">This Week</option>
     <option value="3">This Month</option>
    <option value="4">This Quarter</option>
    <option value="5">This Year</option>
    <option value="6" selected>Previous Week</option>
    <option value="7">Previous Month</option>
    <option value="8">Previous Quarter</option>
    <option value="9">Previous Year</option>
    
  </select>
</div>
                            
  
  </li>
  <li class="nav-item">
   
  </li>
  <li class="nav-item">
    <div class="dropdown" hidden>
  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Save As
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <a class="dropdown-item" href="#"><i class="fa fa-file-pdf"></i>  PDF</a>
    <a class="dropdown-item" href="#"><i class="fa fa-file-excel"> </i>  Excel</a>
   
  </div>
</div>
  </li>
</ul>


 
 

  </div>
 <div class="card-footer text-muted">
    Compare Datasets
    <ul class="nav justify-content-start nav-fill">
  <li class="nav-item">
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <label class="input-group-text" for="report">Choose Report</label>
  </div>
  <select class="custom-select" id="compare" >
    <option value="0" selected>Consumption Report</option>
    <option value="1">Requisition Report</option>
    <option value="2">Orders Report</option>
  </select>
</div>


  </li>
  <li class="nav-item">
   
  </li>
  <li class="nav-item">
    <div class="input-group mb-3">
  <div class="input-group-prepend">
    <label class="input-group-text" for="from"> From</label>
  </div>
  <select class="custom-select" id="from" >
    <option  value="0">Today</option>
    <option value="1">Yesterday</option>
    <option value="2">This Week</option>
    <option value="3">This Quarter</option>
    <option value="4">This Year</option>
    <option value="5" >Previous Week</option>
    <option value="6">Previous Month</option>
    <option value="7">Previous Quarter</option>
    <option value="8">Previous Year</option>
   
  </select>
  <div class="input-group-prepend">
    <label class="input-group-text" for="to"> To</label>
  </div>
  <select class="custom-select" id="to">
    
    <option value="1">Yesterday</option>
    <option value="2">This Week</option>
    <option value="3">This Quarter</option>
    <option value="4">This Year</option>
    <option value="5" >Previous Week</option>
    <option value="6">Previous Month</option>
    <option value="7">Previous Quarter</option>
    <option value="8">Previous Year</option>

  </select>
</div>
                            
  
  </li>
  <li class="nav-item">
   
                        
 
  </li>
  <li class="nav-item">
    <div class="dropdown" hidden>
  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Save As
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <a class="dropdown-item" href="#"><i class="fa fa-file-pdf"></i>  PDF</a>
    <a class="dropdown-item" href="#"><i class="fa fa-file-excel"> </i>  Excel</a>
   
  </div>
</div>
  </li>
</ul>


 
 


  </div>
                            </div>
                            

                        </div>

                        <!-- Pie Chart -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Inventory Health</h6>
                                    <div class="dropdown no-arrow" hidden>
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
                                      <a class="" style="align:center" id="pie_load" disabled> 
  <span class="spinner-grow spinner-grow-sm"></span>
  Loading...
</a>
                                        <canvas id="myPieChart"></canvas>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Content Column -->
                        <div class="col-lg-6 mb-4">

                            <!-- Project Card Example -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Inventory Quantity Level</h6>
                                </div>
                                <div class="card-body">
                                   
                                   
                                   
                                   <div class="hstack gap-">
  <table class="table">
  <thead>
    <tr>
      <th scope="col">Low</th>
      <th scope="col">Medium</th>
      <th scope="col">High</th>
    
    </tr>
  </thead>
  <tbody>
    <tr>
      <th  id="low" class="text-danger">1</th>
      <td id="medium" class="text-warning">Mark</td>
      <td id="high" class="text-primary">Otto</td>
      
    </tr>
    </tbody>
</table>
    <br>
  
 <div class="d-flex" style="height: 200px;">
  <div class="vr"></div>
</div>
&nbsp;&nbsp;
   <div ><canvas id="myround_pie" ></canvas></div>
</div>

                                </div>
                            </div>

                            <!-- Color System -->
                            <div class="row" hidden>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-primary text-white shadow">
                                        <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Top Consumed</h6>
                                </div>
                                        <div class="card-body">
                                            Primary
                                            <div class="text-white-50 small">#4e73df</div>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-warning text-white shadow">
                                        <div class="card-body">
                                            Warning
                                            <div class="text-white-50 small">#f6c23e</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-danger text-white shadow">
                                        <div class="card-body">
                                            Danger
                                            <div class="text-white-50 small">#e74a3b</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-secondary text-white shadow">
                                        <div class="card-body">
                                            Secondary
                                            <div class="text-white-50 small">#858796</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-light text-black shadow">
                                        <div class="card-body">
                                            Light
                                            <div class="text-black-50 small">#f8f9fc</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-dark text-white shadow">
                                        <div class="card-body">
                                            Dark
                                            <div class="text-white-50 small">#5a5c69</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-lg-6 mb-4">

                            <!-- Illustrations -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Top Consumed</h6>
                                </div>
                                <div class="card-body">
                                  
                                   
                                    <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm table-hover" id="items_tabl">
<thead class="thead-light">
    <tr>
  <th scope="col">Preview</th>
      <th scope="col">Item Name</th>
     <th scope="col">Code #</th>
      <th scope="col">Quantity Consumed</th>
   
   
    </tr>
  </thead>
  <tbody>
</table>
      </div>




                                </div>
                            </div>

                            <!-- Approach -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Latest Orders</h6>
                                </div>
                                <div class="card-body">
                                                                     <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm" id="req">
    <thead class="thead-light">
    <tr>
      <th scope="col">SR #</th>
      <th scope="col">Requesting Lab</th>
      <th scope="col">Requested Date</th>
      <!--<th scope="col">Options</th>-->
   
    </tr>
  </thead>
  <tbody>
</table>
 </div>
                                    
                                </div>
                            </div>

                        </div>
                    </div>
 <div class="modal" tabindex="-1" id="inforg" role="dialog" >
  <div class="modal-dialog modal-xl" role="document" >
    <div class="modal-content" id="view_item_datails">
</div>
                </div>
                <!-- /.container-fluid -->

<input type="hidden" id="inventory_health" value="{{route('stats.pie')}}" />
<input type="hidden" id="get_details" value="{{route('stats.detail_modal')}}"/>
<input type="hidden" id="consumption-chart" value="{{route('dashboard.consumption')}}" />
<input type="hidden" id="stock-level-chart" value="{{route('dashboard.stock-level')}}"/>
<input type="hidden" id="requisition_chart" value="{{route('dashboard.requisition')}}"/>
<input type="hidden" id="orders_chart" value="{{route('dashboard.orders')}}"/>
<input type="hidden" id="period-chart" value="{{route('dashboard.period')}}"/>
<input type="hidden" id="item-chart" value="{{route('dashboard.item_details')}}"/>
<input type="hidden" id="top_consumed" value="{{route('dashboard.top_consumed')}}"/>
<input type="hidden" id="get_latest" value="{{ route('dashboard.latestOrders') }}"/>
<input type="hidden" id="view_approved_request" value="{{route('requests.view-approved')}}"/>
<input type="hidden" id="save_approved_request" value="{{route('requisition.save-approved')}}"/>

            </div>
            @endsection

       @push('js')
             <script src="{{asset('assets/js/demo/chart-pie-demo.js')}}"></script>
             <script src="{{asset('assets/admin/js/inventory/reports/dashboard/reports.js')}}"> </script>
              <script src="{{asset('assets/admin/js/inventory/reports/dashboard/consumption.js')}}"> </script>
              <script src="{{asset('assets/admin/js/inventory/issues/requisition.js') }}"></script>
            @endpush