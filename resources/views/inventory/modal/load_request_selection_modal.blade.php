 <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Items List</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close_item_modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="check_quantity" value="{{route('inventory.check_quantity')}}"/>
           <input type="hidden" id="request_url" value="{{route('requests.create')}}" />
         <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm table-hover" id="request_items" width="100%">
<thead class="thead-light">
    <tr>
       <th scope="col"></th>
         <th scope="col">Item Name</th>
         <th scope="col">Code #</th>
         <th scope="col">Batch #</th>
         <th scope="col">Available </th>
          <th scope="col">Requested</th>
       
  
       <th scope="col">Status</th>
      
    </tr>
  </thead>
  <tbody>
</table>
      </div>
        <div class="products items"></div>
      </div>
      <script type="text/javascript">
   var dat_url = $("#request_url").val();
var t="";

    t = $("#request_items").DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        paging: true,
        select: true,
        info: true,
        lengthMenu: [5, 10, 15],
        responsive: true,

        order: [[0, "desc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: dat_url,
            dataType: "json",
            type: "GET",
        },

        AutoWidth: false,
        columns: [
            { data: "id", width: "3%" },
              { data: "name", width: "40%" },
            { data: "code" },
            { data: "brand"},
            { data: "available", width: "5%" },
            { data: "quantity", width: "20%" },
          
            { data: "status", width: "20%" },
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
      <div class="modal-footer">
      
        <button type="button" class="btn btn-primary" onclick=" getSelectedFromModal()" >Ok</button>
      </div>
    </div>
  </div>
</div>
