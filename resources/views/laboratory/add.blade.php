 @extends('layouts.main')
@section('title','Create Laboratory')
@section('content')
<div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Laboratory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create</li>
  </ol>
</nav>


<div class="row" >
  <div class="col-sm-9">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"> <strong>Create Laboratory </strong></h5>
        <form method="post" id="form_id" action="{{route('lab.create')}}">
          @csrf
          <input type="hidden" class="form-control" id="post_url" value="{{route('lab.create')}}">
    
 

    <div class="col-md-12 col-sm-12 col-xs-12 form-group">
        <label for="exampleInputEmail1">Laboratory Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="lab_name" name="lab_name" required>
    
     </div>
     <div class="col-md-12 col-sm-12 col-xs-12 form-group">
        <label for="exampleInputEmail1">Lab Code <span class="text-danger">*</span></label>
        <input type="text" class="form-control" placeholder="LC" id="lab_code" name="lab_code" minlength="2" maxlength="2" required  >
    
     </div>
<div class="col-md-12 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Lab Address</label>
    <input type="text" class="form-control" id="lab_address" name="lab_address" required>
  </div>
 <div class="col-md-12 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Lab Location <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="lab_location"  name="lab_location" >
  </div>

 
<div class="col-md-12 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Lab Email</label>
    <input type="text" class="form-control" id="lab_email" name="lab_email">
  </div>
<div class="col-md-12 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Lab Phone Number</label>
    <input type="text" class="form-control" id="lab_phone" name="lab_phone">
  </div>
  <div class="col-md-12 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Has Sections? </label>
    <div class="form-check">
  <input class="form-check-input" type="radio" name="has_section" id="has_section" value="no" checked  onchange="hasChanged(this.value)">
  <label class="form-check-label" for="exampleRadios1">
    No
  </label>
</div>
<div class="form-check">
  <input class="form-check-input" type="radio" name="has_section" id="has_section" value="yes" onchange="hasChanged(this.value)">
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
    $('#sections').hide();
  </script>
 


  
  <button type="submit" class=" btn btn-primary" id="submit"    style="float:right"><i class="fa fa-save" ></i> Save </button>
</form>
      </div>
    </div>
  </div>


  <!--list of Labs-->
  <div class="col-sm-3">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Laboratories</h5>
        <div id="lab_loader">
    <ol class="list-group list-group-numbered">
        @forelse ( $laboratories as $lab)
 
  <li class="list-group-item d-flex justify-content-between align-items-start">
    <div class="ms-2 me-auto">
      <div class="fw-bold">{{ $lab->lab_name }}</div>
      {{ $lab->lab_location }}
    </div>
    <span class="badge bg-primary rounded-pill" hidden><a href="{{route('lab.more',[$lab->id,$lab->lab_name])}}">More</a></span>
  </li>
  <hr>
        @empty
          <li class="list-group-item list-group-item-primary">Empty</li>
        @endforelse
  
</ol>
</div>
    </div>
  </div>

  
</div>


 




<!-- /.container-fluid -->

            </div>
          
            @endsection
            @push('js')
       <script src="{{asset('assets/admin/js/laboratory/add_laboratory.js') }}"></script>
  
  
  
@endpush