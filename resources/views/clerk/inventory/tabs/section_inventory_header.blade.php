
    <div class="clearfix"></div>
 
  <ul id="myTab" class="nav nav-tabs " role="tablist">
     <li role="presentation" class="@if(Request::segment(1)=='section-bincard')active @ else @endif" role="presentation" >
        <a class="nav-link @if(Request::segment(1)=='section-bincard')active @ else @endif" role="tab" type="button" href="{{route('section.bincard_inventory')}}"><strong>Bin Card</strong></a>
        </li>

  <li  role="presentation" class="@if(Request::segment(1)=='section_consumption')active @ else @endif">
    <a class="nav-link @if(Request::segment(1)=='section_consumption')active @ else @endif" role="tab" type="button"   href="{{route('section.showupdate-consumption')}}"><strong>Update Consumption</strong></a>
  </li>

  <li role="presentation" class="@if(Request::segment(1)=='section-stocktake')active @ else @endif">
    <a class="nav-link @if(Request::segment(1)=='section-stocktake')active @ else @endif"   role="tab" type="button"  href="{{route('section.showstocktake')}}"><strong>Stock Taking </strong></a>
  </li>
  <li  role="presentation" class="@if(Request::segment(1)=='section-stock-forecasting')active @ else @endif" hidden>
    <a class="nav-link @if(Request::segment(1)=='section-stock-forecasting')active @ else @endif"  type="button" role="tab" href="{{route('section.showstock-forecasting')}}" hidden><strong>Stock Forecasting & Order </strong></a>
  </li>
  @if(auth()->user()->authority!=3)
   <li  role="presentation" class="@if(Request::segment(1)=='lab-adjustments')active @ else @endif">
    <a class="nav-link @if(Request::segment(1)=='section-adjustments')active @ else @endif"  type="button" role="tab" href="{{route('section.showstock-adjustment')}}"><strong>Stock Adjustment </strong></a>
  </li>
   
 <li class="@if(Request::segment(1)=='section-disposal')active @ else @endif" role="presentation">
     <a class="nav-link @if(Request::segment(1)=='section-disposal')active @ else @endif"   type="button" role="tab" href="{{route('section.show-disposal')}}"><strong>Stock Disposal </strong></a>
  </li>
   @endif
</ul>
