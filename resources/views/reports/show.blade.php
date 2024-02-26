 @extends('layouts.main')
@section('title','Stock Inventory')
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Reports</a></li>
    <li class="breadcrumb-item active" aria-current="page">Available</li>
  </ol>
</nav>



<div class="row">
  <div class="col">

  <div class="list-group">
  <a href="#" class="list-group-item list-group-item-action active">
   Inventory
  </a>
  <a href="{{route('report.expiry')}}" class="list-group-item list-group-item-action">Expiry Report</a>
 
  <a href="{{route('report.consumption')}}" class="list-group-item list-group-item-action">Consumption Usage Report</a>
  <a href="{{route('report.stock-level')}}" class="list-group-item list-group-item-action">Stock Levels Report</a>
    <a href="{{route('reports.item_out_of_stock')}}" class="list-group-item list-group-item-action">Items out of Stock</a>
     <a href="#" class="list-group-item list-group-item-action" hidden>Variance Report</a>
     <a href="#" class="list-group-item list-group-item-action">Stock Disposal Summary</a>
   
</div>

  </div>

  <div class="col">


<div class="list-group ">
  <a href="#" class="list-group-item list-group-item-action active">
   Movements
  </a>
  <a href="{{route('issue.report')}}" class="list-group-item list-group-item-action">Issue to Report</a>
  <a href="{{route('requisition.report')}}" class="list-group-item list-group-item-action">Requisition Report</a>
  <a href="{{route('report.supplier-order')}}" class="list-group-item list-group-item-action"> Orders to Supplier Report</a>
 
</div>


  </div>
    <br>
      <br>
  <div class="w-100"></div>
  <br>
      
  <div class="col">

  <div class="list-group">
  <a href="#" class="list-group-item list-group-item-action active">
    Activity
  </a>
  <a href="#" class="list-group-item list-group-item-action">System Mails</a>
  <a href="#" class="list-group-item list-group-item-action">Activity Logs</a>

</div>

  </div>
  <div class="col">

  <div class="list-group">
  <a href="#" class="list-group-item list-group-item-action active">
    General
  </a>
  <a href="#" class="list-group-item list-group-item-action">Supplier Report</a>
  <a href="#" class="list-group-item list-group-item-action">ABC classification</a>
 
</div>

  </div>
</div>

   
          
</div>
            @endsection

 @push('js')
       <script src="{{asset('assets/admin/js/inventory/inventory.js')}}"> </script>
 
   <script src="{{asset('assets/admin/js/inventory/stock_take.js')}}"> </script>
  
@endpush