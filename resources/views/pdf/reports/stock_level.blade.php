<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'StockSentry ') }} | Expired</title>
     <link rel="stylesheet" href="{{ asset('assets/css/pdf.css') }}" type="text/css"> 
</head>
<body>
    
                <img src="{{ asset('assets/icon/logo_black.png') }}" style="margin-bottom: 0px;" height=70px >
           
           <hr>
    <table class="w-full">
        <tr>
           <td class="w-half" style="text-align:center">Expired Items</td>
        </tr>
    </table>
 
    <div class="margin-top">
        
    </div>

    <div class="margin-top">
         <span class="heading4" style="text-align: center;"><strong>Stock Levels</strong></span> <br> 
        <table class="products">
            <tr>
                <th>Sr.</th>
                <th>Item Name</th>
                   <th>Code #</th>
             
                 <th>Minimum</th>
                  <th>Maximum</th>
                   <th>Available</th>
                  
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
                     {{ $dat->minimum_level }}
                    </td>
                      <td>
                     {{ $dat->maximum_level }}
                    </td>
                  
                    
                      <td>
                 {{ $dat->stock_on_hand}}
                    </td>
                       
                         </tr>
                            @php $i++; @endphp
                    @endforeach
       
            @endif
        </table>
    </div>
 
    
    <hr>

    <div class="footer margin-top" >
        <div style="text-align:center">Thank you</div>
        
    </div>
</body>
</html>