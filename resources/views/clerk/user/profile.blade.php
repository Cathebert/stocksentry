@extends('clerk.layout.main')
@section('title','User Profile')
@push('style')
   <link href="{{ asset('assets/admin/Image-preview/dist/css/bootstrap-imageupload.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/jcropper/css/cropper.min.css') }}" rel="stylesheet">
@endpush
@section('content')




<!--content start-->

<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>User Profile Information</strong></h5>
      <br>
   @include('clerk.user.header')  
    <div class="clearfix"></div>  
<div class="row">
   
                                            <form id="add_user" name="add_user" role="form" method="POST"
                                                  enctype="multipart/form-data"
                                                  action="{{ route('profile.update')}}">
                                                @csrf

                                                <input type="hidden" id="id" name="id" value="{{ $users->id}}">
                                                <input type="hidden" id="imagebase64" name="imagebase64">
                                                <div class="row">
                                                    <div class="col-md-4 col-sm-12 col-xs-12">

                                                        <div class="row">
                                                            <div class="col-md-12 text-center dimage">
                                                                @if($users->profile_img!='')
                                                                    <img id="crop_image"
                                                                         src="{{ (!empty(auth()->user()->profile_img)) ? url('/public/upload/profile/' . auth()->user()->profile_img) : asset('assets/img/undraw_profile.svg') }}"
                                                                         width='100px' height='100px'
                                                                         class="crop_image_profile"
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
                                                                    <img id="demo_profile"
                                                                         src='{{ asset('assets/img/undraw_profile.svg') }}'
                                                                         width='100px'
                                                                         height='100px'
                                                                         class="crop_image_profile"
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
                                           > &nbsp;Set profile picture</span>
                                      </span>
                                                                            <!-- The file is stored here. -->
                                                                            <input type="file" id="upload" name="image"
                                                                                   data-src="{{ $users->id}}">

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
                                                        <div class="row form-group">
                                                            <div class="col-md-6">
                                                                <label for="f_name">First Name <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" id="name" name="name"
                                                                       placeholder="" class="form-control"
                                                                       value="{{ $users->name}}">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="last_name">Last Name <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" id="last_name" name="last_name"
                                                                       class="form-control"
                                                                       value="{{ $users->last_name}}">
                                                            </div>
                                                        </div>


                                                        <div class="row form-group">
                                                            <div class="col-md-6">
                                                                <label for="email">Email <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" id="email" name="email"
                                                                       class="form-control" value="{{ $users->email}}">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="mobile">Mobile No <span class="text-danger"></span></label>
                                                                <input type="text" id="mobile" name="mobile"
                                                                       class="form-control" maxlength="10"
                                                                       value="{{ $users->phone_number}}">
                                                            </div>
                                                        </div>



                                                        


                                                       
                                                          
                                                        </div>
                                                    </div>

                                                    <div class="form-group pull-right">
                                                        <div class="col-md-12 col-sm-6 col-xs-12">
                                                            <br>
                                                            

                                                            <button type="submit" class="btn btn-primary"
                                                                    id="upload-result"><i class="fa fa-save"
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

	

</div>
            @endsection

    @push('js')
    <script src="{{ asset('assets/admin/jcropper/js/cropper.min.js') }}"></script>
         <script src="{{asset('assets/admin/js/user/image-crop.js') }}"></script>
   
   <script src="{{asset('assets/admin/js/inventory/bincard.js')}}"> </script>
  
@endpush