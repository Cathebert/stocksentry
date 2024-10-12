<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'StockSentry ') }} | Issued</title>
     <link rel="stylesheet" href="{{ asset('assets/css/pdf.css') }}" type="text/css"> 
</head>
<body>
    
                <img src="{{ asset('assets/icon/logo_black.png') }}" style="margin-bottom: 0px;" height=70px >
           
           <hr>
    <table class="w-full">
        <tr>
           <td class="w-half" style="text-align:center">Issued</td>
        </tr>
    </table>
 
    <div class="margin-top">
        
    </div>

    <div class="margin-top">
         <span class="heading4" style="text-align: center;"><strong>Issues</strong></span> <br> 
        <table class="products">
            <tr>
                <th>Sr.</th>
               
                  <th>To Lab</th>
                
                 <th>Issue Date</th>
                  <th>  Status</th>
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
                      {{$dat->siv_number?? '' }}
                    </td>
                   
                    <td>
                      {{$dat->lab_name?? '' }}
                    </td>
                       <td>
                      {{$dat->issuing_date?? '' }}
                    </td>
                     
                    
                    
                     <td>
                     {{ $dat->approve_status }}
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