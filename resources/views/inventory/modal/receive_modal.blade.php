      <script src="{{asset('assets/admin/js/repeter/repeater.js') }}"></script>
         <script src="{{asset('assets/admin/js/inventory/repeater.js') }}"></script>
<div class="modal-header">
        <h5 class="modal-title">Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close_item_modal"></button>
      </div>
      <div class="modal-body">
    <div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Item</strong></h5>
       
<form method="post"  enctype="multipart/form-data" action="{{route('item.add_temp')}}" id="add_form_Modal">
          @csrf
   
       <input type="hidden" class="form-control" id="add_data" value="{{route('item.add_temp')}}">
  
 
          <input type="hidden" class="form-control" id="table_data" value="{{route('inventory.getItems')}}">
            <input type="hidden" class="form-control" id="item_edit" value="{{route('item.item_delete')}}">
               <input type="hidden" class="form-control" id="item_id" value="{{$item->id}}">
  
  <div class="row">

  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Code</label>
    <input type="text" class="form-control" id="item_name" name="brand_name" value="{{$item->code}}" readonly>
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Item Name</label>
    <input type="text" class="form-control" id="item_name" name="generic_name" value="{{$item->item_name}}" readonly>
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Item Description</label>
    <input type="text" class="form-control" id="item_description" name="item_description" value="{{$item->item_description}}" readonly>
  </div>

  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Warehouse pack Size</label>
    <input type="text" class="form-control" id="warehouse_size" name="warehouse_size" value="{{$item->warehouse_size}}" required readonly>
  </div>
   <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Catalog Number</label>
    <input type="text" class="form-control" id="cat_number" name="cat_number"  value="{{$item->catalog_number}}" >
  </div>
    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Hazardous</label>
    <select class="form-control" id="is_hazardous" name="is_hazardous" style="width: 75%" disabled readonly>
   
    <option value="No" {{(!empty($item->is_hazardous) && $item->is_hazardous =='no') ? "Selected" : "" }}>No</option>
    <option value="Yes"{{(!empty($item->is_hazardous) && $item->is_hazardous =='yes') ? "Selected" : "" }}>Yes</option>
 
  </select>
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Storage Condition Temp</label>
    <select class="form-control" id="inputGroupSelect02" name="store_temp" style="width: 75%"   readonly>
   
    <option value="cold chain" {{(!empty($item->store_temp) && $item->store_temp =='cold chain') ? "Selected" : "" }}>Cold Chain</option>
    <option value="room temp" {{(!empty($item->store_temp) && $item->store_temp =='room temp') ? "Selected" : "" }}>Room temp</option>
 
  </select>
  </div>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Unit Of Issue</label>
    <select class="form-control" id="unit_issue" name="unit_issue" style="width: 75%" disabled readonly>
   
    <option value="Bottle" {{(!empty($item->unit_issue) && $item->unit_issue =='Bottle') ? "Selected" : "" }}>Bottle</option>
     
    <option value="Each" {{(!empty($item->unit_issue) && $item->unit_issue =='Each') ? "Selected" : "" }}>Each</option>
    <option value="Pack" {{(!empty($item->unit_issue) && $item->unit_issue =='Pack') ? "Selected" : "" }}>Pack</option>
    <option value="Box" {{(!empty($item->unit_issue) && $item->unit_issue =='Box') ? "Selected" : "" }}>Box</option>
    <option value="Roll"{{(!empty($item->unit_issue) && $item->unit_issue =='Roll') ? "Selected" : "" }}>Roll</option>
    <option value="Set" {{(!empty($item->unit_issue) && $item->unit_issue =='Set') ? "Selected" : "" }}>Set</option>
     <option value="Kit" {{(!empty($item->unit_issue) && $item->unit_issue =='Kit') ? "Selected" : "" }}>Kit</option>
  </select>
  </div>


 <div class="col-md-4 col-sm-12 col-xs-12 form-group">
   <label for="exampleInputPassword1">Stock Level</label>
<div class="input-group mb-3">
  <input type="number" class="form-control" name="min" placeholder="Minimum" aria-label="Minimum" value="{{$item->minimum_level}}" readonly>
  <span class="input-group-text btn btn-secondary">-</span>
  <input type="number" class="form-control" name="max"placeholder="Maximum" aria-label="Maximum" value="{{$item->maximum_level}}" readonly>
