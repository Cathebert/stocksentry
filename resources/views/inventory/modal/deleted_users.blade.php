<div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Disposed Items</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
         <input type="hidden" id="load_deleted_users" value="{{route('user.load_deleted')}}"/>
         <input type="hidden" id="restore_user" value="{{route('user.restore')}}"/>
        </button>
      </div>
      <div class="modal-body">
           <div class="card">
  <div class="card-header text-success" >
    <strong>Deleted Users</strong>
  </div>
  <div class="card-body">
    <h5 class="card-title">Deleted </h5>
     <div class="table-responsive">
   <table class="table table-bordered table-striped table-sm table-hover" id="users_deleted">
<thead class="thead-light">
    <tr>
          <th width="5%">#</th>
         <th width="20%">User Name</th>
          <th width="20%">First Name</th>
          <th width="20%">Last Name</th>
          <th width="20%">Email</th>
           <th width="20%">Phone</th>
            <th width="20%">Lab</th>
            <th width="20%">Location</th>
            <th width="20%">Action</th>
	</tr>
  </thead>
  <tbody>
</table>
  </div>
   </div>
   <script type="text/javascript">
    var get_approved=$("#load_deleted_users").val();
  

    var y="";
   y = $("#users_deleted").DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    destroy:true,
    info: true,
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url:get_approved,
        dataType: "json",
        type: "GET",
    },

        
           
    AutoWidth: false,
    columns: [
             { data: "id", width: "3%" },
        { data: "username", width: "10%" },
        { data: "name", width: "10%" },
        { data: "last_name", width: "10%" },
        { data: "email", width: "10%" },
        { data: "phone", width: "10%" },
        { data: "lab", width: "10%" },
        { data: "location", width: "10%" },
        { data: "options", width: "40%" },
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

</script>