 @extends('provider.layout.main')
@section('title','Issues')
@push('style')
   
@endpush
@section('content')

 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Issue</li>
  </ol>
</nav>




<!--content start-->

<div class="row" >
  
  <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Issues </strong></h5>
      
       
    
 
            <div class="row">
<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true"> Items Requisitions</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Issue</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Receive Issued</button>
  </li>
   <li class="nav-item" role="presentation">
    <button class="nav-link" id="store-tab" data-bs-toggle="tab" data-bs-target="#store" type="button" role="tab" aria-controls="contact" aria-selected="false">Stock Forecasting</button>
  </li>
 
</ul>
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">  
  @include('provider.issues.tabs.requests')
</div>



  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
      @include('inventory.issues_tab.issue')
   </div>
  <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab"> 
    @include('inventory.issues_tab.issue_to')

  </div>

   <div class="tab-pane fade" id="store" role="tabpanel" aria-labelledby="store-tab">Allows you to key in information about items being returned to the store, that were prior issued out.</div>
</div>

</ul>
</div>

<!---content end-->



 <!-- /.container-fluid -->
<div class="modal fade" id="boot" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Approvals</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <input type="hidden" id="view_issue_siv" value="{{route('issue.view')}}"/>
           <div class="card">
  <div class="card-header text-danger" >
    <strong>Pending Approvals</strong>
  </div>
  <div class="card-body">
    <h5 class="card-title">Issues</h5>
     <div class="table-responsive">
        <table class="table table-sm table-striped" id="issue_approvals_items"  width="100%">
<thead class="thead-light">
    <tr>
       
       <th width="5%"></th>
         <th width="5%">SIV #</th>
          <th width="20%">Issue Date</th>
          <th width="20%">Issue To</th>
          <th width="20%">Issue By</th>
            <th width="20%">Status</th>
             <th width="20%">Action</th>
           
  </thead>
</table>
      </div>
  </div>

</div>
    </div>
  </div>

   <div class="modal" tabindex="-1" id="inforg" role="dialog" >
  <div class="modal-dialog modal-lg" role="document" >
    <div class="modal-content" id="view_item_datails">
</div>
 
  

 


<script type="text/javascript">
    
  </script>

            </div>

</div>
            @endsection

            @push('js')
               
         
        <script src="{{asset('assets/admin/js/inventory/issues/get-issued_to.js') }}"></script>
          <script src="{{asset('assets/admin/js/inventory/issues/approve-issued.js') }}"></script>
      
  
  
@endpush
