<div class="modal-header">
        <h5 class="modal-title">Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
<input type="hidden" id="load_adjusted" value="{{route('inventory.adjusted_item')}}"/>
<div class="table-responsive">
        <table class="table table-bordered table-striped table-sm table-hover" id="item_adjusted" width="100%">
    <thead class="thead-light">
    <tr>

      <th scope="col">#</th>
      <th scope="col">Date</th>
       <th scope="col">Adjusted By</th>
         <th scope="col">Approved By</th>
      <th scope="col">Affected Item</th>
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
  var j;
  var load_adjustment_list = $("#load_adjusted").val();

 y = $("#item_adjusted").DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        destroy: true,
        info: true,
        responsive: true,
        order: [[0, "desc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: load_adjustment_list ,
            dataType: "json",
            type: "GET",
        },

        AutoWidth: false,
        columns: [
            { data: "id", width: "3%" },
            { data: "adjust_date", width: "10%" },
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


