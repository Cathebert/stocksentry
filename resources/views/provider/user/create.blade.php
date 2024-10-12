@extends('provider.layout.main')
@section('title','Create User')
@section('content')


 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('moderator.home')}}">Home</a></li>
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
  <input type="hidden" id="check" name="check" value="0"/>
        
<script type="text/javascript">

</script>
  
 
<hr></br>
  <div class="row">
      <div class="col-md-4 col-sm-12 col-xs-12 form-group" id="labs"  >
    <label for="exampleInputPassword1">Laboratory</label>
    <select class="form-control" id="lab_id" name="lab_id" style="width: 75%" >
     <option value="{{$lab->id}}" selected>{{ $lab->lab_name }}</option> 
   
   
  </select>
  </div>
  
  <input type="hidden" name="extension"  id="ext" value="{{strtolower($lab->lab_code)}}"/>
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
    <span class="input-group-text btn btn-secondary"  id="extension" >.{{strtolower($lab->lab_code)}}</span>
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
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">

    <label for="exampleInputPassword1">User Role</label>
    <select class="form-control" id="user_role" name="user_type" style="width: 75%" >

   
  
   <option value="2"> Manager</option>
  <option value="3">User</option>
    
   
  </select>
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Position</label>
    <input type="text" class="form-control" id="user_position" name="user_position">
  </div>
   





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

     
        $('#form_id').on('click','#submit', function(e){
        var url=$('#post_url').val();

         var name = $("#first_name").val()
         var last_name = $("#last_name").val()
         var email = $("#email").val();
         var user_position = $("#user_position").val();
         var user_role = $("#user_role").val();

          
         if (!name) {
             $.alert({
                 icon: "fa fa-warning",
                 title: "Missing information!",
                 type: "orange",
                 content: "Please provide name!",
             });
             $("#first_name").focus();
            e.preventDefault();
            return;
         }
         if (!last_name) {
             $.alert({
                 icon: "fa fa-warning",
                 title: "Missing information!",
                 type: "orange",
                 content: "Please provide Last name!",
             });
             $("#last_name").focus();
          e.preventDefault();
          return;
         }
         if (!email) {
             $.alert({
                 icon: "fa fa-warning",
                 title: "Missing information!",
                 type: "orange",
                 content: "Please provide email address!",
             });
             $("#email").focus();
             e.preventDefault();
             return
         }
         if (!user_position) {
             $.alert({
                 icon: "fa fa-warning",
                 title: "Missing information!",
                 type: "orange",
                 content: "Please provide position of user!",
             });
             $("#user_position").focus();
             e.preventDefault();
             return
         }
          
            
                   $.ajaxSetup({
                       headers: {
                           "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                               "content"
                           ),
                       },
                   });
            $.ajax({
                method: "POST",
                dataType:"JSON",
                url: url,
                data: $('#form_id').serialize(),
                   beforeSend: function () {
                    ajaxindicatorstart("saving data... please wait...");
                },
                success: function(data) {
                  if(data.error==false){
                   ajaxindicatorstop();
                 $('#reset').click();
              // Welcome notification
               // Welcome notification
                toastr.options = {
                  "closeButton": true,
                  "debug": false,
                  "newestOnTop": false,
                  "progressBar": false,
                  "positionClass": "toast-top-right",
                  "preventDuplicates": false,
                  "onclick": null,
                  "showDuration": "300",
                  "hideDuration": "1000",
                  "timeOut": "5000",
                  "extendedTimeOut": "1000",
                  "showEasing": "swing",
                  "hideEasing": "linear",
                  "showMethod": "fadeIn",
                  "hideMethod": "fadeOut"
                }
                toastr["success"](data.message);
              
                }
                else{
                 ajaxindicatorstop();
                   toastr.options = {
                  "closeButton": true,
                  "debug": false,
                  "newestOnTop": false,
                  "progressBar": false,
                  "positionClass": "toast-top-right",
                  "preventDuplicates": false,
                  "onclick": null,
                  "showDuration": "300",
                  "hideDuration": "1000",
                  "timeOut": "5000",
                  "extendedTimeOut": "1000",
                  "showEasing": "swing",
                  "hideEasing": "linear",
                  "showMethod": "fadeIn",
                  "hideMethod": "fadeOut"
                }
                toastr["error"](data.message);  
                }
               
            }
           
            });
            return false;
        });
        
        function updateUserName(lname){
    let name = $("#first_name").val();
    let first = name.charAt(0);
    $('#username').val(first+""+lname);
   }
</script>
    <!-- /.container-fluid -->

            </div>
          
            @endsection
            @push('js')
       
  
  
   <script src="{{asset('assets/admin/js/repeter/repeater.js') }}"></script>
         <script src="{{asset('assets/admin/js/inventory/repeater.js') }}"></script>
@endpush