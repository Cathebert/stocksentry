<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'StockSentry ') }} |Suppliers</title>
     <link rel="stylesheet" href="{{ asset('assets/css/pdf.css') }}" type="text/css"> 
</head>
<body>
    
                <img src="{{ asset('assets/icon/logo_black.png') }}" style="margin-bottom: 0px;" height=70px >
           
           <hr>
    <table class="w-full">
        <tr>
          
        </tr>
    </table>
 


    <div class="margin-top">
         <span class="heading4" style="text-align: center;"><strong>Supplier List</strong></span> <br> 
        <table class="products" style="width:100%">
            <tr>
            <th>#</th>
                <th> Name</th>
                <th>Email </th>
              

                
                              
                  
            </tr>
                  
               @php $x=1 @endphp
                @if(!empty($suppliers) && count($suppliers)>0)

                  
         
                @foreach ($suppliers as $supplier)
              
                
                     <tr class="items">
 
               
        
                 
                        
                    
                    
                   
                     <td>
                      {{$x }}
                    </td>
                     <td>
                      {{trim($supplier->supplier_name)?? '' }}
                    </td>
                     <td>
                      {{trim($supplier->email)?? '' }}
                    </td>
                    
                      
                    
                    
                    
                    
                 
                    
       
                    
          
                    
                     @php $x++ @endphp 
                         </tr>
                            
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