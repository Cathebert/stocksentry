
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
                        <h5 class="font-size-15">Received Items Summary</h5>

                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap table-centered mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 70px;">No.</th>
                                        <th>Item Name</th>
                                         <th>Batch Number</th>
                                          <th>Expiry</th>
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
                                              <td>{{$data->expiry_date??"n/a"}}</td>
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
                        <div class="d-print-none mt-4">
                            <div class="float-start">
                               <span><strong>Received By:</strong> {{auth()->user()->name.' '.auth()->user()->last_name}}</span><br>


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
