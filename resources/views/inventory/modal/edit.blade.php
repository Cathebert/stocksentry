       <link href="{{ asset('assets/admin/Image-preview/dist/css/bootstrap-imageupload.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/jcropper/css/cropper.min.css') }}" rel="stylesheet">
      
      <script src="{{ asset('assets/admin/jcropper/js/cropper.min.js') }}"></script>
         <script src="{{asset('assets/admin/js/inventory/image-crop.js') }}"></script>
   
<div class="modal-header">
        <h5 class="modal-title">Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
    <div class="row" >
  
  <div class="col-sm-9">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Edit Item </strong></h5>
       
<form method="post"  enctype="multipart/form-data" action="{{route('inventory.update')}}" id="edit_form">
          @csrf
   
       <input type="hidden" class="form-control" id="edit_data" value="{{route('inventory.update')}}">
  
 
          <input type="hidden" class="form-control" id="table_data" value="{{route('inventory.getItems')}}">
            <input type="hidden" class="form-control" id="item_edit" value="{{route('inventory.edit')}}">
               <input type="hidden" class="form-control" id="item_id" value="{{$item->id}}">
  
  <div class="row">
     <div class="col-md-2 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Code</label>
    <input type="text" class="form-control" id="code" name="code" value="{{$item->code}}">
  </div>
 <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Brand</label>
    <input type="text" class="form-control" id="brand" name="brand" value="{{$item->brand}}">
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Item Name</label>
    <input type="text" class="form-control" id="item_name" name="generic_name" value="{{$item->item_name}}">
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Item Description</label>
    <input type="text" class="form-control" id="item_description" name="item_description" value="{{$item->item_description}}">
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Item Category</label>
    <select class="form-control" id="item_category" name="item_category" style="width: 75%" >
     <option value="ambient goods"{{(!empty($item->item_category) && $item->item_category=='ambient goods') ? "Selected":"" }}>Ambient goods </option>
    <option value="perishable"{{(!empty($item->item_category) && $item->item_category=='perishable') ? "Selected":""}}>Perishable</option>
  
 
  </select>
  </div>
    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Warehouse pack Size</label>
    <input type="text" class="form-control" id="warehouse_size" name="warehouse_size" value="{{$item->warehouse_size}}" required>
  </div>
    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Location</label>
    <input type="text" class="form-control" id="location" name="location" value="{{$item->location}}" >
  </div>
   <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Catalog Number</label>
    <input type="text" class="form-control" id="cat_number" name="cat_number"  value="{{$item->catalog_number}}">
  </div>
    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Hazardous</label>
    <select class="form-control" id="inputGroupSelect02" name="is_hazardous" style="width: 75%" >
   
    <option value="no" {{(!empty($item->is_hazardous) && $item->is_hazardous =='no') ? "Selected" : "" }}>No</option>
    <option value="yes"{{(!empty($item->is_hazardous) && $item->is_hazardous =='yes') ? "Selected" : "" }}>Yes</option>
 
  </select>
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Storage Condition Temp</label>
    <select class="form-control" id="inputGroupSelect02" name="store_temp" style="width: 75%" >
   
    <option value="cold chain" {{(!empty($item->store_temp) && $item->store_temp =='cold chain') ? "Selected" : "" }}>Cold Chain</option>
    <option value="room temp" {{(!empty($item->store_temp) && $item->store_temp =='room temp') ? "Selected" : "" }}>Room temp</option>
 
  </select>
  </div>

  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Unit Of Issue</label>
    <select class="form-control" id="inputGroupSelect02" name="unit_issue" style="width: 75%" >
   
    <option value=""></option>
    <option value="Bottle" {{(!empty($item->unit_issue) && $item->unit_issue =='Bottle') ? "Selected" : "" }}>Bottle</option>
     
    <option value="EACH" {{(!empty($item->unit_issue) && $item->unit_issue =='Each') ? "Selected" : "" }}>Each</option>
    <option value="Pack" {{(!empty($item->unit_issue) && $item->unit_issue =='Pack') ? "Selected" : "" }}>Pack</option>
    <option value="Box" {{(!empty($item->unit_issue) && $item->unit_issue =='Box') ? "Selected" : "" }}>Box</option>
    <option value="Roll"{{(!empty($item->unit_issue) && $item->unit_issue =='Roll') ? "Selected" : "" }}>Roll</option>
    <option value="Set" {{(!empty($item->unit_issue) && $item->unit_issue =='Set') ? "Selected" : "" }}>Set</option>
     <option value="Kit" {{(!empty($item->unit_issue) && $item->unit_issue =='Kit') ? "Selected" : "" }}>Kit</option>
  </select>
  </div>

 <input type="hidden" id="imagebase64" name="imagebase64">
 <div class="col-md-4 col-sm-12 col-xs-12 form-group">
   <label for="exampleInputPassword1">Stock Level</label>
  
<div class="input-group mb-3">
   <span class="input-group-text btn btn-secondary">Min</span>
  <input type="number" class="form-control" name="min" placeholder="Minimum" aria-label="Minimum" value="{{$item->minimum_level}}">
  <span class="input-group-text btn btn-secondary">Max</span>
  <input type="number" class="form-control" name="max"placeholder="Maximum" aria-label="Maximum" value="{{$item->maximum_level}}">
</div>
</div>
     <div class="col-md-4 col-sm-12 col-xs-12 form-group">
         <label for="exampleInputtxt1">Laboratory</label>
                                                 
  <select class="form-control" id="inputGroupsetion" name="lab_id" >
   
    <option value=""></option>
   @foreach ($laboratories as $laboratory)
       <option value="{{$laboratory->id}}" {{(!empty($item->laboratory_id) && $item->laboratory_id ==$laboratory->id) ? "Selected" : "" }}>{{$laboratory->lab_name}}</option>
   @endforeach
   <option value="999" {{(!empty($item->laboratory_id) && $item->laboratory_id ==999) ? "Selected" : "" }}>Other</option>
  </select>
</div>


</div>

<script type="text/javascript">

</script>

      </div>
    </div>
  </div>

  <div class="col-sm-3">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Item Image</h5>

   <div class="imageupload">
   <div class="file-tab">
                                              
   <div id="upload-demo" class="upload-demo-img"  width='100px' height='100px'></div>
   @if(!$item->item_image)
                                                <img id="demo_profile" src="{{asset('assets/icon/placeholder.png')}}"
                                                     width='100px' height='100px'
                                                     class="img-thumbnail demo_profile">
@else
  <img id="demo_profile" src="{{asset('public/upload/items/'.$item->item_image)}}"
                                                     width='100px' height='100px'
                                                     class="img-thumbnail demo_profile">
   @endif
                                          

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

      <div class="modal-footer">
        
  <button type="submit" class="btn btn-primary"  id="edit" >Save changes</button>

      </div>
        </form>
  
</div>
</div>   

<script type="text/javascript">

   $(document).ready(function() {
   
   $('#edit').on('click',function(e){
    $uploadCrop.croppie('result', {
        type: 'canvas',
        size: 'viewport'
    }).then(function (resp) {

        $('#imagebase64').val(resp);

     console.log(resp)
var edit_url=$("#edit_data").val();
      var id=$('#item_id').val();
    var data=$('#edit_form').serialize();
  
     $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
          $.ajax({
                method: "POST",
                dataType:"JSON",
                url: edit_url,
                data: data+"&id="+id,
    
                  
                success: function(data) {
                       $('.btn-close').click();
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
           getItems();
                }
            });
   })
   


   
});
    });
      
  </script>

  
</div>
