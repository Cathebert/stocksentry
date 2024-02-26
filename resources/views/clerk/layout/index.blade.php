 @extends('clerk.layout.main')
@section('title',' Dashboard')
@section('content')
 <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Stock In Hand</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$item->item??'0' }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                       
                      
 <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                               Users</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{$users??'0' }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    

                        <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                 Requests Made</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $requests }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-comments fa-2x text-gray-300"></i>
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
    
    <option value="0">This Week</option>
      <option value="1">This Week</option>
    <option value="2">This Quarter</option>
      <option value="3">This Month</option>
    <option value="4">This Year</option>
    
   
  </select>
  <div class="input-group-prepend">
    <label class="input-group-text" for="to"> To</label>
  </div>
  <select class="custom-select" id="to" onchange="runComparison(this.value)">
    
    <option value="4">Yesterday</option>
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
    <div class="dropdown" >
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
                    <div class="row" hidden>

                        <!-- Content Column -->
                        <div class="col-lg-6 mb-4">

                            <!-- Project Card Example -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Top Used Items</h6>
                                </div>
                                <div class="card-body">
                                    <h4 class="small font-weight-bold"> <span
                                            class="float-right">20%</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: 20%"
                                            aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h4 class="small font-weight-bold">SunnySide Lab <span
                                            class="float-right">40%</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 40%"
                                            aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h4 class="small font-weight-bold">Nsanje Lab <span
                                            class="float-right">60%</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar" role="progressbar" style="width: 60%"
                                            aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h4 class="small font-weight-bold">Queens Lab<span
                                            class="float-right">80%</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 80%"
                                            aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h4 class="small font-weight-bold">Chilobwe Lab <span
                                            class="float-right">80%</span></h4>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 80%"
                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Color System -->
                            <div class="row" hidden>
                                <div class="col-lg-6 mb-4" >
                                    <div class="card bg-primary text-white shadow">
                                        <div class="card-body">
                                            Primary
                                            <div class="text-white-50 small">#4e73df</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-success text-white shadow">
                                        <div class="card-body">
                                            Success
                                            <div class="text-white-50 small">#1cc88a</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-info text-white shadow">
                                        <div class="card-body">
                                            Info
                                            <div class="text-white-50 small">#36b9cc</div>
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

                        <div class="col-lg-6 mb-4" >

                            <!-- Illustrations -->
                            <div class="card shadow mb-4" hidden>
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Illustrations</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center">
                                        <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 25rem;"
                                            src="{{asset('assets/img/undraw_posting_photo.svg')}}" alt="...">
                                    </div>
                                    <p>Add some quality, svg illustrations to your project courtesy of <a
                                            target="_blank" rel="nofollow" href="https://undraw.co/">unDraw</a>, a
                                        constantly updated collection of beautiful svg images that you can use
                                        completely free and without attribution!</p>
                                    <a target="_blank" rel="nofollow" href="https://undraw.co/">Browse Illustrations on
                                        unDraw &rarr;</a>
                                </div>
                            </div>

                            <!-- Approach -->
                            <div class="card shadow mb-4" hidden>
                                <div class="card-header py-3" >
                                    <h6 class="m-0 font-weight-bold text-primary">Development Approach</h6>
                                </div>
                                <div class="card-body">
                                    <p>SB Admin 2 makes extensive use of Bootstrap 4 utility classes in order to reduce
                                        CSS bloat and poor page performance. Custom CSS classes are used to create
                                        custom components and custom utility classes.</p>
                                    <p class="mb-0">Before working with this theme, you should become familiar with the
                                        Bootstrap framework, especially the utility classes.</p>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->
<input type="hidden" id="inventory_health" value="{{route('stats.pie')}}" />
<input type="hidden" id="get_details" value="{{route('stats.detail_modal')}}"/>

<input type="hidden" id="consumption-chart" value="{{route('dashboard.section_consumption')}}" />
<input type="hidden" id="stock-level-chart" value="{{route('dashboard.section_stock-level')}}"/>
<input type="hidden" id="requisition_chart" value="{{route('dashboard.section_requisition')}}"/>
<input type="hidden" id="orders_chart" value="{{route('dashboard.section_orders')}}"/>
<input type="hidden" id="period-chart" value="{{route('dashboard.section_period')}}"/>
<input type="hidden" id="compare_chart" value="{{route('dashboard.section_compare')}}"/>
@endsection
          @push('js')
             <script src="{{asset('assets/admin/js/inventory/reports/dashboard/reports.js')}}"> </script>
              <script src="{{asset('assets/admin/js/inventory/reports/dashboard/consumption.js')}}"> </script>
                
            @endpush     
            </div>
           