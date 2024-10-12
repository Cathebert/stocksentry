@extends('layouts.main')
@section('title','Create Item')
@push('style')
        <link href="{{ asset('assets/admin/Image-preview/dist/css/bootstrap-imageupload.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/jcropper/css/cropper.min.css') }}" rel="stylesheet">
      
@endpush
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create</li>
  </ol>
</nav>
  
<div class="row" >
  
  <div class="col-sm-9">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>New Items </strong></h5>
        <form method="post" id="form_id" enctype="multipart/form-data" action="{{route('inventory.add')}}">
          @csrf
          <input type="hidden" class="form-control" id="post_url" value="{{route('inventory.add')}}">
 

 
          <input type="hidden" class="form-control" id="table_data" value="{{route('inventory.getTodaysItems')}}">
            <input type="hidden" class="form-control" id="item_edit" value="{{route('inventory.edit')}}">
         <input type="hidden" class="form-control" id="item_delete" value="{{route('inventory.delete')}}">
         <input type="hidden" class="form-control" id="get_selected_lab" value="{{route('lab.sections')}}"/>
  <div class="row">
  
   <div class="col-md-2 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">ULN</label>
    <input type="text" class="form-control" id="uln" name="uln" value="{{$uln}}" readonly>
  </div>

 <div class="col-md-2 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Code <span class="text-danger"></span></label>
    <input type="text" class="form-control" id="code" name="code">
  </div>

   <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Brand Name</label>
    <input type="text" class="form-control" id="brand_name" name="brand_name">
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Item Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="item_name" name="generic_name" required>
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Item Description</label>
    <input type="text" class="form-control" id="item_description" name="item_description">
  </div>
<div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Item Category</label>
    <select class="form-control" id="item_category" name="item_category" style="width: 75%" >
     <option value="ambient goods">Ambient goods </option>
    <option value="perishable">Perishable</option>
  
 
  </select>
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Warehouse pack Size <span class="text-danger"></span></label>
    <input type="text" class="form-control" id="warehouse_size" name="warehouse_size" >
  </div>
   <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Location <span class="text-danger"></span></label>
    <input type="text" class="form-control" id="location" name="location" >
  </div>
   <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Catalog Number<span class="text-danger"></span></label>
    <input type="text" class="form-control" id="cat_number" name="cat_number" >
  </div>
    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Place Of Purchase</label>
    <select class="form-control" id="place_of_purchase" name="place_of_purchase" style="width: 75%" >
   
    <option value="local" selected>Local</option>
    <option value="international">International</option>
 
  </select>
  </div>
    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Hazardous</label>
    <select class="form-control" id="inputGroupSelect02" name="is_hazardous" style="width: 75%" >
   
    <option value="No">No</option>
    <option value="Yes">Yes</option>
 
  </select>
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Storage Condition Temp</label>
    <select class="form-control" id="inputGroupSelect02" name="store_temp" style="width: 75%" >
   
    <option value="cold chain">Cold Chain</option>
    <option value="room temp">Room temp</option>
 
  </select>
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Unit Of Issue</label>
    <select class="form-control" id="inputGroupSelect02" name="unit_issue" style="width: 75%" >
   
    <option value=""></option>
    <option value="Bottle">Bottle</option>
    <option value="each" >Each</option>
    <option value="Pack">Pack</option>
    <option value="Box">Box</option>
    <option value="Roll">Roll</option>
    <option value="Set">Set</option>
     <option value="Kit">Kit</option>
  </select>
  </div>


 <div class="col-md-4 col-sm-12 col-xs-12 form-group">
   <label for="exampleInputPassword1">Stock Level</label>
<div class="input-group mb-3">
  <input type="number" class="form-control" name="min" placeholder="Minimum" aria-label="Minimum">
  <span class="input-group-text">-</span>
  <input type="number" class="form-control" name="max"placeholder="Maximum" aria-label="Maximum">
</div>
</div>
<div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Laboratory</label>
         <div class="input-group mb-3">
  <select class="form-control" id="inputGrouplab" name="laboratory" style="width: 75%" >
   
    <option value=""></option>
   @foreach ($laboratory as $lab)
       <option value="{{$lab->id}}">{{$lab->lab_name}}</option>
   @endforeach
  
  </select>
</div>
  </div>
  <script type="text/javascript">
  $(document).ready(function() {
    $('#inputGrouplab').select2({
 placeholder: 'Select  Laboratory',
  width: 'resolve',
   
    });
     
});
</script>
     <!--div class="col-md-3 col-sm-12 col-xs-12 form-group" id="req" >
         <label for="exampleInputPassword1">Lab Section</label>
                                                 <div class="input-group mb-3">
  <select class="form-control" id="inputGroupsection" name="lab_section" style="width: 75%" >
   
    <option value=""></option>
   @foreach ($sections as $section)
       <option value="{{$section->id}}">{{$section->section_name}}</option>
   @endforeach
  
  </select>
</div></div -->
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $('#inputGroupsection').select2({
 placeholder: 'Select  Lab section',
  width: 'resolve',
   
    });
     
});
</script>

  <button type="submit" class="btn btn-primary" id="upload-result" style="float:right">Save</button>

      </div>
    </div>
  </div>

  <div class="col-sm-3">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Item Image</h5>

   <div class="imageupload">
                                            <div class="file-tab">
                                               <input type="hidden" id="imagebase64" name="imagebase64">
   <div id="upload-demo" class="upload-demo-img"  width='100px' height='100px'></div>
                                                <img id="demo_profile" src="{{asset('assets/icon/placeholder.png')}}"
                                                     width='100px' height='100px'
                                                     class="img-thumbnail demo_profile">

                                          

                                                <label class="btn btn-link btn-file">
                                        <span class="fa fa-upload text-center font-15"><span
                                                    class="set-profile-picture"> &nbsp; Set Item Image</span>
                                        </span>
                                                    <!-- The file is stored here. -->
                                                    <input type="file" id="upload" name="item_image" data-src="" >

                                                </label>
                                                <button type="button" class="btn btn-default" id="cancel_img">Cancel
                                                </button>
                                            </div>



      </div>
    </div>
  </div>
  </form>
</div>

<br>
  <div class="col-sm-12" hidden>
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"><strong>Added Today </strong></h5>
        <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm table-hover" id="added_item" width=100%>
<thead class="thead-light">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Item Name</th>
     <th scope="col">Code #</th>
      <th scope="col">Catalog #</th>
      <th scope="col">Image</th>
      <th scope="col">Brand</th>
      <th scope="col">Warehouse Pack Size</th>
      <th scope="col">Hazardous</th>
      <th scope="col">Storage Temp.</th>
      <th scope="col">Unit Of Issue</th>
      <th scope="col">Stock Level</th>
      <th scope="col">Section</th>
      
    </tr>
  </thead>

</table>
      </div>

       </div>
    </div>
  </div>  

    <!-- /.container-fluid -->

            </div>
			</div>
          
            @endsection
            @push('js')
                <script src="{{ asset('assets/admin/jcropper/js/cropper.min.js') }}"></script>
         <script src="{{asset('assets/admin/js/inventory/image-crop.js') }}"></script>
   
          
       <script src="{{asset('assets/admin/js/inventory/add_inventory.js') }}"></script>
  
  
@endpush