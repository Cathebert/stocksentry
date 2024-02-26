<ul class="nav navbar-right panel_toolbox" style="float:right" hidden>
        <li>
            <a class="card-header-color"  href="{{ route('inventory.all') }}">
             <i class="fa fa-list  "></i> All Inventory</a>
        </li>
      

    </ul>
    <div class="clearfix"></div>
 
  <ul id="myTab" class="nav nav-tabs " role="tablist">
     <li role="presentation" class="@if(Request::segment(2)=='receive')active @ else @endif" role="presentation" >
        <a class="nav-link @if(Request::segment(2)=='receive')active @ else @endif" role="tab" type="button" href="{{route('admin.receive_stock')}}"><strong>New Receipt </strong></a>
        </li>

  <li  role="presentation" class="@if(Request::segment(2)=='all-received')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='all-received')active @ else @endif" role="tab" type="button"   href="{{route('admin.all-received')}}"><strong>All Receipts </strong></a>
  </li>
  
</ul>
