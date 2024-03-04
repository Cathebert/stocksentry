<div class="modal-header">
        <h5 class="modal-title">Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
           <input type="hidden" id="purchase_place" value="{{route('forecast.filter-load')}}"/>
<div class="input-group mb-3" >
 <div class="form-check form-check-inline" hidden>
      <input type="checkbox" class="form-check-input" id="select_all"  onclick="SelectAll()" />&nbsp;
    
  <label class="form-check-label" for="select_all"> &nbsp; Select All </label>
</div>
</div>
<div class="card">
  <div class="card-body">
   <div class="row" >
     <div class="col-md-4 col-sm-12 col-xs-12 form-group">
    <label for="exampleInputPassword1">Place Of Purchase</label>
    <select class="form-control" id="place_of_purchase" name="place_of_purchase" style="width: 75%"  onchange="getPlaceOfPurchase(this.value)">
    <option value="local">Local</option>
    <option value="international">International</option>
  </select>
  </div>
<div class="col-md-4 col-sm-12 col-xs-12 form-group">
   <label for="lab_id">Lead Time  </label>
  <input type="number" aria-label="First name" id="lead_time" class="form-control" name="lead_time" min="1" value=1 >
  </div>
</div><!---end row--->
  </div>
</div>
<div class="table-responsive">
  
        <table class="table table-bordered table-striped table-sm table-hover" id="item_forcast" width="100%">
    <thead class="thead-light">
    <tr>
      @if(auth()->user()->laboratory_id!=0)
      <th scope="col">Select</th>
      <th scope="col">Item Name</th>
      <th scope="col">Code</th>
      <th scope="col">UOM</th>
      <th scope="col">On Hand</th>
      <th scope="col"> In Store</th> 
      @else

 <th scope="col">Select</th>
      <th scope="col">Item Name</th>
      <th scope="col">Code</th>
      <th scope="col">UOM</th>
      <th scope="col">In Labs</th>
      <th scope="col">On Hand</th> 

      @endif

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
        <button type="button" class="btn btn-primary" onclick="RunForecast()">Run Forecast</button>
       
      </div>
</div>
<script type="text/javascript">
  var p;
  var load_forecast = $("#load_ajusted").val();
  p = $("#item_forcast").DataTable({
    processing: true,
    serverSide: true,
    destroy: true,
    paging: false,
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
    },

    AutoWidth: false,
    columns: [
        { data: "check"},
        { data: "item",width:"30%" },
        { data: "code" },
        { data: "unit" },
        { data: "available" },
        {data:   "in_store"},
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

  
   