<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'StockSentry ') }} |Users</title>
     <link rel="stylesheet" href="{{ asset('assets/css/pdf.css') }}" type="text/css"> 
</head>
<body>
    
                <img src="{{ asset('assets/icon/logo_black.png') }}" style="margin-bottom: 0px;" height=70px >
           
           <hr>
    <table class="w-full">
        <tr>
           <td class="w-half" style="text-align:center">{{$lab_name}}</td>
        </tr>
    </table>
 


    <div class="margin-top">
         <span class="heading4" style="text-align: center;"><strong>Users List</strong></span> <br> 
        <table class="products">
            <tr>
            <th>#</th>
                <th>User Name</th>
                <th> First Name</th>
                 <th>Last  Name </th>
                <th>Email</th>
                 <th>Phone</th>
                  <th>Lab</th>
                  
            </tr>
                  
               @php $x=1 @endphp
                @if(!empty($users) && count($users)>0)

                  
         
                @foreach ($users as $user)
              
                
                     <tr class="items">
 
               
        
                 
                        
                    
                    
                   
                     <td>
                      {{$x }}
                    </td>
                     <td>
                      {{$user->username?? '' }}
                    </td>
                    
                       <td>
                      {{$user->name?? '' }}
                    </td>
                     <td>
                      {{$user->last_name?? '' }}
                    </td>
                    
                     <td>
                     {{$user->email??""}}
                    </td>
                    
                     <td>
                   {{ $user->phone_number??""}}
                    </td>
                    
                      <td>
                 {{ $user->lab_name??""}}
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