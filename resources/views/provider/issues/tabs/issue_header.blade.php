<ul class="nav navbar-right panel_toolbox" style="float:right" hidden>
        <li>

            <a class="card-header-color"  href="{{ route('inventory.all') }}">
             <i class="fa fa-list  "></i> All Inventory</a>
        </li>
      

    </ul>
    <div class="clearfix"></div>
 
  <ul id="myTab" class="nav nav-tabs " role="tablist">
     <li role="presentation" class="@if(Request::segment(2)=='request')active @ else @endif" role="presentation" >
        <a class="nav-link @if(Request::segment(2)=='request')active @ else @endif" role="tab" type="button" href="{{route('moderator.request')}}"><strong> Item Requisitions</strong></a>
        </li>

  <li  role="presentation" class="@if(Request::segment(2)=='lab_issue')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='lab_issue')active @ else @endif" role="tab" type="button"   href="{{route('lab.issue')}}"><strong>Issue Item</strong></a>
  </li>
  <li role="presentation" class="@if(Request::segment(2)=='lab_received_issued')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='lab_receive_issued')active @ else @endif"   role="tab" type="button"  href="{{route('lab.received_issued')}}"><strong>Receive Issued</strong></a>
  </li>
 

</ul>
