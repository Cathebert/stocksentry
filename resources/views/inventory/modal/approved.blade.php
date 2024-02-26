  <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Approved</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
         
        </button>
      </div>
      <div class="modal-body">
           <div class="card">
  <div class="card-header text-success" >
    <strong>Approved Issues</strong>
  </div>
  <div class="card-body">
    <h5 class="card-title">Issues</h5>
     <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm " id="issue_approved_items"  width="100%">
<thead class="thead-light">
    <tr>
       
    
         <th width="5%"> #</th>
          <th width="20%">Issued Date</th>
          <th width="20%">Issued To</th>
          <th width="20%">Issued By</th>
            <th width="20%">Approved By</th>
            <th width="20%">Receipt</th>
             <th width="20%">Action</th>
           
  </thead>
</table>
  </div>
   </div>
   <script type="text/javascript">
    var get_approved=$("#items_approved_take").val();
    console.log(get_approved)
    var j;
   y = $("#issue_approved_items").DataTable({
    processing: true,
    serverSide: true,
    paging: true,
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
        { data: "issued_date", width: "10%" },
        { data: "issued_to", width: "15%" },
        { data: "issued_by", width: "15%" },
        { data: "approved_by", width: "15%" },
        { data: "receipt", width: "15%" },
        { data: "action", width: "40%" },
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