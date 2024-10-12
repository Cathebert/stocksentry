<div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Approvals</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
       
        </button>
      </div>
      <div class="modal-body">
            <input type="hidden" id="orders_approvals" value="{{route('order.load_pending_approval')}}"/>
           <div class="card">
  <div class="card-header text-danger" >
    <strong>Pending Orders Approvals</strong>
  </div>
  <div class="card-body">
    <h5 class="card-title">Issues</h5>
     <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm" id="orders_approvals_items"  width="100%">
<thead class="thead-light">
    <tr>
       
       <th width="5%">#</th>
         <th width="5%">Order #</th>
          <th width="20%">Order Date</th>
          <th width="20%">Ordered By</th>
        <th width="20%">Approved?</th>
             <th width="20%">Action</th>
           
  </thead>
</table>
      </div>
  </div>


</div>
 <script type="text/javascript">  
 var p;

var approval_issues = $("#orders_approvals").val();
 p = $("#orders_approvals_items").DataTable({
    processing: true,
    serverSide: true,
    destroy:true,
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
       
        { data: "id", width: "3%" },
        { data: "order_no", width: "15%" },
        { data: "order_date", width: "10%" },
        { data: "order_by", width: "20%" },
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