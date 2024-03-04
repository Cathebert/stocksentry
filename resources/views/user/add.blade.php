 @extends('layouts.main')
@section('title','Create User')
@section('content')


 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">User</li>
  </ol>
</nav>
  
<div class="row" >
  
  <div class="col-sm-9">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Add User</strong></h5>
        <form method="post" id="form_id" action="{{route('user.create')}}" autocomplete="nope">
          @csrf
          <input type="hidden" class="form-control" id="post_url" value="{{route('user.create')}}">
     <input type="hidden" id="get_sections" value="{{route('lab.sections')}}"/>
 
        
<script type="text/javascript">

</script>
  
 
<hr></br>
  <div class="row">
      <div class="col-md-4 col-sm-12 col-xs-12 form-group" id="labs"  >
    <label for="exampleInputPassword1">Laboratory</label>
    <select class="form-control" id="lab_id" name="lab_id" style="width: 75%" onchange="getLabName(this.value)">

 
    @foreach ( $lab as $lab )
     <option value="{{$lab->id}}" >{{ $lab->lab_name }}</option> 
    @endforeach
    
   
  </select>
  </div>
  
  <input type="hidden" name="extension"  id="ext" value=".st"/>
<div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">First Name</label>
    <input type="text" class="form-control" id="first_name" name="first_name">
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Last Name</label>
    <input type="text" class="form-control" id="last_name" name="last_name" oninput="updateUserName(this.value)">
  </div>

    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">User Name</label>
    <div class="input-group mb-3">
  <input type="text" class="form-control"  name="username" id="username" aria-label="username" aria-describedby="basic-addon2">
  <div class="input-group-append" >
    <span class="input-group-text btn btn-secondary"  id="extension" >.st</span>
  </div>
</div>
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Email</label>
    <input type="text" class="form-control" id="email" name="email">
   
  </div>
<div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Phone Number</label>
    <input type="text" class="form-control" id="phone_number" name="phone_number" max=10>
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group" id='not_coldroom'>

    <label for="exampleInputPassword1">User Role</label>
    <select class="form-control" id="user_role" name="user_type" style="width: 75%" >

   
  <option value="1">Store Admin</option>
  <option value="2">Manager</option>
  <option value="3">Lab User</option>
  <option value="4">ColdRoom User</option> 
   
  </select>
  </div>


  <div class="col-md-4 col-sm-12 col-xs-12 form-group" id='cold_room' hidden>

    <label for="exampleInputPassword1">User Role</label>
    <select class="form-control" id="user_role" name="user_type" style="width: 75%" >

   
  
 
   <option value="4"> ColdRoom User</option>
  
   
  </select>
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Position</label>
    <input type="text" class="form-control" id="user_position" name="user_position">
  </div>
    @if(auth()->user()->authority==2)
   <div class="col-md-4 col-sm-12 col-xs-12 form-group">

    <label for="exampleInputPassword1">User Role</label>
    <select class="form-control" id="user_role" name="user_type" style="width: 75%" >

   
  
   <option value="2"> Manager</option>
  <option value="3">User</option>
    
   
  </select>
  </div>
  @endif





</div>
<button type="submit" class="btn btn-primary" id="submit" style="float:right">Submit</button>
  <button type="reset" class="btn btn-secondary" id="reset" style="float:left">Reset</button>
</form>
      </div>
    </div>
  </div>

  <div class="col-sm-3" hidden>
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Expiry Stats</h5>
       <h4 class="small font-weight-bold">Good Condition </h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar " role="progressbar" style="width: 100%"
                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"id="good">1</div>
                                    </div>
                                    <h4 class="small font-weight-bold">Warning condition </h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%"
                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" id="warning">2</div>
                                    </div>
                                    <h4 class="small font-weight-bold">Expired </h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 100%"
                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" id="expired">3</div>
                                    </div>
      </div>
    </div>
  </div>
</div>

<br>
  <div class="col-sm-12" hidden >
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"><strong>Items Added Today</strong></h5>
        <div class="table-responsive">
        <table class="table table-sm">
<thead class="thead-light">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Description</th>
      <th scope="col">Unit</th>
      <th scope="col">Quantity</th>
       <th scope="col">Batch #</th>
        <th scope="col">Expiry Date</th>
    </tr>
  </thead>
  <tbody>
    <tr hidden>
      <th scope="row">1</th>
      <td>item</td>
      <td>Otto</td>
      <td>@mdo</td>
       <td>@mdo</td>
        <td>@mdo</td>
    </tr>
    <tr hidden>
      <th scope="row">2</th>
      <td>Jacob</td>
      <td>Thornton</td>
      <td>@fat</td>
       <td>@mdo</td>
        <td>@mdo</td>
    </tr>
    <tr hidden>
      <th scope="row">3</th>
      <td>Larry</td>
      <td>the Bird</td>
      <td>@twitter</td>
       <td>@mdo</td>
        <td>@mdo</td>
    </tr>
  </tbody>
</table>
      </div>
       </div>
    </div>
  </div>
<script type="text/javascript">
function searchEmail(value){
var email_exist_url="{{route('checkEmailExists')}}"
 document.getElementById("searching").hidden = false;
 $.ajax({
           method: "GET",
           dataType: "json",
           url: email_exist_url,
           data: {
               email: value,
           },

           success: function (data) {
               if (data.available == true) {
                   
                  $("#searching").html('<span class="text-danger">'+data.message+'</span>');
                    document.getElementById("searching").hidden = false;
                    $('#username').focus();
               } else {
                   
                   document.getElementById("searching").hidden = true;
               }
           },
           error: function (jqXHR, textStatus, errorThrown) {
               // console.log(get_case_next_modal)
               alert("Error " + errorThrown);
           },
       });
}
</script>
    <!-- /.container-fluid -->

            </div>
          
            @endsection
            @push('js')
       <script src="{{asset('assets/admin/js/user/add_user.js') }}"></script>
  
  
   <script src="{{asset('assets/admin/js/repeter/repeater.js') }}"></script>
         <script src="{{asset('assets/admin/js/inventory/repeater.js') }}"></script>
@endpush