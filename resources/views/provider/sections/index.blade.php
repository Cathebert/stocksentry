 @extends('provider.layout.main')
@section('title','Sections Management')
@push('style')
     
@endpush
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('moderator.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Sections</a></li>
    <li class="breadcrumb-item active" aria-current="page">Available</li>
  </ol>
</nav>

<div class="row">
@if(!empty($sectionlist) && count($sectionlist))
                        <!-- Earnings (Monthly) Card Example -->
                    @foreach($sectionlist as $section)
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div  class="ad-random">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                               {{$section->section_name}}</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
<script type="text/javascript">
                        var months = ["card border-left-primary shadow h-100 py-2", 
                        "card border-left-secondary shadow h-100 py-2", 
                        "card border-left-info shadow h-100 py-2",
                         "card border-left-success shadow h-100 py-2",
                          "card border-left-warning shadow h-100 py-2", 
                          "card border-left-danger shadow h-100 py-2", 
                          "card border-left-dark shadow h-100 py-2"];

var random = Math.floor(Math.random() * months.length);
$('.ad-random').addClass(months[random])
                        </script>
                    @endforeach


                    
@endif
                        <!---------end row------->
</div>

<div class="row">

                        <!-- Area Chart -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Product</h6>
                                    <div class="dropdown no-arrow">
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
                                    <div class="chart-area">
                                        <canvas id="myAreaChart"></canvas>
                                    </div>
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
                                    <div class="mt-4 text-center small">
                                         <span class="mr-2">
                                            <i class="fas fa-circle text-danger"></i>Expired
                                        </span>
                                        <span class="mr-2">
                                            <i class="fas fa-circle " style="color:#ff6e07"></i> Expire in 30 days
                                        </span>
                                        <span class="mr-2">
                                            <i class="fas fa-circle"  style="color:#9c7b0e"></i> Expire in 60 days
                                        </span>
                                          <span class="mr-2">
                                            <i class="fas fa-circle " style="color:#849c0e"></i> Expire in 90 days
                                        </span>
                                          <span class="mr-2">
                                            <i class="fas fa-circle text-success"></i> Good
                                        </span>
                                       
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

                    

</div>


 </div>
             </div>
           <input type="hidden" id="top_used" value="{{route('stats.area')}}" />
<input type="hidden" id="inventory_health" value="{{route('stats.pie')}}" />
<input type="hidden" id="get_details" value="{{route('stats.detail_modal')}}"/>
            @endsection
           