  <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Disposed Items</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
         <input type="hidden" id="modal_view_dispose_url" value="{{route('disposal.viewmodal')}}"/>
        </button>
      </div>
      <div class="modal-body">
           <div class="card">
  <div class="card-header text-success" >
    <strong>Disposed Items</strong>
  </div>
  <div class="card-body">
    <h5 class="card-title">Disposed</h5>
     <div class="table-responsive">
        <table class="table table-sm table-striped" id="disposal_list"  width="100%">
<thead class="thead-light">
    <tr>
       
    
         <th width="5%"> #</th>
          <th width="20%">Date</th>
          <th width="20%">Disposed By</th>
            <th width="20%">Approved By</th>
              <th width="20%">Items affected</th>
             <th width="20%">Action</th>
           
  </thead>
</table>
  </div>
   </div>
   <script type="text/javascript">
    var get_approved=$("#items_disposal_list").val();
  

    var y="";
   y = $("#disposal_list").DataTable({
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
        { data: "dispose_date", width: "10%" },
        { data: "disposed_by", width: "15%" },
        { data: "approved_by", width: "15%" },
        { data: "items", width: "15%" },
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