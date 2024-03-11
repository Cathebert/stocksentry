<ul class="nav navbar-right panel_toolbox" style="float:right" hidden>
        <li>
            <a class="card-header-color"  href="{{ route('inventory.all') }}">
             <i class="fa fa-list  "></i> All Inventory</a>
        </li>
      

    </ul>
    <div class="clearfix"></div>
 
  <ul id="myTab" class="nav nav-tabs " role="tablist">
     <li role="presentation" class="@if(Request::segment(2)=='cold_new-receipt')active @ else @endif" role="presentation" >
        <a class="nav-link @if(Request::segment(2)=='cold_new-receipt')active @ else @endif" role="tab" type="button" href="{{route('cold.new_receipt')}}"><strong>New Receipt </strong></a>
        </li>

  <li  role="presentation" class="@if(Request::segment(2)=='cold-received')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='cold-received')active @ else @endif" role="tab" type="button"   href="{{route('cold.all-received')}}"><strong>All Receipts </strong></a>
  </li>
  <li  role="presentation" class="@if(Request::segment(2)=='cold-received-status')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='cold-received-status')active @ else @endif" role="tab" type="button"   href="{{route('cold.received-checklist')}}"><strong>Received Checkist</strong></a>
  </li>
</ul>
