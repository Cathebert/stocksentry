 @extends('provider.layout.main')
@section('title','Stock Inventory')
@push('style')
        <link href="{{ asset('assets/admin/Image-preview/dist/css/bootstrap-imageupload.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/jcropper/css/cropper.min.css') }}" rel="stylesheet">
      
@endpush
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('moderator.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Available</li>
  </ol>
</nav>



  <div class="container">
  <div class="row">
    <div class="col">
  <!---Card-->
<div class="card text-center">
  <div class="card-header">
  <div class="dropdown">
  <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Items
  </a>

  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
    <a class="dropdown-item" href="#">Low Stock Items</a>
    <a class="dropdown-item" href="#">Expired Items</a>
    <a class="dropdown-item" href="#">Returnable Items</a>
  </div>
</div>
  </div>
  <div class="card-body">
<ul class="list-group " >
  @foreach ($items as $item )
     <li class="list-group-item list-group-item-action" style="text-align:left"  id="{{$item->id}}" onclick="CheckBoxSelect(this.id)"><input type="checkbox" value="{{$item->id}}" id="check_{{$item->id}}" name="check" onclick="onlyOne(this)"><a> {{$item->item_name}}</a></li>
  @endforeach
 

</ul>

  </div>
  <div class="card-footer text-muted">
    {{ $items->links()}}
  </div>
</div>

<!---end Card-->
    </div>
    <div class="col-8">
      
     <div class="card text-center">
       <div class="card-header" id="item_name">Item</div>
  <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs">
      <li class="nav-item">
        <a class="nav-link active" href="#">Overview</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">History</a>
      </li>
     
    </ul>
  </div>
  <div class="card-body" id="details">

  
</div>
    </div>
    <div class="col">
     
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
        console.log(item_name)
        document.getElementById('item_name').innerHTML=item_name;
         document.getElementById('details').innerHTML=""
          var details='<form><div class="form-group row">'
    details+='<label for="staticEmail" class="col-sm-2 col-form-label">Brand</label>'
   details+=' <div class="col-sm-4">'
    
 details+='<label for="staticEmail" class=" col-form-label text-muted " id="brand">'+item_brand+'</label></div></div>'
   
   details+='<div class="form-group row">'
    details+=' <label for="inputPassword" class="col-sm-2 col-form-label">Code</label>'
  details+='<label for="staticEmail" class=" col-form-label text-muted " id="brand">'+data.item['code']+'</label></div></div>'
    details+='<div class="form-group row">'
    details+=' <label for="inputPassword" class="col-sm-2 col-form-label">Description</label>'
    details+=' <div class="col-sm-4">'+data.item['item_description']+' </div>'
       details+='<div class="form-group row">'
        details+='<label for="inputPassword" class="col-sm-2 col-form-label">Cost</label>'
        details+='<div class="col-sm-4">'+data.item['cost']+'</div> </div></div></form>'
      document.getElementById('details').innerHTML=details;
    

        
    

          
         
          },
      });
}
  </script>


  <!-- /.container-fluid -->

            </div>
             </div>
          
            @endsection