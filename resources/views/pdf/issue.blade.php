<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'StockSentry ') }} | Stock Transfer # {{ $value?? ''}}</title>
     <link rel="stylesheet" href="{{ asset('assets/css/pdf.css') }}" type="text/css"> 
</head>
<body>
   
                <img src="{{ asset('assets/icon/logo_black.png') }}" style="margin-bottom: 0px;" height=70px >
           
           <hr>
    <table class="w-full">
        <tr>
           <td class="w-half" style="text-align:center">Issue Transfer Note</td>
        </tr>
    </table>
 
    <div class="margin-top">
        <table class="w-full">
            <tr>
                <td class="w-half">
                    <div><h4>Issued From: {{$from_lab?? ''}}</h4></div>
                    <div>Stock Transfer #: {{ $siv_number ?? ''}}</div>
                    <div>Issue Date: {{ $issue_date ?? ''}}</div>
                   
                </td>
                <td class="w-half">
                    <div><h4>Issued To : {{$to_lab?? ''}} </h4></div>
                   
                </td>
            </tr>
        </table>
    </div>

    <div class="margin-top">
         <span class="heading4" style="text-align: center;"><strong> Issued Item List</strong></span> <br> 
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
                @if(!empty($info) && count($info )>0)

                  
         
                @foreach ($info as $dat)
                     <tr class="items">
 
               
             
                 
                        
                    @php
                  
                    
                          $sub=$dat->cost*$dat->quantity;
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
                 {{ $dat->quantity}}
                    </td>
                          <td>
                {{ $dat->quantity*$dat->cost }}
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
                    <div><h4>Issued By :{{$issued_by}}</h4></div>
                     @if ($signature!='')
                    <div>Signature:</div>
                    <div><img src="{{ url('/').'/public/upload/signatures/'.$signature }}" width="50px" height="50px"/></div>
                    @endif
                </td>
                   @if($approved_by!='')
                      
                       <td class="w-half" >
                    <div><h4>Approved  By:{{$approved_by}}</h4></div>
                     @if ($approver_sign!='')
                    <div>Signature:</div>
                    <div><img src="{{ url('/').'/public/upload/signatures/'.$approver_sign }}" width="50px" height="50px"/></div>
                    @endif
                </td>
                @endif
                
                 @if($receiver!=NULL)
                      
                       <td class="w-half" >
                    <div><h4>Received  By:{{$receiver}}</h4></div>
                     @if ($receiver_sign!='')
                    <div>Signature:</div>
                    <div><img src="{{ url('/').'/public/upload/signatures/'.$receiver_sign }}" width="50px" height="50px"/></div>
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