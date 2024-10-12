<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'StockSentry ') }} | Disposal</title>
     <link rel="stylesheet" href="https://stocksentry.org/assets/css/pdf.css" type="text/css"> 
</head>
<body>
    
                <img src="https://stocksentry.org/assets/icon/logo_black.png" style="margin-bottom: 0px;" height=70px >
           
           <hr>
    <table class="w-full">
        <tr>
           <td class="w-half" style="text-align:center">{{$lab_name ?? "" }} Item Disposal</td>
        </tr>
    </table>
 
    <div class="margin-top">
        
    </div>

    <div class="margin-top">
         <span class="heading4" style="text-align: center;"><strong>Item Disposed</strong></span> <br> 
        <table class="products">
            <tr>
                <th>Sr.</th>
                <th>Item Name</th>
                  <th>Code</th>
                 <th>Batch #</th>
                 <th>Disposed Date</th>
                  <th>Remark</th>
                  <th>Disposed Quantity</th>
                   <th>Cost</th>
                
                    <th>Total</th>
                   
            </tr>
                          @php $i=1;
                  $Total=0;
                 $x=0;
                 $dat= array();
                 
  $sub=0;
                 @endphp
                @if(!empty($info) && count($info)>0)

                  
         
                @foreach ($info as $dat)
                     <tr class="items">
 
               
                          
                    @php
                  
                    
                          $sub=$dat->cost*$dat->dispose_quantity;
                                          $Total=$Total+$sub;
                                         
                    @endphp
                   
                    
                    <td>
                        {{ $i }}
                    </td>
                     <td>
                      {{$dat->item_name?? '' }}
                    </td>
                    <td>
                      {{$dat->code}}
                    </td>
                    <td>
                      {{$dat->batch_number}}
                    </td>
                    <td>
                      {{$dat->created_at}}
                    </td>
                     <td>
                     {{ $dat->remarks }}
                    </td>
                       <td>
                      {{$dat->dispose_quantity?? '' }}
                    </td>
                      <td>
                      {{$dat->cost?? '' }}
                    </td>
                    
                       <td>
                {{ $dat->dispose_quantity*$dat->cost }}
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
  
  

    <div class="footer margin-top" >
        <div style="text-align:center">Thank you</div>
        
    </div>
</body>
</html>