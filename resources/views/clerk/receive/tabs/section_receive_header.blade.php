<ul class="nav navbar-right panel_toolbox" style="float:right" hidden>
        <li>

            <a class="card-header-color"  href="{{ route('inventory.all') }}">
             <i class="fa fa-list  "></i> All Inventory</a>
        </li>
      

    </ul>
    <div class="clearfix"></div>
 
  <ul id="myTab" class="nav nav-tabs " role="tablist">
     <li role="presentation" class="@if(Request::segment(1)=='section_new-receipt')active @ else @endif" role="presentation" >
        <a class="nav-link @if(Request::segment(1)=='section_new-receipt')active @ else @endif" role="tab" type="button" href="{{route('section.new_receipt')}}"><strong> New Receipt</strong></a>
        </li>

  <li  role="presentation" class="@if(Request::segment(1)=='section_all_receipts')active @ else @endif">
    <a class="nav-link @if(Request::segment(1)=='section_all_receipts')active @ else @endif" role="tab" type="button"   href="{{route('section.all_received')}}"><strong>All Receipts</strong></a>
  </li>
  <li  role="presentation" class="@if(Request::segment(2)=='section-received-checklist')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='section-received-checklist')active @ else @endif" role="tab" type="button"   href="{{route('section.received-checklist')}}"><strong>Received Checklist</strong></a>
  </li>
 

</ul>
