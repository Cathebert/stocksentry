<div class="modal-header">
        <h5 class="modal-title">Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
<input type="hidden" id="load_adjustment" value="{{route('inventory.adjustment_list')}}"/>
<div class="table-responsive">
        <table class="table table-bordered table-striped table-sm table-hover" id="item_adjustment" width="100%">
    <thead class="thead-light">
    <tr>

      <th scope="col">Select</th>
      <th scope="col">Item Name</th>
       <th scope="col">Code #</th>
      <th scope="col">Batch #</th>
      <th scope="col">UOM</th>
       <th scope="col">Quantity</th>
      <th scope="col">Expiry</th>



    </tr>
  </thead>
  <tbody>
</table>
 </div>

</div>
</div>
</div>
</div>

<div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="RunAdjustment()">Get Selected</button>

      </div>
</div>
<script type="text/javascript">
  var j;
  var load_adjustment_list = $("#load_adjustment").val();
  var selected=[];
  j = $("#item_adjustment").DataTable({
    processing: true,
    serverSide: true,
    destroy: true,
    paging: true,
    select: true,
    info: false,
    lengthMenu: [10, 20, 50],
    responsive: true,

    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url: load_adjustment_list,
        dataType: "json",
        type: "GET",
    },

    AutoWidth: false,
    columns: [

        { data: "check" },
        { data: "item" },
        { data: "code"},
        { data: "batch" },
        { data: "unit" },
        { data: "quantity"},
        { data: "expiry" },
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

j.on("click", "tbody tr", function () {

  let data = j.row(this).data();
  console.log(data['id'])
  let checkbox = document.getElementById(data['id']);
  checkbox.checked = !checkbox.checked;
  if (checkbox.checked) {
       if(selected.includes(data['id'])){

  }else{
selected.push(data['id']);

  }
  } else {
       if(selected.includes(data['id'])){
selected = selected.filter(function(item) {
    return item !==data['id']
})

  }
  }
  console.log("Spliced: "+selected)
});






function SelectAll(){

    // Check if the 'select_all' checkbox is checked
    if (document.getElementById("select_all").checked) {
        // Select all checkboxes with the class 'checkboxall'
        is_all=true;
        $('.checkboxall').prop('checked', true);
    } else {
        is_all=false;
        // Deselect all checkboxes with the class 'checkboxall'
        $('.checkboxall').prop('checked', false);
    }

}


  </script>


