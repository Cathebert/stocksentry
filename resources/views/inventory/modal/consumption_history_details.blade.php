   
<div class="modal-header">
        <h5 class="modal-title">Consumption Taken Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="Close_Button"></button>
      </div>
      <div class="modal-body">
  <div class="row">
        <div class="col-lg-12">
            <div class="card" style="box-shadow: 0 20px 27px 0 rgb(0 0 0 / 5%);position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 0 solid rgba(0,0,0,.125);
    border-radius: 1rem;">
   

   <script type="text/javascript">

    function Close(){
        $('#Close_Button').click();
       t.destroy()
         LoadTable();
    }
    </script>
<hr>
                <div class="card-body">
                    
                    <div class="invoice-title">
                   
                        <div class="mb-4">
                             <img src="{{ asset('assets/icon/logo_black.png') }}" style="margin-bottom: 0px;" height=70px >
                        </div>
                        
                    </div>

                    <hr class="my-4">

                    <div class="row" >
                        <div class="col-sm-6">
                            <div class="text-muted" hidden>
                                
                            </div>
                        </div>
                        <!-- end col -->
                        <div class="col-sm-6">
                            <div class="text-muted text-sm-end">
                                <div>
                                    <h5 class="font-size-12 mb-1">Date Captured:<strong> {{$date}}</strong></h5>
                                </div>
                                <div class="mt-4">
                                <h5 class="font-size-12 mb-1">Consumption Period:<strong>{{$type}}</strong></h5>
                                </div>
                                
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                        
<h5 class="font-size-15"><strong>Consumption Take Details</strong></h5>
                    <div class="py-2">
                        

                          <div class="table-responsive">
        <table class="table table-sm" id="inventories_view" width="100%">
<thead class="thead-light">
    <tr>
       <th scope="col"></th>
     <th scope="col">Item Name</th>
       <th scope="col">Code</th>
        <th scope="col">Batch Number</th>
        <th scope="col">Catalog #</th>
        <th scope="col">UOM </th>
         <th scope="col">Consumed</th>
       
        
      
    </tr>
  </thead>
  <tbody>
</table>
      </div>
      <div class="d-print-none mt-4">
                            <div class="float-start">
                               <span><strong>Captured By:</strong> {{$consumption_taker}}</span><br>
                            
                              
                            </div>
                            <br>
</div>
</div>
                        
                    
           
            </div>
        </div><!-- end col -->
  </div>
  
</div>
</div>  
  
</div>
<script type="text/javascript">
    $(document).ready(function () {
    let  consumption_history="{{route('consumption.update_table_data')}}"
    let consumption_take_id="{{$id}}"
    let t=''
    t = $("#inventories_view").DataTable({
    processing: true,
    serverSide: true,
    destroy: true,
    lengthMenu: [10, 50, 100],
    responsive: true,
    order: [[0, "desc"]],
    oLanguage: {
        sProcessing:
            "<div class='loader-container'><div id='loader'></div></div>",
    },
    ajax: {
        url: consumption_history,
        dataType: "json",
        type: "GET",
        data:{
            id:consumption_take_id,
        }
    },

    AutoWidth: true,
   
         
    columns: [
        { data: "id",  },
        { data: "name",width:"30%"  },
        { data: "code",  },
         { data: "batch_number",  },
        { data: "brand",  },
        { data: "unit_issue" },
        { data: "consumed",  },
        
       
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