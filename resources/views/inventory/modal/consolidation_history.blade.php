   
<div class="modal-header">
        <h5 class="modal-title">Consolidated Orders History</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="Close_Button"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="load_consolidate" value="{{route('consolidated.load_history')}}"/>
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
   

  
<hr>
                <div class="card-body">
                    
                    <div class="invoice-title">
                   
            <!--h4 class="float-end font-size-15">Status: <span class="badge bg-success font-size-12 ms-2">hello</span></h4>-->

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
                                    <!--h5 class="font-size-12 mb-1">Disposal Date:<strong> </strong></h5-->
                                </div>
                               
                                
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                        
<h5 class="font-size-15"><strong>Consolidation   History</strong></h5>
                    <div class="py-2">
                        

                          <div class="table-responsive">
        <table class="table table-sm" id="consolidate_history_list" width="100%">
<thead class="thead-light">
    <tr>
       <th scope="col"></th>
     <th scope="col"> Date</th>
       <th scope="col">Consolidated By</th>
        <th scope="col">Orders Affected</th>
        <th scope="col">Consolidated Document</th>
        
       
       
      
    </tr>
  </thead>
  <tbody>

        
  
                                 
                                  
</table>
      
</div>
                       
                        
                   
        </div><!-- end col -->
  </div>
  
</div>
</div>  
  
</div>
<script type="text/javascript">
var histori=$('#load_consolidate').val()
var cons='';
 cons = $("#consolidate_history_list").DataTable({
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
             url: histori,
             dataType: "json",
             type: "GET",
         },

         AutoWidth: false,
         columns: [
              { data: "id" },
             { data: "date" },
             { data: "consolidated_by" },
             { data: "orders" },
             { data: "document" },
          
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