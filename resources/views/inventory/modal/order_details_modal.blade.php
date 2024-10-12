<div class="modal-header">

        <h5 class="modal-title">Order Details</h5>
       
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="load_orders" value="{{route('order.load')}}"/>
        <input type="hidden" id="order_id" value="{{$id}}"/>
        <input type="hidden" id="mark_all_as_received" value="{{route('order.mark_all_received')}}"/>
        <input type="hidden" id="mark_without_number" value="{{route('order.mark_without_number')}}"/>
         <button type="button" class="btn btn-primary btn-sm pull-right" onclick="markAllAsReceived()">Mark All As Received</button>
<div class="input-group mb-3">

</div>
<div class="table-responsive">
        <table class="table table-sm" id="item_orders" width="100%">
    <thead class="thead-light">
    <tr>
      
          <th scope="col">#</th>
      <th scope="col">Item</th>
       <th scope="col">Supplier</th>
         <th>Purchase Number</th>
      <th scope="col">UOM</th>
      <th scope="col">Ordered Quantity</th>
      <th scope="col">Mark As Received</th>
    
      
      
    </tr>
  </thead>
  <tbody>
</table>
 </div>

</div>
</div>
</div>
</div>

<div class="modal-footer" hidden>
        <button type="button" class="btn btn-primary" onclick="RunForecast()">Run Forecast</button>
       
      </div>
</div>
<script type="text/javascript">
  var p;
  var load_forecast = $("#load_orders").val();
  var id="{{$id}}"
  p = $("#item_orders").DataTable({
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
        url: load_forecast,
        dataType: "json",
        type: "GET",
        data:{
            id:id,
        }
    },

    AutoWidth: false,
    columns: [
        { data: "id" },
        { data: "item" },
        { data: "supplier" },
         { data: "purchase_number" },
      
        { data: "unit" },
        { data: "ordered" },
         { data: "mark_received" },
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
  
p.on("click", "tbody tr", function () {
   document.getElementById('select_all').checked = false;
  let data = p.row(this).data();
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