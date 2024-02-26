
      <div class="modal-header">
        <h5 class="modal-title">Update Inventory Consumption</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close_modal">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
<div class="row" >
      <div class="col-sm-12">
    
    <div class="card">
 
      <div class="card-body">
        
        <h5 class="card-title"> <strong>Update Consumption </strong></h5>
<form method="post" id="consume_form">
          @csrf
          <input type="hidden" class="form-control" id="inventory_update_all" value="{{route('inventory.update_all')}}">
           <input type="hidden" class="form-control" id="update_selected" value="{{route('inventory.selected_update')}}">
              <input type="hidden" class="form-control" id="item_search" value="{{route('items.search')}}">
               <input type="hidden" class="form-control" id="inventory_received" value="{{route('item.recieved')}}">
  

 
            
            <div class="row">
    <div class="col-md-4 col-sm-12 col-xs-12 form-group"  >
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <label class="input-group-text" for="period">Period</label>
    <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
  </div>
  <select class="custom-select" id="period" name="period" onchange="getSelected()">
    <option value=""selected> Select Period</option>
    <option value="1" >Today</option>
    <option value="2">This Week</option>
    <option value="3">This Month</option>
  </select>
</div>
  </div>
  
     <div class="col-md-6 col-sm-12 col-xs-12 form-group" >
  <div class="input-group">
  <span class="input-group-text">Custom Range:</span> 
  <input type="date" aria-label="First name" class="form-control" id="start_date" name="start_date">
   <span class="input-group-text">-</span>
  <input type="date" aria-label="Last name" class="form-control " id="end_date" name="end_date">
</div>
  </div>
  
</form>

</div>
 <h6 id="status" style="text-align:center"> </h6>
</div>
 <script type="text/javascript">
 function getSelected (){
  var value=$('#period').val();
if(value==1){

$('#status').html("As of <strong>"+ getTodaysDate()+"</strong>")
 }
 if(value==2){
  const getWeekBehind=()=>{

var v= new Date(new Date().setDate(new Date().getDate() - 7));
  return v.toUTCString().slice(5, 16);
 
  }
   $('#status').html("From <strong>"+ getWeekBehind()+"</strong> - <strong>"+getTodaysDate()+"</strong>")
 }
 if(value==3){
  let date_today = new Date();

let firstDay =  moment().startOf('month').format('DD MMM YYYY');
let lastDay =  moment().endOf('month').format('DD MMM YYYY');
//let month=firstDay.toUTCString().slice(5, 16);
 $('#status').html("From <strong>"+firstDay+ "</strong>- <strong>"+lastDay+"</strong>")

 }

 function getTodaysDate(){
   const date = new Date();
  return date.toUTCString().slice(5, 16);;
 }
  }
  </script>
<hr></br>


</form>

      </div>
    </div>
        <!---- table start ---->
               <div class="table-responsive">
        <table class="table table-sm" id="inventories" width="100%">
<thead class="thead-light">
    <tr>
       <th scope="col"></th>
     <th scope="col">Code</th>
       <th scope="col">Brand</th>
         <th scope="col">Batch Number</th>
        <th scope="col">Generic Name</th>
        <th scope="col">UOM </th>
         <th scope="col">Available </th>
          <th scope="col">Consumed</th>
  
       <th scope="col">Action</th>
      
    </tr>
  </thead>
  <tbody>
</table>
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
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="close_modal_n">Close</button>
        <button type="button" class="btn btn-primary" id="update_all"  onclick="updateAll()">Update All</button>
      </div>
   <script type="text/javascript">
    $('#close_modal').on('click',function(){
       $('#infor').modal('toggle')
    })
   $('#close_modal_n').on('click',function(){
       $('#infor').modal('toggle')
    })

    
   </script>