
<div class="modal-header">
        <h5 class="modal-title">Edit Laboratory</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
    <div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Edit  {{ $laboratory->lab_name }}  Details</Details></strong></h5>
       
<form method="post"   action="{{route('lab.update')}}" id="edit_form">
          @csrf
<input type="hidden" class="form-control" id="edit_data" value="{{route('lab.update')}}">
  
<input type="hidden" class="form-control" id="lab_id" name="lab_id" value="{{$laboratory->id}}">
  
  <div class="row">
     <div class="col-md-12 col-sm-12 col-xs-12 form-group">
        <label for="exampleInputEmail1">Laboratory Name</label>
        <input type="text" class="form-control" id="lab_name" name="lab_name" value="{{$laboratory->lab_name}}">
    
     </div>
    
<div class="col-md-12 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Lab Address</label>
    <input type="text" class="form-control" id="lab_address" name="lab_address" value="{{$laboratory->lab_address}}">
  </div>
 <div class="col-md-12 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Lab Location</label>
    <input type="text" class="form-control" id="lab_location"  name="lab_location" value="{{$laboratory->lab_location}}">
  </div>

 
<div class="col-md-12 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Lab Email</label>
    <input type="text" class="form-control" id="lab_email" name="lab_email" value="{{$laboratory->lab_email}}">
  </div>
<div class="col-md-12 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Lab Phone Number</label>
    <input type="text" class="form-control" id="lab_phone" name="lab_phone" value="{{$laboratory->lab_phone}}">
  </div>

  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Has Sections? </label>
    <div class="form-check">
  <input class="form-check-input" type="radio" name="has_section" id="has_section_no" value="no" {{($laboratory->has_section =='no') ? "checked" : ""}} onchange="hasChanged(this.value)">
  <label class="form-check-label" for="exampleRadios1">
    No
  </label>
</div>
<div class="form-check">
  <input class="form-check-input" type="radio" name="has_section" id="has_section_yes" value="yes" {{($laboratory->has_section =='yes') ? "checked" : ""}} onchange="hasChanged(this.value)">
  <label class="form-check-label" for="exampleRadios2">
    Yes
  </label>
</div>
  </div>
 
   <div class="col-md-12 col-sm-12 col-xs-12 form-group"  id="sections" >
    <label for="exampleInputPassword1">Section</label><br>
    <select class=" form-control" id="inputsection" name="section_id[]" style="width: 100%" multiple>

    <option value=""></option>
    @foreach ( $lab_sections as $section )
     <option value="{{$section->id}}">{{ $section->section_name }}</option> 
    @endforeach
    
   
  </select>
  </div>

  <script type="text/javascript">
    var check="{{$laboratory->has_section}}"
  
    if(check==="yes"){
     $('#sections').show()
    }
    if(check==="no"){
     $('#sections').hide();
    }
    </script>

      <div class="modal-footer">
        
  <button type="submit" class="btn btn-primary"  id="edit" >Save changes</button>

      </div>
        </form>
  
</div>
</div>   

<script type="text/javascript">
  $("#inputsection").select2({
     dropdownParent: $("#sections"),
        allowClear: true,
        placeholder: "Select Lab Sections",
        multiple: true,
    });
   $(document).ready(function() {
   
   $('#edit').on('click',function(e){
    
     var name=$('#lab_name').val()
            var location=$('#lab_location').val();
            if(!name){
                  $("#lab_name").focus();
                 $.alert({
                     icon: "fa fa-warning",
                     title: "Missing information!",
                     type: "orange",
                     content: "Please provide Laboratory name!",
                 });
               
                 e.preventDefault();
            }
             if (!location) {
                 $.alert({
                     icon: "fa fa-warning",
                     title: "Missing information!",
                     type: "orange",
                     content: "Please provide Laboratory Location!",
                 });
                 $("#lab_location").focus();
                e.preventDefault();
             }
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
