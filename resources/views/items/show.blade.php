@extends('layouts.main')
@section('title','Items')
@push('style')
       
@endpush
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Items</li>
  </ol>
</nav>




<!--content start-->

<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong> Create Item </strong></h5>
 <div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
         <form method="post" id="form_id" enctype="multipart/form-data" action="{{route('inventory.add')}}">
          @csrf
          <input type="hidden" class="form-control" id="post_url" value="{{route('inventory.add')}}">
    
 
          <input type="hidden" class="form-control" id="item_data" value="{{route('inventory.getItems')}}"/>
            <input type="hidden" class="form-control" id="item_edit" value="{{route('inventory.edit')}}"/>
         <input type="hidden" class="form-control" id="item_delete" value="{{route('inventory.delete')}}"/>
         <input type="hidden" class="form-control" id="deactivate" value="{{route('item.deactivate')}}"/>
         <input type='hidden' class="form-control" id="item-csvlist" value="{{route('item.uploadcsv-itemlist')}}"/>
         <input type="hidden" class="" id="search_url" value="{{route('item.filter-search')}}"/>
         <input type="hidden" id="export"  value="{{route('items.export_items')}}"/>
         <input type="hidden" id="deleted_items" value="{{route('item.deleted')}}"/>
        <div class="row">
         
              
  <div class="col-md-2 col-sm-12 col-xs-12 form-group" >
   <div class="input-group">
  <span class="input-group-text " style="background-color:grey;color:white">Item Code:</span>
  <input type="text" aria-label="First name" class="form-control"  name="item_code" id="item_code" oninput="searchItemByCode()">
   
</div>
  </div>

   <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
  <div class="input-group">
  <span class="input-group-text"  style="background-color:grey;color:white">Item Name:</span>
  <input type="text" aria-label="First name" class="form-control" name="item_name" id="item_name" oninput="searchByName()">
   
</div>
  </div>


   <div class="col-md-3 col-sm-12 col-xs-12 form-group" >
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <label class="input-group-text" for="inputGroupSelect01" style="background-color:grey;color:white">Laboratory:</label>
  </div>
  <select class="custom-select" id="inputGroupSelect01" name="item_lab"  onchange="searchByLab(this.value)">
   <option value="100">All</option>
     @foreach ($laboratory as $lab)
       <option value="{{$lab->id}}">{{$lab->lab_name}}</option>
   @endforeach
   <option value="999">Other</option>
  </select>
</div>
</div>


   <div class="col-md-3 col-sm-12 col-xs-12 form-group" hidden>
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <label class="input-group-text" for="search_by_section" style="background-color:grey;color:white">Section:</label>
  </div>
  <select class="custom-select" id="search_by_section" name="item_section"  onchange="searchBySection(this.value)">
   <option value=""></option>
     @foreach ($sections as $section)
       <option value="{{$section->id}}">{{$section->section_name}}</option>
   @endforeach
  
  </select>
</div>
</div>

   
 <div class="col-md-3 col-sm-12 col-xs-12 form-group"  hidden>
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <label class="input-group-text" for="inputGroupSelect01" style="background-color:grey;color:white">Item Category:</label>
  </div>
  <select class="custom-select" id="by_category" name="item_category"  onchange="searchByCategory()">
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
</div>
 <div class="col-md-3 col-sm-12 col-xs-12 form-group" hidden>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="checkbox" name="inlineRadioOptions" id="inlineRadio1" value="option1">
  <label class="form-check-label" for="inlineRadio1">Active Only</label>
</div>


</div>

</div>
<div>
    
    <br>
<hr><br>

       <div class="btn-group  btn-group-sm" role="group" aria-label="Basic example" style="float:right">
        <a  href="{{route('admin.create')}}" class="btn btn-secondary" id="add_new" style="border-radius: 4px;" ><i class="fa fa-plus" ></i> Add</a>&nbsp;&nbsp;
       
     <button type="button" class="btn btn-primary" id="deactivate_item" style="x" onclick="deactivateItem()" hidden><i class="fa fa-check"></i> Deactivate</button>&nbsp;&nbsp;
          <button type="button" class="btn btn-info" id="print_items" style="x" hidden><i class="fa fa-print" ></i> Print</button>&nbsp;&nbsp;
           <a href="{{route('items.export_items')}}" type="button" class="btn btn-secondary" id="export_list" style="border-radius: 4px;" ><i class="fa fa-share"></i> Export List</a>&nbsp;&nbsp;
          @if (auth()->user()->authority==1 ||auth()->user()->authority==2 )
               <button type="button" class="btn btn-secondary" id="import_list" style="border-radius: 4px;" onclick="inputFile()" hidden><i class="fa fa-download"></i> Import  List</button>&nbsp;&nbsp;
                <button type="button" class="btn btn-danger"  style="border-radius: 4px;" onclick="deletedItems()" ><i class="fa fa-trash"></i> Deleted Items</button>&nbsp;&nbsp;
                
          @endif
  

</div>
</div>
</div>
  
    <div class="row">
 <div class="col-sm-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"><strong>Item list</strong></h5>
        <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm table-hover" id="items_table">
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
      <th scope="col">Laboratory</th>
       <th scope="col">Options</th>
    </tr>
  </thead>
  <tbody>
</table>
      </div>
      
     </div>
       </div>
         </div>
  </div>
</div>

</div>

<!---content end-->



 <!-- /.container-fluid -->
<div class="modal" tabindex="-1" id="infor" role="dialog">
  <div class="modal-dialog modal-xl" role="document" >
    <div class="modal-content" id="edit_item">
</div></div>
</div>
            </div>

            @endsection

            @push('js')
               
            
       <script src="{{asset('assets/admin/js/items/items.js') }}"></script>  
  
  
@endpush
<!-- Modal -->