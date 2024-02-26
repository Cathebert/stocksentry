
      <div class="modal-header">
        <h5 class="modal-title">{{$lab_name}} -( {{$item}} )Summary</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close_modal">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
<div class="row" >
      <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
       @if(!empty($consumed) && count($consumed)>0)
        <h5 class="card-title"> <strong>Consumption Summary </strong></h5>
 
 
     <ul class="list-group">
     @foreach ($consumed as $details)
 
  <li class="list-group-item">Amount Consumed: {{$details->consumed_quantity}}</li>
  <li class="list-group-item">Date: {{$details->created_at}}</li>
  <li class="list-group-item">Code:{{$details->batch_number}}</li>
 
    
  @endforeach
  </ul>
  
 
</div>

</div>
 
<hr></br>


</form>

      </div>
    </div>
    <hr></br>
    @endif
        <!---- table start ---->
            <div class="card" >
  <h5 class="card-title"> <strong>Order Summary </strong></h5>
  <div class="card-body">
    <li class="list-group-item">Orders Consolidated: {{$orders_consolidated}}</li>
  <li class="list-group-item">Orders Received: {{$orders_received}}</li>
  <li class="list-group-item">Orders Pending:{{$orders_pending}}</li>
  </div>
</div>
<hr></br>

<div class="card" >
<h5 class="card-title"> <strong>Issue Summary </strong></h5>
  <div class="card-body">
    <li class="list-group-item">Issued Out: {{$issued_out}}</li>
  <li class="list-group-item">Issue Received: {{$issued_received}}</li>

  </div>
</div>
      <script type="text/javascript">
var inventory = $("#load_inventory").val();
var t;

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    t = $("#inventories").DataTable({
        processing: true,
        serverSide: true,
        paging: true,
       scrollCollapse: true,
    scrollY: '200px',
        info: true,

        lengthMenu: [5, 10, 15],
        responsive: true,
        order: [[0, "desc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: inventory,
            dataType: "json",
            type: "GET",
        },

        AutoWidth: false,
        columns: [
            { data: "id", width: "3%" },
            { data: "code", width: "15%" },
            { data: "brand", width: "15%" },
            { data: "batch_number" },
            { data: "name" },
            { data: "unit" },
            { data: "available" },
            { data: "consumed" },
            { data: "status" },
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



<!----------Table end --------->
      </div>
     
   <script type="text/javascript">
    $('#close_modal').on('click',function(){
       $('#inforg').modal('toggle')
    })
   

    
   </script>