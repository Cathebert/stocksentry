 @extends('layouts.main')
@section('title','Inventory')
@push('style')
   
@endpush
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{route('inventory.bincard')}}">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Bin Card</li>
  </ol>
</nav>


<!--content start-->

<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Bin Card</strong></h5>
      
   @include('inventory.inventory_tab.header')  
    <div class="clearfix"></div>  
<div class="row">
    <div class="col-3">
      <input type="hidden" id="get_bincard" value="{{route('bincard')}}"/>
      <input type="hidden" id="filter_by_date" value="{{route('bincard.search')}}"/>
      <input type="hidden" id="url" value="{{url('/')}}"/>
      <input type="hidden" id="search_result" value="{{route('inventory.search')}}"/>
      
  <!---Card-->
<div class="card text-center">
  <div class="card-header">
     <div class="dropdown" style="text-align:right" >
  <form class="form-inline my-2 my-lg-0" id="search_form">
      <input class="form-control mr-sm-2" type="search" placeholder="Search Item" aria-label="Search" id="search_term">
      <button class="btn btn-outline-primary my-2 my-sm-0" id="search_button" type="submit" onclick="searchTerm()">Search</button>
    </form>
</div>
  <div class="dropdown" style="text-align:left"  hidden>
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

  </div>
  <div class="card-body">
    @php
      $x=1;
    @endphp
      @if(!empty($items) && count($items)>0)
<ul class="list-group " id="search">

  @foreach ($items as $item )
  <div class="input-group-prepend">
    <span class="input-group-text">{{$x}}</span>
     <li class="list-group-item list-group-item-action list-group-flush" style="text-align:left"  id="{{$item->id}}" onclick="CheckBoxSelect(this.id)"><a> {{$item->item_name}}</a></li>
  </div>
    @php
      $x++
    @endphp
  @endforeach
 

</ul>



  </div>
  <div class="card-footer text-muted">
    {{ $items->links()}}
  </div>
  @else
<h6> No Inventory available </h6>
  @endif
</div>
  </div>





  <div class="col-9">
    
     <div class="card text-center">
      
       <div class="card-header" id="item_name">Bin Card</div>
       
  
    <div class="container">
    <div class="card">
  <div class="card-body">
                <div class="row">

  <div class="col-md-8 col-sm-12 col-xs-12 form-group" >
  <div class="input-group">
  <span class="input-group-text btn btn-secondary">Start date:</span> 
  <input type="date" aria-label="First name" class="form-control" id="start_date" name="start_date">
   <span class="input-group-text  btn btn-secondary">End date:</span>
  <input type="date" aria-label="Last name" class="form-control " id="end_date" name="end_date" onchange="filterDate()">
</div>
  </div>
  </div>
</div>
</div>
  <div class="row">
    <div class="co-8 col-lg-12">
    <br>
<div class="table-responsive">
        <table class="table table-sm" id="bin_card" width="100%">
<thead class="thead-light">
    <tr>
       <th ></th>
     <th >Date of Transaction</th>
       <th >Quantity Transacted</th>
        <th >Batch #</th>
        <th>Description</th>
        <th >Supplier</th>
        <th >Cost </th>
        <th >Expiry date</th>
        <th >Quantity Out</th>
       <th >Balance</th>
    </tr>
  </thead>
  <tbody>
</table>
      </div>
      <form class="form-inline" style="float:right">
  <div class="form-group mb-2">
    <label for="staticEmail2" class="sr-only">Total Remaining Balance:</label>
    <input type="text" readonly class="form-control-plaintext"style="font-weight: bold;" id="staticEmail2" value="Total Remaining Balance:">
  </div>
  <div class="form-group mx-sm-1 mb-1">
    <label for="cost" class="sr-only">Balance</label>
    <input type="text" class="form-control form-control-lg" id="balance" style="font-weight: bold;direction: rtl;" placeholder="0" readonly disabled >
  </div>
  
</form>
    </div>
    
   

  </div>
  <br>
       <div class="card" style="width: 18rem; float:left" >
        
  <h5 class="card-title">Summary</h5>
  <ul class="list-group list-group-flush">
    <li class="list-group-item"><strong>Opening Stock:  <span id="open"></span> </strong></li>
    <li class="list-group-item" hidden>Consumed : <span id="consumed"></span> </li>
    <li class="list-group-item">Issued Out: <span ><a id="out"> </a></span></li>
  </ul>
    <div class="card-footer text-muted">
    <img class="card-img-top"  id='img_card' src="{{ asset('assets/icon/not_available.jpg') }}" alt="Card image cap" style="width: 17rem; height:12rem"  >
</div>
</div>
</div>
  
     
    </div>
	</div>
	
	</div>

	

</div>
            @endsection

    @push('js')
   
   <script src="{{asset('assets/admin/js/inventory/bincard.js')}}"> </script>
  
@endpush