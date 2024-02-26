<div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Approvals</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
       
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
        <table class="table table-bordered table-striped table-sm" id="issue_approvals_items"  width="100%">
<thead class="thead-light">
    <tr>
       
       <th width="5%"></th>
         <th width="5%">#</th>
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
 <script type="text/javascript">  
 var p;

var approval_issues = $("#issue_approvals").val();
 p = $("#issue_approvals_items").DataTable({
    processing: true,
    serverSide: true,
    paging: true,
    info: true,
    lengthMenu: [5, 10, 15],
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url: approval_issues,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: false,
    columns: [
        { data: "check", width: "3%" },
        { data: "id", width: "3%" },
        { data: "issue_date", width: "15%" },
        { data: "issue_to", width: "10%" },
        { data: "issue_by", width: "20%" },
        { data: "status", width: "10%" },
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