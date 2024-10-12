<div class="modal-header">
        <h5 class="modal-title">Goods Received Note</h5>
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
                        <h4 class="float-end font-size-15">GRN #: <strong>{{ $value }}</strong> <span class="badge bg-success font-size-12 ms-2"></span></h4>
                        <div class="mb-4">
                             <img src="{{ asset('assets/icon/logo_black.png') }}" style="margin-bottom: 0px;" height=70px >
                        </div>
                        
                    </div>

                    <hr class="my-4">

                    <div class="row" >
                        <div class="col-sm-6" >
                            <div class="text-muted">
                                <h5 class="font-size-16 mb-3"><strong>Supplied By:</strong></h5>
                                <h5 class="font-size-15 mb-2">{{$supplier->supplier_name}}</h5>
                                <p class="mb-1">{{$supplier->address??''}}</p>
                                <p class="mb-1">{{$supplier->email??''}}</p>
                                <p>{{$supplier->phone_number??''}}</p>
                            </div>
                        </div>
                        <!-- end col -->
                        <div class="col-sm-6">
                            <div class="text-muted text-sm-end">
                                <h5 class="font-size-16 mb-3"><strong>Supplied To:</strong></h5>
                                <h5 class="font-size-15 mb-2">{{$lab}}</h5>
                                <p class="mb-1">GRN #: {{$value}}</p>
                                <p class="mb-1">PO REF #: {{$po_ref??''}}</p>
                                <p>Date Received: {{$date_received??''}}</p>
                                
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                    <hr>
                    <div class="py-2">
                        <h5 class="font-size-15">REAGENTS AND CONSUMABLES RECEIVED CHECK OFF </h5>

                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap table-centered mb-0">
                                <thead>
                                    <tr>
                                         <th style="width: 20px;">Item Name</th>
                                        <th>ULN</th>
                                       
                                         <th>Code</th>
                                          <th>Cat Number</th>
                                         <th>Batch Number</th>
                                          <th>Pack Size</th>
                                        <th>Quantity</th>
                                         <th>Any Items expired? (Y/N)</th>
                                          <th>Any Items Damaged? (Y/N)</th>
                                           <th>Items received at correct temperature? (Y/N)</th>
                                             <th>Are Items suitable for use? (Y/N)</th> 
                                    </tr>
                                </thead><!-- end thead -->
                                <tbody>
                                  

                                      @foreach ($print_data  as $data )
                                        <tr>
                                         <td>{{$data->item_name}}</td>
                                          <td>{{$data->uln}}</td>
                                         <td>{{$data->code}}</td>
                                         <td>{{$data->catalog_number}}</td>
                                           <td>{{$data->batch_number}}</td>
                                          <td>{{$data->warehouse_size}}</td>
                                      
                                       
                                        <td>{{$data->quantity}}</td>
                                         <td>{{$data->any_expired}}</td>
                                           <td>{{$data->any_damaged}}</td>
                                          <td>{{$data->correct_temp}}</td>
                                           <td>{{$data->suitable_for_use}}</td>
                                    </tr>
                                   
                                      @endforeach
                                       
                                    <!-- end tr -->
                                 
                                 
                                 
                                    <!-- end tr -->
                                  
                                    <!-- end tr -->
                                </tbody><!-- end tbody -->
                            </table><!-- end table -->
                        </div><!-- end table responsive -->
                        <table class="table align-middle table-nowrap table-centered mb-0">
                                <thead>
                                    <tr>
                                        <th>Checked By</th>
                                          <th>Checked Date</th>
                                          <th>Comment</th>
                                             <th>Signature</th>
</tr>
</thead>
                            <tr>
                                <td>
                                   {{$checker??""}}
                                </td>

                                <td>
                                   {{$checker_date??""}}
                                </td>
                                <td>
                                {{$checker_comment??""}}
                                </td>
                                  <td>
                             @if($checker_signature!="")
                                 <img src="{{ url('/').'/public/upload/signatures/'.$checker_signature}}" width="40px" height="40px"/>
                                @endif
                                </td>
                                <tr>
                            </table>


                       <!---reviewer !-->
                       <table class="table align-middle table-nowrap table-centered mb-0">
                                <thead>
                                    <tr>
                                        <th>Reviewed By</th>
                                          <th>Reviewed Date</th>
                                          <th>Comment</th>
                                             <th>Signature</th>
</tr>
</thead>
                            <tr>
                                <td>
                                   {{$reviewer??""}}
                                </td>

                                <td>
                                   {{$reviewer_date??""}}
                                </td>
                                <td>
                                {{$reviewer_comment??""}}
                                </td>
                                  <td>
                             @if($reviewer_signature!="")
                                 <img src="{{ url('/').'/public/upload/signatures/'.$reviewer_signature}}" width="40px" height="40px"/>
                                @endif
                                </td>
                                <tr>
                            </table>
                       <!--reviewer end -->
                        <div class="d-print-none mt-4">
                            <div class="float-start">
                               <span><strong>Received By:</strong> {{$received_by??""}}</span><br>
                            
                              
                            </div>
                            <div class="float-end">
                              
                             @if($signature!='')
                                
                              <span><strong>Signature:</strong><img src="{{ url('/').'/public/upload/signatures/'.$signature }}" width="70px" height="50px"/></span> 
                             @endif
                              
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- end col -->
  </div>
  
</div>
</div>  
  
</div>