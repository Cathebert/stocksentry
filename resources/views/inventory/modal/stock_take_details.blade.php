<div class="modal-header">
        <h5 class="modal-title">Stock Taken Details</h5>
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
                        @if($state=="yes")
                                              <h4 class="float-end font-size-15">Stock Taken On:{{ $date }}  <span class="badge bg-success font-size-12 ms-2">Approved</span></h4>

                        @elseif($state=="cancel")
                                               <h4 class="float-end font-size-15">Stock Taken On: {{ $date }} <span class="badge bg-danger font-size-12 ms-2">Cancelled</span></h4>
                        @else
                         <h4 class="float-end font-size-15">Stock Taken On: {{ $date }} <span class="badge bg-warning font-size-12 ms-2">Not Approved</span></h4>
                        @endif
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
                                    <h5 class="font-size-12 mb-1">Stock Date:<strong> {{ $date }}</strong></h5>
                                </div>
                                <div class="mt-4">
                                <h5 class="font-size-12 mb-1">Inventory Area:<strong>{{$area}}</strong></h5>
                                </div>
                                
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                        
<h5 class="font-size-15"><strong>Stock Take Details</strong></h5>
                    <div class="py-2">
                        

                          <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm table-hover" id="inventories_view" width="100%">
<thead class="thead-light">
    <tr>
       <th scope="col">#</th>
     <th scope="col">Item Name</th>
       <th scope="col">Code</th>
        <th scope="col">Batch #</th>
        <th scope="col">UOM </th>
         <th scope="col">Available </th>
        <th scope="col">Physical Count</th>
        <th scope="col">Status</th>
      
    </tr>
  </thead>
  <tbody>
</table>
      </div>
      
           <div class="d-print-none mt-4">
                            <div class="float-start">
                               <span><strong>Captured By:</strong> {{$captured_by?? "N/A"}}</span><br>
                                                          @if ($capturer_sig!='')
                                                          <span><strong>Signature:</strong><img src="{{ url('/').'/public/upload/signatures/'.$capturer_sig}}" width="70px" height="50px"/></span> 
                                
                          
                              
                             @endif

                              
                            </div>
                            <br>
</div>
</div>
      <div class="d-print-none mt-4">
                            <div class="float-start">
                               <span><strong>Employees Involved:</strong> {{$supervisor}}(Supervisor)</span><br>
                               @foreach($employees as $employee)
<span><strong></strong> {{$employee->name.' '.$employee->last_name}}</span><br>
                            @endforeach
                              
                            </div>
                            <br>
</div>
</div>
                        <div class="d-print-none mt-4">
                            <div class="float-end">
                               <span><strong>Supervised By:</strong> {{$supervisor}}</span><br>
                            
                              
                            </div>
                            <br>
                            <div class="float-end">
                              
                             @if ($signature=='')
                                 <span><strong>Signature:</strong><img src="{{ asset('assets/icon/sign.png') }}" width="70px" height="70px"/></span> 
                            @else
                              <span><strong>Signature:</strong><img src="{{ url('/').'/public/upload/signatures/'.$signature }}" width="70px" height="50px"/></span> 
                             @endif
                              
                            </div>
                        </div>

                        
                    </div>
                    @if($state=="yes")
                    <div class="d-print-none mt-4">
                            <div class="float-end">
                               <span><strong>Approved By:</strong> {{$approved_by??""}}</span><br>
                            
                              
                            </div>
                            <br>
                            <div class="float-end">
                              
                             @if ($approver_sign=='')
                                 <span><strong>Signature:</strong><img src="{{ asset('assets/icon/sign.png') }}" width="70px" height="70px"/></span> 
                            @else
                              <span><strong>Signature:</strong><img src="{{ url('/').'/public/upload/signatures/'.$approver_sign }}" width="70px" height="50px"/></span> 
                             @endif
                              
                            </div>
                        </div>
                        @endif
                        
                                @if($state=="cancel")
                    <div class="d-print-none mt-4">
                            <div class="float-end">
                               <span><strong>Cancelled By:</strong> {{$approved_by??""}}</span><br>
                            
                              
                            </div>
                            <br>
                            <div class="float-end">
                              
                             @if ($approver_sign=='')
                                 <span><strong>Signature:</strong><img src="{{ asset('assets/icon/sign.png') }}" width="70px" height="70px"/></span> 
                            @else
                              <span><strong>Signature:</strong><img src="{{ url('/').'/public/upload/signatures/'.$approver_sign }}" width="70px" height="50px"/></span> 
                             @endif
                              
                            </div>
                        </div>
                        @endif
                </div>
            </div>
        </div><!-- end col -->
  </div>
  
</div>
</div>  
  
</div>
<script type="text/javascript">
    $(document).ready(function () {
    let  stock_history="{{route('stock.load_data')}}"
    let stock_take_id="{{$id}}"
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
        url: stock_history,
        dataType: "json",
        type: "GET",
        data:{
            id:stock_take_id,
        }
    },

    AutoWidth: true,
   
         
    columns: [
        { data: "id",  },
        { data: "name", width:'30%' },
        { data: "code",  },
        { data: "batch_number",  },
        { data: "unit_issue" },
        { data: "available",  },
        { data: "physical",  },
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
});
</script>