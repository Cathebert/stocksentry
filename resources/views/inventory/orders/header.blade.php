<ul class="nav navbar-right panel_toolbox" style="float:right" hidden>
        <li>
            <a class="card-header-color"  href="{{ route('inventory.all') }}">
             <i class="fa fa-list  "></i> Orders</a>
        </li>
      

    </ul>
    <div class="clearfix"></div>
 
  <ul id="myTab" class="nav nav-tabs " role="tablist">
     <li role="presentation" class="@if(Request::segment(2)=='view-order')active @ else @endif" role="presentation" >
        <a class="nav-link @if(Request::segment(2)=='view-order')active @ else @endif" role="tab" type="button" href="{{route('view.orders')}}"><strong>New Orders </strong></a>
        </li>


   <li  role="presentation" class="@if(Request::segment(2)=='received-orders')active @ else @endif" role="presentation"  >
    <a class="nav-link @if(Request::segment(2)=='received-orders')active @ else @endif" role="tab" type="button"   href="{{route('received.orders')}}"><strong>Received Orders </strong></a>
  </li>
  
</ul>
