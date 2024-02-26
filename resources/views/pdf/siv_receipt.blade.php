<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'StockSentry ') }} | Stock Transfer # $user['siv_number'] ?? ''}}</title>
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
                    <div><h4>Issued From: {{$user['from_lab']?? ''}}</h4></div>
                    <div>Stock Transfer #: {{ $user['siv_number']?? ''}}</div>
                    <div>Issue Date: {{ $user['issue_date'] ??date('Y-m-d')}}</div>
                   
                </td>
                <td class="w-half">
                    <div><h4>Issued To : {{$user['to_lab']?? ''}} </h4></div>
                   
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

                  
          @for ($y=0; $y<count($info); $y++)
                  
   @php $dat=$info[$y]; @endphp
        
             @for ($x=0;$x<count($dat);$x++)
                     <tr class="items">
 
               
             
                 
                        
                    @php
                  
                    
                          $sub=$dat[$x]->cost*$dat[$x]->quantity;
                                          $Total=$Total+$sub;
                                         
                    @endphp
                 
                    
                    <td>
                        {{ $i }}
                    </td>
                     <td>
                      {{$dat[$x]->item_name?? '' }}
                    </td>
                    
                       <td>
                      {{$dat[$x]->code?? '' }}
                    </td>
                     <td>
                      {{$dat[$x]->batch_number?? '' }}
                    </td>
                    
                     <td>
                     {{ $dat[$x]->unit_issue }}
                    </td>
                    
                     <td>
                   {{ $dat[$x]->cost }}
                    </td>
                    
                      <td>
                 {{ $dat[$x]->quantity}}
                    </td>
                          <td>
                {{ $dat[$x]->quantity*$dat[$x]->cost }}
                    </td>
                         </tr>
                            @php $i++; @endphp
                    @endfor
        @endfor
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
                    <div><h4>Issued By :{{$user['issued_by']}}</h4></div>
                     @if ($user['signature']!='')
                    <div>Signature:</div>
                    <div><img src="{{ url('/').'/public/upload/signatures/'.$user['signature'] }}" width="50px" height="50px"/></div>
                    @endif
                </td>
                   @if($user['approved_by']!='')
                      
                       <td class="w-half" >
                    <div><h4>Approved  By:{{$user['approved_by']}}</h4></div>
                     @if ($user['approver_sign']!='')
                    <div>Signature:</div>
                    <div><img src="{{ url('/').'/public/upload/signatures/'.$user['approver_sign'] }}" width="50px" height="50px"/></div>
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