<div class="modal-header">
        <h5 class="modal-title">Disposal Details</h5>
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
                   
            <h4 class="float-end font-size-15">Status: <span class="badge bg-success font-size-12 ms-2">{{$status}}</span></h4>

                        <div class="mb-4">
                             <img src="{{ asset('assets/icon/logo_black.png') }}" style="margin-bottom: 0px;" height=20px >
                        </div>
                        
                    </div>

                    <hr class="my-4">

                    <div class="row" >
                        <div class="col-sm-6">
                            <div class="text-muted" >
                                 <div>
                                <h5 class="font-size-12 mb-1">Disposing Lab:<strong>{{$disposal_lab}}</strong></h5>
                                </div>
                                <div>
                                    <h5 class="font-size-12 mb-1">Disposal Date:<strong> {{ $date }}</strong></h5>
                                </div> 
                            </div>
                        </div>
                        <!-- end col -->
                        <div class="col-sm-6">
                            <div class="text-muted text-sm-end">
                                 <div class="mt-4">
                                   
                               
                                
                            </div>
                        </div>
                      
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                   
                    <div><hr></div>
                    <br>
<h5 class="font-size-15"><strong>Disposal  Details</strong></h5>
                    <div class="py-2">
                        

                          <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm table-hover" id="disposal_view" width="100%">
<thead class="thead-light">
    <tr>
       <th scope="col">#</th>
       <th scope="col" width="30%">Item Name</th>
     <th scope="col">Code</th>
        <th scope="col">Batch #</th>
         <th scope="col">Catalog #</th>
        <th scope="col">UOM </th>
        <th scope="col">Reason</th>
        <th scope="col">Cost </th>
         <th scope="col">Quantity Disposed </th>
        
        <th scope="col">Total</th>
       
      
    </tr>
  </thead>
  <tbody>
@php
  $Total=0;
  $sub=0;
  $i=1;
@endphp
        
  
    <tr>
                                      @foreach ($disposal_details  as $data )
                                     
                                       <th scope="row">{{$i}}</th>
                                        <td>
                                            <div>
                                                <h5 class="text-truncate font-size-14 mb-1">{{$data->item_name}}</h5>
                                                <p class="text-muted mb-0"></p>
                                            </div>
                                        </td>
                                          <td>{{$data->code}}</td>
                                         <td>{{$data->batch_number}}</td>
                                   <td>{{$data->catalog_number}}</td>
                                        <td>{{$data->unit_issue}}</td>
                                         <td>{{$data->remarks}}</td>
                                         <td>{{$data->cost}}</td>
                                         <td>{{$data->dispose_quantity}}</td>
                                         
                                        @php
                                          $sub=$data->cost*$data->dispose_quantity;
                                          $Total=$Total+$sub;
                                           $i++;
                                        @endphp
                                        <td class="text-end">{{ $data->cost*$data->dispose_quantity }}</td>
                                   
                                   </tr>
                                      @endforeach
                                       
                                    <!-- end tr -->

                                 
                                    <tr>
                                        <th scope="row" colspan="4" class="text-end">Total</th>
                                         <td class="text-end"></td>
                                         <td class="text-end"></td>
                                         <td class="text-end"></td>
                                          <td class="text-end"></td>
                                           <td class="text-end"></td>
                                        <td class="text-end "><strong style="color:red">{{number_format($Total,2)  }}</strong></td>
                                    </tr>
                                    <!-- end tr -->
                                  
                                    <!-- end tr -->
                                </tbody><!-- end tbody -->
</table>
      </div>


      <div class="d-print-none mt-4">
                      
                            
</div>
</div>
                        <div class="d-print-none mt-4">
                            <div class="float-start">
                               <span><strong>Disposed By:</strong> {{$disposed_by}}</span><br>
                            
                              
                            </div>
                            <br>
                            <div class="float-start">
                          
                             @if ($signature!='')
                              <span><strong>Signature:</strong><img src="{{ url('/').'/public/upload/signatures/'.$signature }}" width="50px" height="50px"/></span> 
                             @endif
                              
                            </div>
                        </div>

                        
                    </div>
                   @if($is_approved=="yes")
                    <div class="d-print-none mt-4">
                            <div class="float-end">
                               <span><strong>Approved By:</strong> {{$approved_by??""}}</span><br>
                            
                              
                            </div>
                            <br>
                            <div class="float-end">
                              
                             @if ($approver_sign!='')
                                
                              <span><strong>Signature:</strong><img src="{{ url('/').'/public/upload/signatures/'.$approver_sign}}" width="50px" height="50px"/></span> 
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