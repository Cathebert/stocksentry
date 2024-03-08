
      <div class="modal-header">
        <h5 class="modal-title">Marked for Consolidation</h5>
      <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" action="{{ route('order.export') }}">
        @csrf
        <input type="hidden" id="consolidate_marked" value="{{route('order.show_marked')}}"/>
        <input type="hidden" id="get_details" value="{{route('orders.get_details')}}"/>
        <input type="hidden" id="remove_consolidate" value="{{route('requisition.remove')}}"/>
<div class="row" >
      <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Marked For Consolidation </strong></h5>

  

 
        <!---- table start ---->
               <div class="table-responsive">
        <table class="table table-sm" id="marked_requests" width="100%">
<thead class="thead-light">
    <tr>
           
        <th scope="col">#</th>
        <th scope="col">Item Name</th>
      <th scope="col">Requested By</th>
       <th scope="col">Code</th>
       <th scope="col">Unit Issue</th>
      <th scope="col">Is Hazardous</th>
       <th scope="col">Storage Temp.</th>
      <th scope="col">Quantity</th>
      <th scope="col">Cost</th>
      <th scope="col">Total</th>

  
      
    </tr>
  </thead>
  <tbody>
</table>
      </div>
      <script type="text/javascript">

var t;

    var requests_url = $("#consolidate_marked").val();

    t = $("#marked_requests").DataTable({
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
            url: requests_url,
            dataType: "json",
            type: "GET",
        },
 
        AutoWidth: false,
        columns: [
         
           
                {
            data:"id"
        },
        
           { data: "item_name" },
            { data: "request" },
            { data: "code" },
           
            { data: "unit_issue" },
            { data: "is_hazardous" },
            { data: "store_temp" },
            { data: "total" },
             { data: "cost" },
            { data: "quantity" },

           
         
           
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

 

 function format(rowData) {
    let more_details= $('#get_details').val();
    console.log(rowData)
     var div = $("<div/>").addClass("loading").text("Loading...");

     $.ajax({
         url: more_details,
         data: {
           id: rowData.id,
           
         },
         dataType: "json",
         success: function (json) {
           
          var info='<div class="table-responsive"><table class="table table-sm table-striped table-info"><thead> <tr>'
      info+='<th scope="col">#</th><th scope="col">Lab Name</th><th scope="col"> Requested Date</th><th scope="col">Requested By</th>'
       info+='<th scope="col">Approved By</th><th scope="col">Cost</th> <th scope="col">Requested Quantity</th>'
    info+='</tr></thead> <tbody> <tr id="row1">'
    
    var x=1
    console.log(json.data.length);
            for (let index= 0; index < json.data.length; index++) {
               
         info+='<td>' +json.data[index]["sr_number"] +'</td>'    
        info+='<td>' +json.data[index]["lab_name"] +'</td>'
         info+='<td>' +json.data[index]["requested_date"] +'</td>'
         info+='<td>' +json.data[index]["name"] +' '+json.data[index]["last_name"] +'</td>'
        info+='<td>' +json.data[index]["approved_name"] +' '+json.data[index]["approved_lastname"] +'</td>'
          info+='<td>' +json.data[index]["cost"] +'</td>'
            info+='<td>' +json.data[index]["quantity_requested"] +'</td></tr>'
          x++;  
         
            }
            info += "</tbody></table></div>";
            console.log(info)
             div.html(info).removeClass("loading");
         },
     });

     return div;
 }
 function reloadMarked(){
 
     t = $("#marked_requests").DataTable({
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
            url: requests_url,
            dataType: "json",
            type: "GET",
        },
 
        AutoWidth: false,
        columns: [
             {
            className: "dt-control",
            orderable: false,
            data: null,
            defaultContent: "",
        },
            { data: "sr" },
            { data: "request_lab" },
            { data: "request_date" },
            { data: "requested_by" },
            { data: "approved_by" },
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

}
        </script>



<!----------Table end --------->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary"   >Consolidate</button>
      </div>
</form>