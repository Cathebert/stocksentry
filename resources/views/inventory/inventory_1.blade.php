 @extends('layouts.main')
@section('title','Stock Inventory')
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
    <li class="breadcrumb-item active" aria-current="page">Available</li>
  </ol>
</nav>



  <div class="container">
   
  <div class="row">
    <div class="di">
  <nav class="navbar navbar-expand-lg navbar-light bg-light" style="float:right;">
  
  <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
    <div class="navbar-nav">
      <a class="nav-item nav-link active" href="" id="update_date"><i class="fa fa-edit" aria-hidden="true"></i> Update Consumption <span class="sr-only">(current)</span></a>
      <a class="nav-item nav-link" href="#" id="stock_take" ><i class="fa fa-list"  aria-hidden="true"></i> Take Stock</a>
      <a class="nav-item nav-link" href="#"><i class="fa fa-plus-square" aria-hidden="true"></i> Adjust Inventory</a>
    
    </div>
  </div>
</nav>
</div>
    <div class="col">
      
  <!---Card-->
<div class="card text-center">
  <div class="card-header">
  <div class="dropdown" style="text-align:left" >
  <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Items
  </a>

  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
    <a class="dropdown-item" href="#">Low Stock Items</a>
    <a class="dropdown-item" href="#">Expired Items</a>
    <a class="dropdown-item" href="#">Active Items</a>
      <a class="dropdown-item" href="#">Inactive Items</a>
       <a class="dropdown-item" href="#">All Items</a>
  </div>
</div>
 <div class="dropdown" style="text-align:right" >
  <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <button class="btn btn-outline-secondary btn-sm" hidden><i class="fa fa-ellipsis-h"></i></button>
  </a>

  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
  
    <a class="dropdown-item" href="#"><i class="fa fa-share-square "></i> Export Items</a>
    <a class="dropdown-item" href="#">Returnable Items</a>
  </div>
</div>
  </div>
  <div class="card-body">
      @if(!empty($items) && count($items)>0)
<ul class="list-group " >

  @foreach ($items as $item )
     <li class="list-group-item list-group-item-action" style="text-align:left"  id="{{$item->id}}" onclick="CheckBoxSelect(this.id)"><input type="checkbox" value="{{$item->id}}" id="check_{{$item->id}}" name="check" onclick="onlyOne(this)"><a> {{$item->item_name}}</a></li>
  @endforeach
 

</ul>



  </div>
   <div class="card-footer text-muted" id="foot">
    {{ $items->links()}}
  </div>
  @else
<h6> No Inventory available </h6>
  @endif
</div>

<!---end Card-->
    </div>
    
    <div class="col-8">
    
     <div class="card text-center">
      
       <div class="card-header" id="item_name">Item</div>
       
  <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs">
      <li class="nav-item">
        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home">Overview</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile">History</a>
      </li>
     
    </ul>
  </div>
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
    <div class="container">
    
  <div class="row">
    <div class="col col-lg-10">
      <div class="card-body" id="details">

  
</div>
    </div>
    
    <div class="col-lg">

      <div id="image" ></div>
      
    </div>

  </div>
       <div class="card" style="width: 18rem; float:right">
  
  <ul class="list-group list-group-flush">
    <li class="list-group-item"><strong>Opening Stock: </strong></li>
    <li class="list-group-item">Consumed :</li>
    <li class="list-group-item">Issued To:</li>
  </ul>
</div>
</div>
  
     
    </div>



  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">...</div>

</div>
</div>
  
    
  
 
</div>

<script type="text/javascript">
 function onlyOne(checkbox) {
    var checkboxes = document.getElementsByName('check')
    checkboxes.forEach((item) => {
        if (item !== checkbox) item.checked = false
    })

   
}
function CheckBoxSelect(id){
     var checkboxes = document.getElementsByName('check')
    checkboxes.forEach((item) => {
        if (item.checked ==true) 
        item.checked=false
    })
 var check= document.getElementById('check_'+id).checked=true;
var id=id;
  getItemDetails(id)
  
}

