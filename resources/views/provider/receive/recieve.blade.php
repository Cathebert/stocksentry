@extends('provider.layout.main')
@section('title','Receive Item')
@section('content')


 <div class="container-fluid">
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('moderator.home')}}">Home</a></li>
    <li class="breadcrumb-item"><a href="#">Inventory</a></li>
    <li class="breadcrumb-item active" aria-current="page">Receive</li>
  </ol>
</nav>
  

        <!-------TAB LIST--->
<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">New Receipt</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">All Receipts</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false" hidden> Contact</a>
  </li>
</ul>
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
    <!--- home Tab Start --->
   

 @include('provider.receive.receive_tabs.new_receipt');

    <!--home Tab End---->


  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
   @include('provider.receive.receive_tabs.lab_received_receipts')
 
  <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">...</div>
</div>








 



       



    <!-- /.container-fluid -->

            </div>
             <div class="modal" tabindex="-1" id="inforg" role="dialog" >
  <div class="modal-dialog modal-lg" role="document" >
    <div class="modal-content" id="receive_item">

          
            @endsection
            @push('js')
       <script src="{{asset('assets/admin/js/inventory/receive_inventory.js') }}"></script>
  
                <script src="{{asset('assets/moderator/receive/received.js') }}"></script>

  
@endpush