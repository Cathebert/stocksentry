<div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Disposed Items</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
         <input type="hidden" id="load_deleted_items" value="{{route('items.load_deleted')}}"/>
         <input type="hidden" id="restore" value="{{route('item.restore')}}"/>
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
   <table class="table table-bordered table-striped table-sm table-hover" id="items_deleted">
<thead class="thead-light">
    <tr>
       <th scope="col">#</th>
      <th scope="col">Item Name</th>
     <th scope="col">Code #</th>
      <th scope="col">Catalog #</th>
      <th scope="col">Image</th>
      <th scope="col">Brand</th>
      <th scope="col">Warehouse Pack Size</th>
      <th scope="col">Hazardous</th>
      <th scope="col">Storage Temp.</th>
      <th scope="col">Unit Of Issue</th>
      <th scope="col">Stock Level</th>
      <th scope="col">Laboratory</th>
       <th scope="col">Options</th>
    </tr>
  </thead>
  <tbody>
</table>
  </div>
   </div>
   <script type="text/javascript">
    var get_approved=$("#load_deleted_items").val();
  

    var y="";
   y = $("#items_deleted").DataTable({
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
            { data: "name",width:"30%" },
            { data: "code", width: "3%" },
            { data: "cat_number" },
            { data: "image", width: "20%" },
            { data: "brand", width: "3%" },
            { data: "warehouse_size" },
            { data: "hazardous" },
            { data: "storage_temp" },
            { data: "unit_issue" },
            { data: "stock_level" },
            { data: "section" },
            { data: "options" },
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