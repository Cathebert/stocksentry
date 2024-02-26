
    <div class="clearfix"></div>
 
  <ul id="myTab" class="nav nav-tabs " role="tablist">
     <li role="presentation" class="@if(Request::segment(1)=='coldroom-bincard')active @ else @endif" role="presentation" >
        <a class="nav-link @if(Request::segment(1)=='cold-bincard')active @ else @endif" role="tab" type="button" href="{{route('cold.bincard_inventory')}}"><strong>Bin Card</strong></a>
        </li>

  <li  role="presentation" class="@if(Request::segment(1)=='cold_consumption')active @ else @endif" hidden>
    <a class="nav-link @if(Request::segment(1)=='cold_consumption')active @ else @endif" role="tab" type="button"   href="{{route('cold.showupdate-consumption')}}"><strong>Update Consumption</strong></a>
  </li>

  <li role="presentation" class="@if(Request::segment(1)=='cold-stocktake')active @ else @endif">
    <a class="nav-link @if(Request::segment(1)=='cold-stocktake')active @ else @endif"   role="tab" type="button"  href="{{route('cold.showstocktake')}}"><strong>Stock Taking </strong></a>
  </li>

  
   <li  role="presentation" class="@if(Request::segment(1)=='cold-adjustments')active @ else @endif">
    <a class="nav-link @if(Request::segment(1)=='section-adjustments')active @ else @endif"  type="button" role="tab" href="{{route('cold.showstock-adjustment')}}"><strong>Stock Adjustment </strong></a>
  </li>
   
 <li class="@if(Request::segment(1)=='cold-disposal')active @ else @endif" role="presentation">
     <a class="nav-link @if(Request::segment(1)=='cold-disposal')active @ else @endif"   type="button" role="tab" href="{{route('cold.show-disposal')}}"><strong>Stock Disposal </strong></a>
  </li>
 
</ul>
