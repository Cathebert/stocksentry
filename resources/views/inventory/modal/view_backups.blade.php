<div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Approvals</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
       
        </button>
      </div>
      <div class="modal-body">
            <input type="hidden" id="load_backups" value="{{route('backups.load_created')}}"/>
           <div class="card">
  
  <div class="card-body">
  
   <div class="dropdow" style="text-align:right" >
      
       <button  class="btn btn-danger" role="button"onclick="clearAllBackups()" >
  <i class="fa fa-trash"> Clear All</i>
</button>


</div>


  
    <h5 class="card-title">Generated Backups</h5>
     <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm" id="created_backups"  width="100%">
<thead class="thead-light">
    <tr>
       
       <th width="5%">#</th>
         <th width="10%">Name</th>
          <th width="20%">Backup Type</th>
          <th width="20%">Backed By</th>
        <th width="20%">Scheduled By</th>
          <th width="20%">Created At</th>
             <th width="20%">Action</th>
           
  </thead>
</table>
      </div>
  </div>


</div>
 <script type="text/javascript">  
 var p;

var backups_url = $("#load_backups").val();
 p = $("#created_backups").DataTable({
    processing: true,
    serverSide: true,
    destroy:true,
    paging: true,
    info: true,
    lengthMenu: [10, 20, 50],
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url:backups_url,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: false,
    columns: [
       
        { data: "id", width: "3%" },
        { data: "name", width: "15%" },
        { data: "type", width: "10%" },
        { data: "backup_by", width: "20%" },
        { data: "scheduled_by", width: "10%" },
         { data: "created_at", width: "10%" },
        { data: "action", width: "30%" },
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