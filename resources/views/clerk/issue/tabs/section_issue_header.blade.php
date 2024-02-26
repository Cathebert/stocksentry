<ul class="nav navbar-right panel_toolbox" style="float:right" hidden>
        <li>

            <a class="card-header-color"  href="{{ route('inventory.all') }}">
             <i class="fa fa-list  "></i> All Inventory</a>
        </li>
      

    </ul>
    <div class="clearfix"></div>
 
  <ul id="myTab" class="nav nav-tabs " role="tablist">
     <li role="presentation" class="@if(Request::segment(1)=='section_request')active @ else @endif" role="presentation" >
        <a class="nav-link @if(Request::segment(1)=='section_request')active @ else @endif" role="tab" type="button" href="{{route('section.request')}}"><strong> Item Requisitions</strong></a>
        </li>

  <li  role="presentation" class="@if(Request::segment(1)=='section_issue')active @ else @endif">
    <a class="nav-link @if(Request::segment(1)=='section_issue')active @ else @endif" role="tab" type="button"   href="{{route('section.issue')}}"><strong>Issue Item</strong></a>
  </li>
  <li role="presentation" class="@if(Request::segment(1)=='section_received_issued')active @ else @endif">
    <a class="nav-link @if(Request::segment(1)=='section_receive_issued')active @ else @endif"   role="tab" type="button"  href="{{route('section.received_issued')}}"><strong>Receive Issued</strong></a>
  </li>
 

</ul>
