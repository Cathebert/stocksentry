@extends('provider.layout.main')
@section('title', 'Settings')
@section('content')

<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('moderator.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Settings</a></li>
            <li class="breadcrumb-item active" aria-current="page">Settings</li>
        </ol>
    </nav>

<div class="card-deck">
   <div class="card">
      <div class="card-header bg-primary border-secondary">Notification Settings</div>
    <div class="card-body">

  <div class="card-group">
  <div class="card">

    <div class="card-body">
      <h5 class="card-title"></h5>
      <form method="post" action="{{route('setting.approvals')}}">
     @csrf
<input type="hidden" id="approvals" value="{{route('setting.load')}}"/>
<input type="hidden" id="remove"  value="{{route('setting.remove')}}"/>
  <div class="form-group">
    <label for="exampleFormControlSelect1">Select Email Recipients to  approve transactions </label>
    <select class="form-control" id="email_receivers" name="issue_email_receivers[]" multiple>

        @foreach($users as $user)
                                
         <option value="{{$user->id}}">{{$user->name.' '.$user->last_name}}-{{$user->email}}</option>
          @endforeach
                               
    </select>
  </div>
   <div class="form-group pull-right">
                        <div class="col-md-12 col-sm-6 col-xs-12">
                         

                          <button type="submit" class="btn btn-success"><i class="fa fa-save" id="show_loader"></i>&nbsp;Save</button>
                        </div>
                  </div>
</form>
    </div>
     <script type="text/javascript">
  $(document).ready(function() {
    $('#email_receivers').select2({
 placeholder: 'Select  Email Recipient ',
      allowClear: true,
 
   
    });
    
    
   
});



</script>
<div class="table-responsive">
        <table class="table table-bordered table-striped table-sm table-hover" id="user_table" width="100%">
<thead class="thead-light">
    <tr>
       <th scope="col"></th>
       <th scope="col">Name</th>
       <th scope="col">Email</th>
       <th scope="col">Action</th>
      
    </tr>
  </thead>
  <tbody>
</table>
      </div>
  </div>
  
<script type="text/javascript">
    $(document).ready(function() {
let users = $('#approvals').val();

var t;

t = $("#user_table").DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    scrollCollapse: true,

    info: true,

    lengthMenu: [10, 20, 50],
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url:users,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: false,
    columns: [
        { data: "id", width: "3%" },
        { data: "name", width: "15%" },
          { data: "email", width: "15%" },
        { data: "action" },
        
    ],
    //Set column definition initialisation properties.
    columnDefs: [
        {
            targets: [-1], //last column
            orderable: false, //set not orderable
        },
        {
            targets: [-2], //last column
            orderable: false, //set not orderable
        },
        {
            targets: [-3], //last column
            orderable: false, //set not orderable
        },
    ],
});
}); 
function removeUser(id){
  let remove =$('#remove').val();
     
$.confirm({
    title: "Confirm!",
    content:
        "Are you sure you want to remove this entry?",
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
                $.ajax({
                    method: "POST",
                    dataType: "JSON",
                    url: remove,
                    data: {
                        id:id
                    },
                    success: function (data) {
                        // Welcome notification
                        // Welcome notification
                        toastr.options = {
                            closeButton: true,
                            debug: false,
                            newestOnTop: false,
                            progressBar: false,
                            positionClass: "toast-top-right",
                            preventDuplicates: false,
                            onclick: null,
                            showDuration: "300",
                            hideDuration: "1000",
                            timeOut: "5000",
                            extendedTimeOut: "1000",
                            showEasing: "swing",
                            hideEasing: "linear",
                            showMethod: "fadeIn",
                            hideMethod: "fadeOut",
                        };
                        toastr["success"](data.message);
                      window.location.reload()
                    },
                });
            },
        },

        cancel: function () {},
    },
});
}
  </script>

    </div>
  </div>
  
</div>
<br>
   <div class="card-deck" >
  <div class="card" hidden>
      <div class="card-header bg-primary border-secondary">About to Expire Notification Settings</div>
    <div class="card-body">
      <h5 class="card-title">Card title</h5>
      <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
      <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
    </div>
  </div>
  
</div>
<br>
</div>
  </div>
    </div>


<!-- DataTables JS -->
@push('js')
  
@endpush

@endsection
