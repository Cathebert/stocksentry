
    <div class="clearfix"></div>
 
  <ul id="myTab" class="nav nav-tabs " role="tablist">
     <li role="presentation" class="@if(Request::segment(2)=='profile')active @ else @endif" role="presentation" >
        <a class="nav-link @if(Request::segment(2)=='profile')active @ else @endif" role="tab" type="button" href="{{route('lab.userprofile')}}"><strong>User Profile</strong></a>
        </li>

  <li  role="presentation" class="@if(Request::segment(2)=='password')active @ else @endif" >
    <a class="nav-link @if(Request::segment(2)=='password')active @ else @endif" role="tab" type="button"   href="{{route('lab.password')}}"><strong>Change Password</strong></a>
  </li>


  <li role="presentation" class="@if(Request::segment(2)=='signature')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='signature')active @ else @endif"   role="tab" type="button"  href="{{route('lab.signature')}}"><strong>Change Signature</strong></a>
  </li>
 
</ul>
