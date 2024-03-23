 @extends('layouts.main')
@section('title','User List')
@section('content')
<div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">User</a></li>
    <li class="breadcrumb-item active" aria-current="page">All Users</li>
  </ol>
</nav>


<div class="row" >
  <div class="col-sm-12">
     <br>
  
      <div class="card">
          
        
          
   

       
		<div class="card-body">
            <input type="hidden" id="user_list_url" value="{{route('user.load')}}"/>
       <input type="hidden" id="edit_modal" value="{{route('user.edit')}}"/>
       <input type="hidden" id="delete_user" value="{{route('user.destroy')}}"/>
        <input type="hidden" id="reset_user" value="{{route('user.reset')}}"/>
     
            <h5 class="card-title"><strong>User List </strong></h5>
        <div class="table-responsive">
        <table class="table table-sm table-striped" id="user_list"  width="100%">
<thead class="thead-light">
    <tr>
         <th width="5%">#</th>
         <th width="20%">User Name</th>
          <th width="20%">First Name</th>
          <th width="20%">Last Name</th>
          <th width="20%">Email</th>
           <th width="20%">Phone</th>
            <th width="20%">Lab</th>
            <th width="20%">Location</th>
            <th width="20%">Action</th>
			</tr>
           
  </thead>
</table>
      </div>
       </div>
   
</div>
</div>
</div>
         <div class="modal" tabindex="-1" id="inforg" role="dialog" >
  <div class="modal-dialog modal-lg" role="document" >
    <div class="modal-content" id="receive_item">

    </div>
    </div>
    </div>      

            </div>
          
            @endsection
            @push('js')
       <script src="{{asset('assets/admin/js/user/users.js') }}"></script>
  
  
  
@endpush