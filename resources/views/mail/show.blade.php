@extends('layouts.main')
@section('title','System Mail List')
@section('content')
<div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('admin.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{route('reports.show')}}">System</a></li>
    <li class="breadcrumb-item active" aria-current="page">Mails</li>
  </ol>
</nav>


<div class="row" >
  <div class="col-sm-12">
     <br>
  
      <div class="card">
          
        
          
   

       
		<div class="card-body">
            <input type="hidden" id="mail_url" value="{{route('mail.load')}}"/>
       
     
            <h5 class="card-title"><strong>System Mail List </strong></h5>
        <div class="table-responsive">
        <table class="table table-sm table-striped" id="system_mail_list"  width="100%">
<thead class="thead-light">
    <tr>
         <th width="5%">#</th>
         <th width="20%">Date</th>
        <th width="20%">Lab</th>
          <th width="20%">Subject</th>
          <th width="20%">Type</th>
         
			</tr>
           
  </thead>
</table>
      </div>
       </div>
   
</div>
</div>
</div>
         <div class="modal" tabindex="-1" id="inforg" role="dialog" >
  <div class="modal-dialog modal-lg" role="document" >
    <div class="modal-content" id="receive_item">

    </div>
    </div>
    </div>      

            </div>
          
            @endsection
            @push('js')
       <script src="{{asset('assets/admin/js/mails/mail.js') }}"></script>
  
  
  
@endpush