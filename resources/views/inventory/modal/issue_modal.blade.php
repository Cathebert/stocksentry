   
<div class="modal-header">
        <h5 class="modal-title">Issue</h5>
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
     <nav class="navbar navbar-expand-sm  navbar-light bg-secondary">

  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
    
    
     
      <li class="nav-item">
         
    <a  href="{{route('download',['id'=>$siv,'action'=>'download','type'=>'issue'])}}"  target="_blank" class="btn shadow-none  btn-lg" style="outline: none !important; box-shadow: none;color:white" onclick="Close()"><i class="fa fa-download" ></i> Download</a>
      </li>
       <li class="nav-item">
         
    <a href="{{route('download',['id'=>$siv,'action'=>'print','type'=>'issue'])}}" target="_blank"class="btn shadow-none  btn-lg" style="outline: none !important; box-shadow: none;color:white" onclick="Close()"><i class="fa fa-print"></i> Print</a>
      </li>
      
      <li class="nav-item dropdown" hidden>
         <a class="nav-link dropdown-toggle text-lg" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-file"></i>   Export To
        </a>
         <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="#">PDF</a>
          <a class="dropdown-item" href="#">Excel</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Print</a>
        </div>
      </li>
    
    </ul>
  </div>
</nav>

   <script type="text/javascript">

    function Close(){
        $('#Close_Button').click();
     //  t.destroy()
         //LoadTable();
    }
    </script>
<hr>
                <div class="card-body">
                                                
                    <div class="invoice-title" >
                        <h4 class="float-end font-size-15">Stock Transfer #: {{ $siv }} <span class="badge bg-success font-size-12 ms-2">{{$status}}</span></h4>
                        <div class="mb-6">
 <img src="{{ asset('assets/icon/logo_black.png') }}" style="margin-bottom: 0px;" >
                        </div>
                        
                    </div>

                    <hr class="my-4">

                    <div class="row" >
                        <div class="col-sm-6">
                            <div class="text-muted">
                                <h5 class="font-size-16 mb-3">Issued From:<strong>  {{$from_lab}} </strong></h5>
                                <h5 class="font-size-15 mb-3">Issued To:<strong> {{$to_lab}} </strong></h5>
                                
                            </div>
                        </div>
                        <!-- end col -->
                        <div class="col-sm-6">
                            <div class="text-muted text-sm-end">
                                <div>
                                    <h5 class="font-size-15 mb-1">Transfer #:</h5>
                                    <p> {{ $siv }}</p>
                                </div>
                                <div class="mt-4">
                                    <h5 class="font-size-15 mb-1"> Issuing Date:</h5>
                                    <p>{{$issuing_date}}</p>
                                </div>
                                <div class="mt-4" hidden>
                                    <h5 class="font-size-15 mb-1">Order No:</h5>
                                    <p>#1123456</p>
                                </div>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                    
                    <div class="py-2">
                        <h5 class="font-size-15">Issued Items Summary</h5>

                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap table-centered mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 70px;">No.</th>
                                        <th>Item</th>
                                         <th>Batch Number</th>
                                          <th>UOM</th>
                                        
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th class="text-end" style="width: 120px;">Total</th>
                                    </tr>
                                </thead><!-- end thead -->
                                <tbody>
                                    <tr>
@php
  $Total=0;
  $sub=0;
  $i=1;
@endphp
                                      @foreach ($print_data  as $data )
                                        
                                       <th scope="row">{{$i}}</th>
                                        <td>
                                            <div>
                                                <h5 class="text-truncate font-size-14 mb-1">{{$data->item_name}}</h5>
                                                <p class="text-muted mb-0"></p>
                                            </div>
                                        </td>
                                          <td>{{$data->batch_number}}</td>
                                          <td>{{$data->unit_issue}}</td>
                                       
                                        <td>{{$data->cost}}</td>
                                        <td>{{$data->quantity}}</td>
                                        @php
                                          $sub=$data->cost*$data->quantity;
                                          $Total=$Total+$sub;
                                           $i++;
                                        @endphp
                                        <td class="text-end">{{ $data->cost*$data->quantity }}</td>
                                    </tr>
                                   
                                      @endforeach
                                       
                                    <!-- end tr -->
                                 
                                 
                                    <tr>
                                        <th scope="row" colspan="4" class="text-end">Total</th>
                                         <td class="text-end"></td>
                                         <td class="text-end"></td>
                                        <td class="text-end"><strong>{{number_format($Total,2)  }}</strong></td>
                                    </tr>
                                    <!-- end tr -->
                                  
                                    <!-- end tr -->
                                </tbody><!-- end tbody -->
                            </table><!-- end table -->
                        </div><!-- end table responsive -->
</div>
<hr>
                          <table class="table-borderless">
                                <thead>
                                    <tr>
                                        <th>Issued By</th>
                                          <th>Signature</th> 
</tr>
</thead>
                            <tr>
                                <td>
                                   {{$issued_by}}
                                </td>

                            
                             <td>
                                 <img src="{{ url('/').'/public/upload/signatures/'.$signature}}" width="40px" height="40px"/>
                                </td>
                                <tr>
                            </table>

                        <!--approver---->
                        @if($approver!=NULL)
                        <table class="table-borderless">
                                <thead>
                                    <tr>
                                        <th>Approved By</th>
                                          <th>Signature</th> 
</tr>
</thead>
                            <tr>
                                <td>
                                   {{$approver}}
                                </td>

                            
                             <td>
                                 <img src="{{ url('/').'/public/upload/signatures/'.$approver_sign}}" width="40px" height="40px"/>
                                </td>
                                <tr>
                            </table>
@endif

@if($receiver!=NULL)
  <table class="table-borderless">
                                <thead>
                                    <tr>
                                        <th>Received By</th>
                                          <th>Signature</th> 
</tr>
</thead>
                            <tr>
                                <td>
                                   {{$receiver}}
                                </td>

                            
                             <td>
                                 <img src="{{ url('/').'/public/upload/signatures/'.$receiver_sign}}" width="40px" height="40px"/>
                                </td>
                                <tr>
                            </table>

@endif
                   </div>
                   </div> 
                </div>
            </div>
        </div><!-- end col -->
  </div>
  
</div>
</div>  
  
</div>