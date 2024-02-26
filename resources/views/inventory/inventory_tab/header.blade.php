@if(Request::segment(2)=='stock-take')
<ul class="nav navbar-right panel_toolbox" style="float:right">
        <li>

            <a class="card-header-color"  href="{{ route('stock.view_history') }}">
             <i class="fa fa-list  "></i> Stock Taken History</a>
        </li>
      

    </ul>
    @elseif (Request::segment(2)=='stock-forecasting')
    <ul class="nav navbar-right panel_toolbox" style="float:right">
        <li>

            <a class="card-header-color"  href="{{route('view.orders')}}">
             <i class="fa fa-server  "></i> View Orders</a>
        </li>
         </ul>
    @else
    <ul class="nav navbar-right panel_toolbox" style="float:right">
        <li>

            <a class="card-header-color"  href="{{ route('inventory.all') }}">
             <i class="fa fa-list  "></i> All Inventory</a>
        </li>
      

    </ul>
    @endif
    <div class="clearfix"></div>
 
  <ul id="myTab" class="nav nav-tabs " role="tablist">
     <li role="presentation" class="@if(Request::segment(2)=='inventory')active @ else @endif" role="presentation" >
        <a class="nav-link @if(Request::segment(2)=='inventory')active @ else @endif" role="tab" type="button" href="{{route('inventory.bincard')}}"><strong>Bin Card</strong></a>
        </li>

  <li  role="presentation" class="@if(Request::segment(2)=='update-consumption')active @ else @endif" hidden>
    <a class="nav-link @if(Request::segment(2)=='update-consumption')active @ else @endif" role="tab" type="button"   href="{{route('inventory.showupdate-consumption')}}"><strong>Update Consumption</strong></a>
  </li>
  <li role="presentation" class="@if(Request::segment(2)=='stock-take')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='stock-take')active @ else @endif"   role="tab" type="button"  href="{{route('inventory.showstocktake')}}"><strong>Stock Taking </strong></a>
  </li>
  <li  role="presentation" class="@if(Request::segment(2)=='stock-forecasting')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='stock-forecasting')active @ else @endif"  type="button" role="tab" href="{{route('inventory.showstock-forecasting')}}"><strong>Stock Forecasting & Order </strong></a>
  </li>
   <li  role="presentation" class="@if(Request::segment(2)=='stock-adjustment')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='stock-adjustment')active @ else @endif"  type="button" role="tab" href="{{route('inventory.showstock-adjustment')}}"><strong>Stock Adjustment </strong></a>
  </li>
 <li class="@if(Request::segment(2)=='stock-disposal')active @ else @endif" role="presentation">
     <a class="nav-link @if(Request::segment(2)=='stock-disposal')active @ else @endif"   type="button" role="tab" href="{{route('inventory.show-disposal')}}"><strong>Stock Disposal </strong></a>
  </li>
</ul>
