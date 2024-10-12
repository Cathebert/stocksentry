<ul class="nav navbar-right panel_toolbox" style="float:right" hidden>
        <li>

            <a class="card-header-color"  href="{{ route('inventory.all') }}">
             <i class="fa fa-list  "></i> All Inventory</a>
        </li>
      

    </ul>
    <div class="clearfix"></div>
 
  <ul id="myTab" class="nav nav-tabs " role="tablist">
     <li role="presentation" class="@if(Request::segment(2)=='requisition')active @ else @endif" role="presentation" >
        <a class="nav-link @if(Request::segment(2)=='requisition')active @ else @endif" role="tab" type="button" href="{{route('issue.requisition')}}"><strong> Item Requisitions</strong></a>
        </li>

  <li  role="presentation" class="@if(Request::segment(2)=='issue')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='issue')active @ else @endif" role="tab" type="button"   href="{{route('issue.issue')}}"><strong>Issue Item</strong></a>
  </li>
  <li role="presentation" class="@if(Request::segment(2)=='received-issued')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='received-issued')active @ else @endif"   role="tab" type="button"  href="{{route('issue.received_issued')}}"><strong>Receive Issued</strong></a>
  </li>
 

</ul>
