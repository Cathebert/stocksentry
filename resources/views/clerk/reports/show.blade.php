@extends('clerk.layout.main')
@section('title','Stock Inventory')
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('user.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{route('user_reports.show')}}">Reports</a></li>
    <li class="breadcrumb-item active" aria-current="page">Available</li>
  </ol>
</nav>



<div class="row">
  <div class="col">

  <div class="list-group">
  <a href="#" class="list-group-item list-group-item-action active">
   Inventory
  </a>
  <a href="{{route('user_report.expired')}}" class="list-group-item list-group-item-action">Expired Items Report</a>
  <a href="{{route('user_report.expiry')}}" class="list-group-item list-group-item-action">About To Expire Report</a>
 
  <a href="{{route('user_report.consumption')}}" class="list-group-item list-group-item-action">Consumption Usage Report</a>
  <a href="{{route('user_report.stock-level')}}" class="list-group-item list-group-item-action">Stock Levels Report</a>
    <a href="{{route('user_reports.item_out_of_stock')}}" class="list-group-item list-group-item-action">Items out of Stock</a>
       <a href="{{route('user_report.variance')}}" class="list-group-item list-group-item-action" >StockTake Variance Report</a>
     <a href="{{ route('user_report.show-disposed') }}" class="list-group-item list-group-item-action">Stock Disposal Summary</a>
   
</div>

  </div>

  <div class="col">


<div class="list-group ">
  <a href="#" class="list-group-item list-group-item-action active">
   Movements
  </a>
  <a href="{{route('user_issue.report')}}" class="list-group-item list-group-item-action">Issue to Report</a>
  <a href="{{route('user_requisition.report')}}" class="list-group-item list-group-item-action">Requisition Report</a>
  <a href="{{route('user_report.supplier-order')}}" class="list-group-item list-group-item-action"> Orders to Supplier Report</a>
 
</div>


  </div>
    <br>
      <br>
  <div class="w-100"></div>
  <br>
      
  <div class="col"hidden>

  <div class="list-group" >
  <a href="#" class="list-group-item list-group-item-action active">
    Activity
  </a>
  <a href="{{route('lab.system_mails')}}" class="list-group-item list-group-item-action">System Mails</a>
  <a href="#" class="list-group-item list-group-item-action">Activity Logs</a>

</div>

  </div>
  <div class="col" hidden>

  <div class="list-group">
  <a href="#" class="list-group-item list-group-item-action active">
   Automation
  </a>
 <a href="{{route('labscheduled.show')}}" class="list-group-item list-group-item-action">View/Schedule Reports</a>

 
</div>

  </div>
</div>

   
          
</div>
            @endsection