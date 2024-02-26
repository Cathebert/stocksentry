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
       
      

    </ul>
    @endif
    <div class="clearfix"></div>
 
  <ul id="myTab" class="nav nav-tabs " role="tablist">
     <li role="presentation" class="@if(Request::segment(2)=='requisition-report')active @ else @endif" role="presentation" >
        <a class="nav-link @if(Request::segment(2)=='requisition-report')active @ else @endif" role="tab" type="button" href="{{route('lab_manager_requisition.report')}}"><strong>Store Orders</strong></a>
        </li>

  
  <li role="presentation" class="@if(Request::segment(2)=='supplier_order_report')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='supplier_order_report')active @ else @endif"   role="tab" type="button"  href="{{route('lab_manager_report.supplier-order')}}"><strong>Supplier Orders</strong></a>
  </li>
 
   
 
</ul>
