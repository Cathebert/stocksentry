<div class="modal-header">
        <h5 class="modal-title">Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
<div class="input-group mb-3" hidden>
 <div class="form-check form-check-inline">
      <input type="checkbox" class="form-check-input" id="select_all"  onclick="SelectAll()" />&nbsp;

  <label class="form-check-label" for="select_all"> &nbsp; Select All </label>
</div>
</div>
<div class="table-responsive">
        <table class="table table-bordered table-striped table-sm table-hover" id="item_adjust" width="100%">
    <thead class="thead-light">
    <tr>

      <th scope="col"> #</th>
      <th scope="col">Item Name</th>
      <th scope="col">Code #</th>
      <th scope="col">Batch #</th>
      <th scope="col">Adjusted Date</th>
      <th scope="col">Available</th>
      <th scope="col">Adjusted</th>
    <th scope="col">Adjusted By</th>
    <th scope="col">Type</th>
    <th scope="col">Remarks</th>
      <th scope="col">Status</th>
    <th scope="col">Action</th>



    </tr>
  </thead>
  <tbody>
</table>
 </div>

</div>
</div>
</div>
</div>


</div>
<script type="text/javascript">
  var p;
  var load_forecast = "{{route('inventory.load_adjusted')}}"

  p = $("#item_adjust").DataTable({
    processing: true,
    serverSide: true,
    destroy: true,
    paging: true,
    select: true,
    info: true,
    lengthMenu: [10, 20, 50],
    responsive: true,

    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url: load_forecast,
        dataType: "json",
        type: "GET",
        data:{
id:{{ $id }}
        },
    },

    AutoWidth: false,

    columns: [

        { data: "id" },
        { data: "item",width:"30%" },
        { data: "code" },
        { data: "batch_number" },
        {data:"date"},
        { data: "available" },
        { data: "adjusted" },
        { data: "adjusted_by"},
        { data: "type"},
        { data: "remarks",width:"20%"},
        { data: "status",width:"10%"},
        { data: "action",width:"30%"},
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