function  getItemDetails(id){
 
  var detailsurl="{{route('inventory.item_details')}}"
   $.ajax({
          method: "GET",
          dataType: "JSON",
          url: detailsurl,
          data: {
            id:id,
          },
          success: function (data) {
        console.log(data)
        var item_name=data.item['item_name'];
        var item_brand=data.item['item_description'];
        var item_image=data.item['item_image'];
        var image_src="{{asset('assets/icon/placeholder.png')}}";
        var full_path=""
        if(item_image){
image_src="{{asset('public/upload/items/')}}";
image_src+='/'+item_image
        }
        console.log(item_name)
        document.getElementById('item_name').innerHTML=item_name;
         document.getElementById('details').innerHTML=""
         document.getElementById('image').innerHTML=""
          var details='<form>'
           details+='<div class="form-group row" >'
    details+='<label for="inputPassword" class="col-sm-2 col-form-label">Brand:</label>'
   details+=' <div class="col-sm-4">'  
    details+=' <div class="col-sm-8  text-muted">'+data.item['brand']+' </div></div></div>'
   
   details+='<div class="form-group row" >'
    details+=' <label for="inputPassword" class="col-sm-2 col-form-label">Code:</label>'
     details+=' <div class="col-sm-4">'
   details+=' <div class="col-sm-8  text-muted">'+data.item['code']+' </div></div></div>'

 details+='<div class="form-group row">'
    details+=' <label for="inputPassword" class="col-sm-2 col-form-label">Batch #:</label>'
     details+=' <div class="col-sm-4">'
   details+=' <div class="col-sm-8  text-muted">'+data.item['batch_number']+' </div></div></div>'


    details+='<div class="form-group row">'
    details+=' <label for="inputPassword" class="col-sm-2 col-form-label">Description:</label>'
       details+=' <div class="col-sm-4">'
    details+=' <div class="col-sm-12 text-muted">'+data.item['item_description']+' </div></div></div>'

     details+='<div class="form-group row">'
    details+=' <label for="inputPassword" class="col-sm-2 col-form-label">UOM:</label>'
     details+=' <div class="col-sm-4">'
   details+=' <div class="col-sm-8  text-muted">'+data.item['unit_issue']+' </div></div></div>'

       details+='<div class="form-group row">'
        details+='<label for="inputPassword" class="col-sm-2 col-form-label">Cost:</label>'
           details+=' <div class="col-sm-4">'
        details+='<div class="col-sm-4" text-muted>'+data.item['cost']+'</div> </div></div>'

         details+='<div class="form-group row">'
        details+='<label for="inputPassword" class="col-sm-2 col-form-label">Quantity:</label>'
         details+=' <div class="col-sm-4">'
        details+='<div class="col-sm-4 text-muted">'+data.item['quantity']+'</div> </div></div>'

        details+='<div class="form-group row">'
        details+='<label for="inputPassword" class="col-sm-2 col-form-label">Expiry Date:</label>'
         details+=' <div class="col-sm-4">'
        details+='<div class="col-sm-8 text-muted">'+data.item['expiry_date']+'</div> </div></div>'

        
         details+='</form>'
         var image='<img src='+image_src+' alt="..." class="img-thumbnail" width="200px" height="200px">'
      document.getElementById('details').innerHTML=details;
  document.getElementById('image').innerHTML=image;

        
    

          
         
          },
      });
}
  </script>


    <div class="modal fade" id="infor" role="dialog" data-focus="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" id="add_certi">

            </div>
        </div>
    </div>
  <!-- /.container-fluid -->
      
            </div>
        
    <input type="hidden" id="consumption_update" value="{{route('inventory.show_update')}}" />
       <input type="hidden" class="form-control" id="load_inventory" value="{{route('inventory.load')}}"> 
           <input type="hidden" class="form-control" id="stock_taking" value="{{route('stock.take')}}"> 
            <input type="hidden" class="form-control" id="inputFile" value="{{route('stock.upload')}}"> 
           
             </div>
          
            @endsection

 @push('js')
       <script src="{{asset('assets/admin/js/inventory/inventory.js')}}"> </script>
 
   <script src="{{asset('assets/admin/js/inventory/stock_take.js')}}"> </script>
  
@endpush