</div>
</div>
<hr>
  <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Has Expiry Date ?</label>
    <select class="form-control" id="has_expiry" name="has_expiry" style="width: 75%" onchange="showExpiry(this.value)">
   
    <option value="yes" selected>Yes</option>
    <option value="no" >No</option>
 
  </select>
  </div>
    <div class="col-md-4 col-sm-12 col-xs-12 form-group" id="any_expired" >
    <label for="exampleInputPassword1">Any Items Expired ?</label>
    <select class="form-control" id="any_expired" name="any_expired" style="width: 75%">
   <option value="no" selected>No</option>
    <option value="yes">Yes</option>
    
 
  </select>
  </div>
   <div class="col-md-4 col-sm-12 col-xs-12 form-group" id="any_expired" >
    <label for="exampleInputPassword1">Any Items Damaged? (Y/N)
 ?</label>
    <select class="form-control" id="any_damaged" name="any_damaged" style="width: 75%">
   <option value="no" selected>No</option>
    <option value="yes">Yes</option>
    
 
  </select>
  </div>
@if($item->store_temp =='cold chain')
    <div class="col-md-4 col-sm-12 col-xs-12 form-group" >
    <label for="exampleInputPassword1">Items received at correct temperature?</label>
    <select class="form-control" id="correct_temp" name="correct_temp" style="width: 75%">
   <option value="no" selected>No</option>
    <option value="yes">Yes</option>
    
 
  </select>
  </div>
  @endif

    <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Are Items Suitable for Use ?</label>
    <select class="form-control" id="suitable_for_use" name="suitable_for_use" style="width: 75%">
   
    <option value="yes" selected>Yes</option>
    <option value="no" >No</option>
 
  </select>
  </div>
<div class="col-md-4 col-sm-12 col-xs-12 form-group">
   <label for="exampleInputPassword1">Storage Location</label>
<div class="input-group ">
  <select class="custom-select" id="store_location" name="store_location">

      <option value="1">Store</option>
      <option value="2"   readonly  selected>Laboratory</option>
      
  </select>
  
</div>
</div>

  <div class="col-md-4 col-sm-12 col-xs-12 form-group" hidden>
    <label for="exampleInputPassword1">PP No.</label>
    <input type="text" class="form-control" id="item_pp" name="item_pp" value="PP01">
  </div>
</div>
<hr>

 <div class="row">
     <div class="repeater">
      <div class="row">
     <hr>
                            <div data-repeater-list="additional">
                                <div data-repeater-item>
                                    
                                    <div class="row">
        <div class="col-md-2 col-sm-12 col-xs-12 form-group">
       <label for="exampleInputPassword1">Quantity</label>
       <input type="number" class="form-control" id="item_quantity" name="item_quantity" min="1">
  </div>
<div class="col-md-2 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Cost</label>
    <input type="number" class="form-control" id="item_cost" name="item_cost" min="1">
  </div>      
      <div class="col-md-2 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Batch #</label>
    <input type="text" class="form-control" id="item_batch" name="item_batch">
  </div>
      <div class="col-md-2 col-sm-12 col-xs-12 form-group" id="show_expiry">
    <label for="exampleInputPassword1">Expiry Date</label>
    <input type="date" class="form-control" id="item_expiry" name="item_expiry">
  </div>  
                             

                                        <div class="col-md-1">

                                            <div class="form-group">

                                                <div class="case-margin-top-23"></div>
                                                <button type="button" data-repeater-delete type="button"
                                                        class=""><i
                                                        class="fa fa-trash" aria-hidden="true" style="color:red"></i></button>
                                            </div>


                                        </div>


                                    </div>

                                 
                                </div>
                            </div>

                            <button data-repeater-create type="button" value="Add New"
                                    class="tn-success-edit" type="button">
                                <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;New
                            </button>

                        </div>
                     </div>

  <!--repeater-->
  

</div>
</div>
</div>

  
   




      <div class="modal-footer">
        
  <button type="button" class="btn btn-primary "  id="edit" onclick="submitItemDetails()">Add</button>

      </div>
        </form>
  
</div>
  

<script type="text/javascript">
   
function submitItemDetails(){
    
  var id=$('#item_id').val();
  var table=$('#add_form_Modal').serialize();
  var add_url="{{route('item.add_temp')}}";
  
  console.log(table);
  let cat_number=$('#cat_number').val()
  let unit_issue=$('#unit_issue').val()
  let cost=$('#item_cost').val()
   
$.confirm({
       title: "Confirm!",
       content: "Are you sure Quantity,Cost and Batch Number  are correct?",
       buttons: {
           Oky: {
               btnClass: "btn-warning",
               action: function () {

                 $.ajaxSetup({
                     headers: {
                         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                             "content"
                         ),
                     },
                 });

                $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
            $.ajax({
                method: "POST",
                dataType:"JSON",
                url: add_url,
                data: table+"&id="+id,
                success: function(data) {
                  console.log("ADEDEDE")
                          $('#close_item_modal').click();
                 t.destroy();
         
                  LoadTable();

                },
                error:function(error){
console.log("Something went wrong"+error);

                }
            });
               },
           },
          

           cancel: function () {},
       },
   });
  
  
  

  
  
  //end
      
          }

   

  </script>
  
</div>