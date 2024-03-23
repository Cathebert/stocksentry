 @extends('sectionhead.layout.main')
@section('title','Change Password')
@push('style')
  
@endpush
@section('content')

  

<!--content start-->

<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>User Profile Information</strong></h5>
      <br>
   @include('sectionhead.user.header')  
    <div class="clearfix"></div>  
<div class="row">
   
                                        
                                        <form id="change_password" name="change_password" role="form" method="POST"
                                              action="{{route('changepassword')}}" onsubmit="return verifyPassword()">
                                            @csrf
                                            <input type="hidden" id="change_url" value="{{route('changepassword')}}" />
                                            <div class="row form-group">
                                                <div class="col-md-12">
                                                    <label for="current_password">Current Password <span
                                                            class="text-danger">*</span></label>
                                                    <input type="password" id="old" name="old" class="form-control"
                                                           autocomplete="off" oninput="clearOldInput()">
                                                             <span class="text-danger" id="old_error"></span> 
</div>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-12">
                                                    <label for="new_password">New Password <span
                                                            class="text-danger">*</span></label>
                                                    <input type="password" id="new" name="new" class="form-control"
                                                           autocomplete="off">
                                                     <span class="text-danger" id="new_error"></span>       
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-md-12">
                                                    <label for="confirm_password">Confirm Password <span
                                                            class="text-danger">*</span></label>
                                                    <input type="password" id="confirm" name="confirm"
                                                           class="form-control" autocomplete="off" >
                                                            <span class="text-danger" id="confirm_error"></span>
                                                </div>
                                            </div>
                                            <div class="form-group pull-right">
                                                <div class="col-md-12 col-sm-6 col-xs-12">
                                                    <br>

                                                    <button type="submit" name="btn_add_change"  id="change" class="btn btn-primary">
                                                        <i class="fa fa-save" id="show_loader"></i>&nbsp;Change Password
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                        </div>
<div>

                                    </div>
	</div>

	<script type="text/javascript">

   const form = document.querySelector("form");
 
        // Prevent form submission on button click
        document
            .getElementById("change")
            .addEventListener("click", function (event) {
                event.preventDefault();
                let old_pass=$('#old').val();
let new_pass=$('#new').val()
let confirm_pass=$('#confirm').val();
if(!old_pass){
    $('#old_error').text('Enter your old password')
    $('#old').focus();
    return
}
if(old_pass.length<8){
$('#old_error').text('The password should be at least 8 characters')
 $('#old').focus();
return
            }
     if(old_pass.length>15){
$('#old_error').text('Password length must not exceed 15 characters')
 $('#old').focus();

return
            }   
            //new
            if(!new_pass){
    $('#new_error').text('Enter your new password')
     $('#new').focus();
    return
}
if(new_pass.length<8){
$('#new_error').text('The password should be at least 8 characters')
  $('#new').focus();
return
            }
     if(new_pass.length>15){
$('#old_error').text('Password length must not exceed 15 characters')
  $('#new').focus();
return
            } 
            //end new
 



 if(!confirm_pass){
    $('#confirm_error').text('Confirm Pass')
     $('#confirm').focus();
    return
}

if(confirm_pass!==new_pass){
$('#confirm_error').text('The passwords don not match')
 $('#confirm').focus();

 return
            }

            sendConfirmed()
    

            });
 

 function clearOldInput(){
   

     $('#old_error').text('')
 }
  function clearNewInput(){
   

     $('#new_error').text('')
 }

  function clearConfirmInput(){
   

     $('#confirm_error').text('')
 }

 function  sendConfirmed(){
    
    let change_url=$('#change_url').val()
     $.ajaxSetup({
             headers: {
                 "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
             },
         });
            $.ajax({
                method: "POST",
                dataType:"JSON",
                url: change_url,
                data: $('#change_password').serialize(),
                success: function(data) {
               
              // Welcome notification
               // Welcome notification
               if(data.error==false){
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
                   $('#change_password')[0].reset();
            }
            else{
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
            },
            error:function(error){

            }
            });
  
        }
       

        </script>

</div>
            @endsection

@push('js')


       
@endpush