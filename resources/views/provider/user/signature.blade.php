 @extends('provider.layout.main')
@section('title','User Profile')
@push('style')
   <link href="{{ asset('assets/admin/Image-preview/dist/css/bootstrap-imageupload.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/jcropper/css/cropper.min.css') }}" rel="stylesheet">
@endpush
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.3.5/signature_pad.min.js" integrity="sha512-kw/nRM/BMR2XGArXnOoxKOO5VBHLdITAW00aG8qK4zBzcLVZ4nzg7/oYCaoiwc8U9zrnsO9UHqpyljJ8+iqYiQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
       var canvas = document.getElementById("signature-pad");

       function resizeCanvas() {
           var ratio = Math.max(window.devicePixelRatio || 1, 1);
           canvas.width = canvas.offsetWidth * ratio;
           canvas.height = canvas.offsetHeight * ratio;
           canvas.getContext("2d").scale(ratio, ratio);
       }
       window.onresize = resizeCanvas;
       resizeCanvas();

       var signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(250,250,250)'
       });

       document.getElementById("clear").addEventListener('click', function(){
        signaturePad.clear();
       })
   </script>
<script type="text/javascript">
    jQuery(document).ready(function () {
   

    $("#flash"). fadeOut(3000)
    //$("#flash").remove();

});
    </script>



<!--content start-->

<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>User Profile Information</strong></h5>
      <br>
   @include('provider.user.header')  
    <div class="clearfix"></div>  
<div class="row">
   
                                            <form id="change_signature" name="change_signature" role="form" method="POST"
                                                  enctype="multipart/form-data"
                                                  action="{{ route('update_signature')}}">
                                                @csrf
<input type="hidden" id="send_signature" value="{{ route('update_signature')}}"/>
                                                <input type="hidden" id="id" name="id" value="{{ $users->id}}">
                                                <input type="hidden" id="imagebase64" name="imagebase64">
                                                <div class="row">
                                                    <div class="col-md-4 col-sm-12 col-xs-12">

                                                        <div class="row">
                                                        
                                                            <div class="col-md-12 text-center dimage" style="margin-top:20%">
                                                                @if($users->signature!='')
                                                                 <span>CURRENT SIGNATURE</span>
                                                                    <img id="crop_image"
                                                                    src="{{ (!empty(auth()->user()->signature)) ? url('/public/upload/signatures/' . auth()->user()->signature) : asset('assets/icon/sign.png') }}"
                                                                         width='200px' height='200px'
                                                                         class="crop_image_profile"
                                                                         style=" border: 3px solid #555;"
                                                                    >
                                                                    <div class="contct-info">
                                                                        <label id="remove_crop">
                                                                            <input type="checkbox" value="Yes"
                                                                                   name="is_remove_image"
                                                                                   id="is_remove_image">&nbsp;Remove
                                                                            profile picture.
                                                                        </label>
                                                                    </div>
                                                                @else
                                                                <div></div>
                                                                    <img id="demo_profile"
                                                                         src='{{ asset('assets/icon/sign.png') }}'
                                                                         width='200px'
                                                                         height='200px'
                                                                         class="crop_image_profile"
                                                                         style=" border: 3px solid #555;"
                                                                    >

                                                                @endif


                                                                <div class="imageupload">
                                                                    <div class="file-tab">

                                                                        <div
                                                                            id="upload-demo"
                                                                            class="upload-demo"


                                                                        ></div>
                                                                        <div id="upload-demo-i"
                                                                        ></div>

                                                                        <br>
                                                                        <label class="btn btn-link btn-file">
                                      <span class="fa fa-upload text-center font-15 set-profile-picture" ><span
                                           > &nbsp;Upload Your Signature</span>
                                      </span>
                                                                            <!-- The file is stored here. -->
                                                                            <input type="file" id="upload" name="image[]"
                                                                                   data-src="">

                                                                        </label>
                                                                        <button type="button" class="btn btn-default"
                                                                                id="cancel_img">Cancel
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <br>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-8 col-sm-12 col-xs-12">
                                                        <span class="text-danger"> OR</span>
                                                       <div class="col-md-6 col-sm-4 col-xs-4 form-group" >
                              <label for="fullname">Write Your Signature Here <span class="text-danger"> </span></label>
                           <div class="wrapper">
                           
           <canvas id="signature-pad" width="200" height="200"  style=" border: 3px solid #555;"></canvas>
            <input type="hidden" name="signature" id="signature" value="">
            <input type="hidden" name="sign_check" id="sign_check" value=0>
       </div>
       <div class="btn_clear">
           <submit id="clear"><span style="background-color:gray;" class="text-danger"> Clear </span></submit>
       </div>
</div>
                                                    </div>

                                                    <div class="form-group pull-right" >
                                                        <div class="col-md-12 col-sm-6 col-xs-12">
                                                            <br>
                                                            <input type="hidden" name="route-exist-check"
                                                                   id="route-exist-check"
                                                                   value="{{ url('admin/check_user_email_exits') }}">
                                                            <input type="hidden" name="token-value"
                                                                   id="token-value"
                                                                   value="{{csrf_token()}}">

                                                            <button type="submit" class="btn btn-primary"
                                                                    id="upload-result" style="float:right"><i class="fa fa-save"
                                                                                          id="show_loader"></i>&nbsp;Update
                                                            </button>
                                                        </div>
                                                    </div>

                                                </div>
                                            </form>
                                        </div>
<div>

                                    </div>
	</div>
<script type="text/javascript">
    var canvas = document.getElementById("signature-pad");

       function resizeCanvas() {
           var ratio = Math.max(window.devicePixelRatio || 1, 1);
           canvas.width = canvas.offsetWidth * ratio;
           canvas.height = canvas.offsetHeight * ratio;
           canvas.getContext("2d").scale(ratio, ratio);
       }
	window.onresize = resizeCanvas;
       resizeCanvas();

       var signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(250,250,250)'
       });

       document.getElementById("clear").addEventListener('click', function(){
        signaturePad.clear();
       })


    const form = document.querySelector("form");
 
        // Prevent form submission on button click
        document
            .getElementById("upload-result")
            .addEventListener("click", function (event) {
                //event.preventDefault();
 var signed= document.getElementById("upload").files.length 
    document.getElementById('signature').value = signaturePad.toDataURL();
    console.log(signaturePad.isEmpty())
   var signature=$('#signature').val();
   if(signaturePad.isEmpty()==false){
     document.getElementById('sign_check').value=1;

   }
   let form=$('#change_signature').serialize()
   console.log(form)
if(signaturePad.isEmpty()==true && signed==0){
    $.alert({
             icon: "fa fa-warning",
             title: "Missing Data!",
             type: "orange",
             content: "At least choose one method",
         });
         return; 
            } 

            sendSignature()
            });

            function    sendSignature(){

            let signature_url=$('#send_signature').val();
            $.ajaxSetup({
             headers: {
                 "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
             },
         });
            $.ajax({
                method: "POST",
                dataType:"JSON",
                url: signature_url,
                data: $('#change_signature').serialize(),
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
    <script src="{{ asset('assets/admin/jcropper/js/cropper.min.js') }}"></script>
         <script src="{{asset('assets/admin/js/user/image-crop.js') }}"></script>
   
   <script src="{{asset('assets/admin/js/inventory/bincard.js')}}"> </script>
  
@endpush