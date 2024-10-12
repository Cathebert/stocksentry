<div class="clearfix"></div>
 
  <ul id="myTab" class="nav nav-tabs " role="tablist">
     <li role="presentation" class="@if(Request::segment(2)=='user_requisition-report')active @ else @endif" role="presentation" >
        <a class="nav-link @if(Request::segment(2)=='user_requisition-report')active @ else @endif" role="tab" type="button" href="{{route('user_requisition.report')}}"><strong>Store Orders</strong></a>
        </li>

  
  <li role="presentation" class="@if(Request::segment(2)=='user_supplier_order_report')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='user_supplier_order_report')active @ else @endif"   role="tab" type="button"  href="{{route('user_report.supplier-order')}}"><strong>Supplier Orders</strong></a>
  </li>
 
   
 
</ul>