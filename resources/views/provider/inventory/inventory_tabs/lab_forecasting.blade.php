   @extends('provider.layout.main')
@section('title','Lab Inventory')
@push('style')
   
@endpush
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
     <li class="breadcrumb-item"><a href="{{route('moderator.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{route('lab.bincard_inventory')}}">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Stock Forecasting</li>
  </ol>
</nav>
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Forecasting and Order</strong></h5>
  @include('provider.inventory.inventory_tabs.lab_inventory_header')  
<!--content start-->
  <div class="row" >
   
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        <input type="hidden" id="forecast" value="{{route('inventory.getforecast')}}"/>
        <input type="hidden" id="load_ajusted" value="{{route('forecast.load')}}"/>
        <input type="hidden" id="run_forecast" value="{{route('forecast.generate')}}"/>
        <input type="hidden" id="order_url" value="{{route('forecast.order')}}"/>
       
 <div class="row" >
 <form class="form-inline"  id="form_forecast">
  <div class="form-group mb-2">
  <label for="lab_id">Quantiy of Stock( in months)  </label>
  <input type="number" aria-label="First name" id="order" class="form-control" name="order" value=4 min='1' >
  </div>
  <div class="form-group mx-sm-3 mb-2" id="time_lead"  >
   <label for="lab_id">Lead Time  </label>
  <input type="number" aria-label="First name" id="lead" class="form-control" name="lead" value=1  min="1" >
  </div>

  <div class="form-group mx-sm-3 mb-2">
   <label for="lab_id">Order #:  </label>
  <input type="text" aria-label="First name" id="order_number" class="form-control" name="order_number" value={{$order}} readonly>
  </div>
  <button type="submit" class="btn btn-primary mb-2"  onclick="LoadInventory()">Load Inventory</button>
</form>
</div>
      <br>
      

   
    
  </div></div>

       <h5 class="card-title"><strong>Forecast</strong></h5>

        @if(auth()->user()->authority==2)
        <div class="dropdown" style="text-align:right">
     
    <a  type="button" href="#"  id="view_orders" class="btn btn-info">   <i class="fa fa-check"></i>Approve Orders</a>
      </div>
      @endif
        <div id='real_table'></div>
        <div class="table-responsive">
        <table class="table table-sm" id="forecast_table">
    <thead class="thead-light">
    <tr>
     <th scope="col">#</th>
        <th scope="col">Item Name</th>
           <th scope="col">Code</th>
      <th scope="col">Supplier</th>
      <th scope="col">UOM</th>
      <th scope="col">On Hand</th>
      <th scope="col">Av. Consumption</th>
      <th scope="col">Forecasted</th>
      <th scope="col">Order</th>
     
      
    </tr>
  </thead>
  <tbody>
</table>
 </div>
   </div>
   
     </div>
       <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="PlaceOrder()">Place Order</button>
       
      </div>
    </div>
  </div>
  <br>
 </div>
  

  </div>  
    <div class="modal" tabindex="-1" id="inforg" role="dialog" >
  <div class="modal-dialog modal-xl" role="document" >
    <div class="modal-content" id="view_item_datails">
           
    
            @endsection

    @push('js')
    <script src="{{asset('assets/admin/js/inventory/inventory_forecasting.js')}}"> </script>
   
   @endpush