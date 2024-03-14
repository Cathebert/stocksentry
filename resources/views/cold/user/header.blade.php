
    <div class="clearfix"></div>
 
  <ul id="myTab" class="nav nav-tabs " role="tablist">
     <li role="presentation" class="@if(Request::segment(2)=='cold_profile')active @ else @endif" role="presentation" >
        <a class="nav-link @if(Request::segment(2)=='cold_profile')active @ else @endif" role="tab" type="button" href="{{route('cold.profile')}}"><strong>User Profile</strong></a>
        </li>

  <li  role="presentation" class="@if(Request::segment(2)=='cold_password')active @ else @endif" >
    <a class="nav-link @if(Request::segment(2)=='cold_password')active @ else @endif" role="tab" type="button"   href="{{route('cold.password')}}"><strong>Change Password</strong></a>
  </li>


  <li role="presentation" class="@if(Request::segment(2)=='cold_signature')active @ else @endif">
    <a class="nav-link @if(Request::segment(2)=='cold_signature')active @ else @endif"   role="tab" type="button"  href="{{route('cold.signature')}}"><strong>Change Signature</strong></a>
  </li>
 
</ul>
