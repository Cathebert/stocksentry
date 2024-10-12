@if(Request::segment(2)=='user-stocktake')
<ul class="nav navbar-right panel_toolbox" style="float:right">
        <li>

            <a class="card-header-color"  href="{{ route('section.view_history') }}">
             <i class="fa fa-list  "></i> Stock Taken History</a>
        </li>
      

    </ul>
    @endif
<div class="clearfix"></div>
 
  <ul id="myTab" class="nav nav-tabs " role="tablist">
     <li role="presentation" class="@if(Request::segment(2)=='user-bincard')active @ else @endif" role="presentation" >
        <a class="nav-link @if(Request::segment(2)=='user-bincard')active @ else @endif" role="tab" type="button" href="{{route('section.bincard_inventory')}}"><strong>Bin Card</strong></a>
        </li>
@if(auth()->user()->laboratory_id!=0)
  <li  role="presentation" class="@if(Request::segment(2)=='user_consumption')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='user_consumption')active @ else @endif" role="tab" type="button"   href="{{route('section.showupdate-consumption')}}"><strong>Update Consumption</strong></a>
  </li>
@endif
  <li role="presentation" class="@if(Request::segment(2)=='user-stocktake')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='user-stocktake')active @ else @endif"   role="tab" type="button"  href="{{route('section.showstocktake')}}"><strong>Stock Taking </strong></a>
  </li>
  <li  role="presentation" class="@if(Request::segment(2)=='user-stock-forecasting')active @ else @endif" >
    <a class="nav-link @if(Request::segment(2)=='user-stock-forecasting')active @ else @endif"  type="button" role="tab" href="{{route('section.showstock-forecasting')}}" ><strong>Stock Forecasting & Order </strong></a>
  </li>

   <li  role="presentation" class="@if(Request::segment(2)=='user-adjustments')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='user-adjustments')active @ else @endif"  type="button" role="tab" href="{{route('section.showstock-adjustment')}}"><strong>Stock Adjustment </strong></a>
  </li>
   
 <li class="@if(Request::segment(2)=='user-disposal')active @ else @endif" role="presentation">
     <a class="nav-link @if(Request::segment(2)=='user-disposal')active @ else @endif"   type="button" role="tab" href="{{route('section.show-disposal')}}"><strong>Stock Disposal </strong></a>
  </li>
 
</ul>