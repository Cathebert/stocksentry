<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'StockSentry ') }} | SR # {{ $sr ?? '' }}</title>
     <link rel="stylesheet" href="{{ asset('assets/css/pdf.css') }}" type="text/css"> 
</head>
<body>
    
                <img src="{{ asset('assets/icon/logo_black.png') }}" style="margin-bottom: 0px;" height=70px >
           
           <hr>
    <table class="w-full">
        <tr>
           <td class="w-half" style="text-align:center">Item Requisition</td>
        </tr>
    </table>
 
    <div class="margin-top">
        <table class="w-full">
            <tr>
                <td class="w-half">
                    <div><h4>Requesting Lab/Section: {{$lab}}</h4></div>
                    <div>Requested #: {{$sr}}</div>
                    <div>Request Date:{{$requested_date?? ''}}</div>
                     <div>Status:  {{$status?? ''}}</div>
                </td>
                
            </tr>
        </table>
    </div>

    <div class="margin-top">
         <span class="heading4" style="text-align: center;"><strong>Requested Item List</strong></span> <br> 
        <table class="products">
            <tr>
                <th>Sr.</th>
                <th>Item Name</th>
                   <th>Code #</th>
                <th>Batch #</th>
                 <th>UOM</th>
                  <th>Cost</th>
                   <th>Quantity</th>
                    <th>Total</th>
            </tr>
                          @php $i=1;
                  $Total=0;
                 $x=0;
                 $dat= array();
                 
  $sub=0;
                 @endphp
                @if(!empty($requests) && count($requests)>0)

                  
         
                @foreach ($requests  as $dat)
                     <tr class="items">
 
               
             
                 
                        
                    @php
                  
                    
                          $sub=$dat->cost*$dat->quantity_requested;
                                          $Total=$Total+$sub;
                                         
                    @endphp
                    
                    <td>
                        {{ $i }}
                    </td>
                     <td>
                      {{$dat->item_name?? '' }}
                    </td>
                    
                       <td>
                      {{$dat->code?? '' }}
                    </td>
                     <td>
                      {{$dat->batch_number?? '' }}
                    </td>
                    
                     <td>
                     {{ $dat->unit_issue }}
                    </td>
                    
                     <td>
                   {{ $dat->cost }}
                    </td>
                    
                      <td>
                 {{ $dat->quantity_requested}}
                    </td>
                          <td>
                {{ $dat->quantity_requested*$dat->cost }}
                    </td>
                         </tr>
                            @php $i++; @endphp
                    @endforeach
       
            @endif
        </table>
    </div>
 
    <div class="total">
        Total: {{number_format($Total,2)  }}
    </div>
    <hr>
    <table class="w-full">
            <tr>
                    <td class="w-half" >
                    <div><h4>Requested By:{{$issued_by}}</h4></div>
                     @if ($signature!='')
                    <div>Signature:</div>
                    <div><img src="{{ url('/').'/public/upload/signatures/'.$signature }}" width="50px" height="50px"/></div>
                    @endif
                </td>
                   @if($approved_by!="")
                      
                       <td class="w-half" >
                    <div><h4>Approved  By:{{$approved_by}}</h4></div>
                     @if ($approver_sign!='')
                    <div>Signature:</div>
                    <div><img src="{{ url('/').'/public/upload/signatures/'.$approver_sign }}" width="50px" height="50px"/></div>
                    @endif
                </td>
                @endif
                </tr>
                </table>
    <div class="footer margin-top" >
        <div style="text-align:center">Thank you</div>
        
    </div>
</body>
</html>