<div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close_modal">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
<input type="hidden" id="table_health" value="{{route('stats.table')}}"/>
                     <div class="table-responsive">
        <table class="table table-sm" id="details" width="100%">
<thead class="thead-light">
    <tr>
       <th scope="col"></th>
     <th scope="col">Lab Name</th>
       <th scope="col">{{$label}}</th>
      
    </tr>
  </thead>
  <tbody>
</table>
      </div>
        
      </div>
    

         <script type="text/javascript">
    $('#close_modal').on('click',function(){
       $('#infor').modal('toggle')
    })

   </script>

   <script type="text/javascript">
    $(document).ready(function () {
        var id="{{$id}}"
var q=""
var data_url=$('#table_health').val()
    q = $("#details").DataTable({

        processing: true,
    
        serverSide: true,
        destroy: true,
        paging: false,
        select: true,
        info: false,
        lengthMenu: [5, 10, 15],
        responsive: true,

        order: [[0, "desc"]],
        oLanguage: {
            sProcessing:
                "<div class='loader-container'><div id='loader'></div></div>",
        },
        ajax: {
            url: data_url,
            dataType: "json",
            type: "GET",
            data:{id:id},
        },

        AutoWidth: false,
        columns: [
            { data: "id", width: "3%" },
            { data: "lab", width: "35%" },
            { data: "count", width: "40%" },
            
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
});
    </script>