@if(Request::segment(2)=='lab-stocktake')
<ul class="nav navbar-right panel_toolbox" style="float:right">
        <li>

            <a class="card-header-color"  href="{{ route('lab_stock.view_history') }}">
             <i class="fa fa-list  "></i> Stock Taken History</a>
        </li>
      

    </ul>
    @endif
    @if(Request::segment(2)=='lab_consumption')
<ul class="nav navbar-right panel_toolbox" style="float:right">
        <li>

            <a class="card-header-color"  href="{{ route('lab_consumption.history') }}">
             <i class="fa fa-th  "></i> Consumption History</a>
        </li>
      

    </ul>
    @endif
    <div class="clearfix"></div>
 
  <ul id="myTab" class="nav nav-tabs " role="tablist">
     <li role="presentation" class="@if(Request::segment(2)=='lab-bincard')active @ else @endif" role="presentation" >
        <a class="nav-link @if(Request::segment(2)=='lab-bincard')active @ else @endif" role="tab" type="button" href="{{route('lab.bincard_inventory')}}"><strong>Bin Card</strong></a>
        </li>

  <li  role="presentation" class="@if(Request::segment(2)=='lab_consumption')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='lab_consumption')active @ else @endif" role="tab" type="button"   href="{{route('lab.showupdate-consumption')}}"><strong>Update Consumption</strong></a>
  </li>
  <li role="presentation" class="@if(Request::segment(2)=='lab-stocktake')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='lab-stocktake')active @ else @endif"   role="tab" type="button"  href="{{route('lab.showstocktake')}}"><strong>Stock Taking </strong></a>
  </li>
  <li  role="presentation" class="@if(Request::segment(2)=='lab-stock-forecasting')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='lab-stock-forecasting')active @ else @endif"  type="button" role="tab" href="{{route('lab.showstock-forecasting')}}"><strong>Stock Forecasting & Order </strong></a>
  </li>
   <li  role="presentation" class="@if(Request::segment(2)=='lab-adjustments')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='lab-adjustments')active @ else @endif"  type="button" role="tab" href="{{route('lab.showstock-adjustment')}}"><strong>Stock Adjustment </strong></a>
  </li>
 <li class="@if(Request::segment(2)=='lab-disposal')active @ else @endif" role="presentation">
     <a class="nav-link @if(Request::segment(2)=='lab-disposal')active @ else @endif"   type="button" role="tab" href="{{route('lab.show-disposal')}}"><strong>Stock Disposal </strong></a>
  </li>
</ul>
