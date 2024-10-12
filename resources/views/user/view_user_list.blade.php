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
				<div class="dropdown" style="text-align:right" >
  <button class="dropdown-toggle btn btn-outline-primary btn-sm" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
  <i class="fa fa-share"> Export User List As</i>
</button>

  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
  
      <a  class="dropdown-item" href="#"    id="user_pdf" ><i class="fa fa-file-pdf"></i>  PDF File</a>
      <hr>
    <a class="dropdown-item" href="#"  id="user_excel"  ><i class="fa fa-file-excel"></i>  Excel file</a>
    <hr>
   
   
    
  </div>
  &nbsp;&nbsp;&nbsp;
   @if (auth()->user()->authority==1)
                <button type="button" class="btn btn-danger"  style="border-radius: 4px;" onclick="deletedUsers()" ><i class="fa fa-trash"></i> Deleted Users</button>

          @endif
  </div>
 
  	
		<form method="post" id="users_form">
                @csrf
                <input type="hidden" id="download_url" value="{{route('user.download')}}"/>
                <input type="hidden" id="filter_user" value="{{route('user.filter')}}"/>
                
                <input type="hidden" id="deleted_users" value="{{route('user.deleted')}}"/>
               <div class="row">
  
     <div class="col-md-3 col-sm-12 col-xs-12 form-group"  id="sup">
         <label for="exampleInputPassword1">Laboratory</label>
                                                 <div class="input-group mb-3">
  <select class="form-control" id="inputGroupSelect02" name="lab" style="width: 75%"  onchange="filterUser(this.value)">
   
    <option value="-1">All</option>
   @foreach ($labs as $lab)
       <option value="{{$lab->id}}">{{$lab->lab_name}}</option>
   @endforeach
  
  </select>

</div>

</div>


  

<!---table!-->


  

</form>

  <hr></br>
         
	
            <input type="hidden" id="user_list_url" value="{{route('user.load')}}"/>
       <input type="hidden" id="edit_modal" value="{{route('user.edit')}}"/>
       <input type="hidden" id="delete_user" value="{{route('user.destroy')}}"/>
        <input type="hidden" id="reset_user" value="{{route('user.reset')}}"/>
     
            <h5 class="card-title" style="text-align:left"><strong>User List </strong></h5>
              

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