<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'StockSentry ') }} |Contracts</title>
     <link rel="stylesheet" href="{{ asset('assets/css/pdf.css') }}" type="text/css"> 
</head>
<body>
    
                <img src="{{ asset('assets/icon/logo_black.png') }}" style="margin-bottom: 0px;" height=70px >
           
           <hr>
    <table class="w-full">
        <tr>
           <td class="w-half" style="text-align:center">Contracts</td>
        </tr>
    </table>
 


    <div class="margin-top">
         <span class="heading4" style="text-align: center;"><strong>Contract List</strong></span> <br> 
        <table class="products">
            <tr>
            <th>#</th>
                <th> Name</th>
                <th> Number</th>
                   <th>Description </th>
                <th>Start Date</th>
                 <th>End Date</th>
                  <th>Contract Type</th>
                   <th>Frequency</th>
                    <th>Contract Unit</th>
                     <th>Supplier/Contractor </th>
            </tr>
                  
               @php $x=1 @endphp
                @if(!empty($contracts) && count($contracts)>0)

                  
         
                @foreach ($contracts as $dat)
                @if($dat->contract_unit==1)
                @php $unit="Month" @endphp
                @else
                  @php $unit="Year" @endphp
                @endif
                
                @if($dat->contract_type==1)
                 @php $contract_type="Supplier" @endphp
                 @else
                  @php $contract_type="Service" @endphp
                  @endif
                
                     <tr class="items">
 
               
             
                 
                        
                    
                    
                   
                     <td>
                      {{$x }}
                    </td>
                     <td>
                      {{$dat->contract_name?? '' }}
                    </td>
                    
                       <td>
                      {{$dat->contract_number?? '' }}
                    </td>
                     <td>
                      {{$dat->contract_description?? '' }}
                    </td>
                    
                     <td>
                     {{ date('d, M Y',strtotime($dat->contract_startdate)) }}
                    </td>
                    
                     <td>
                   {{ date('d, M Y',strtotime($dat->contract_enddate)) }}
                    </td>
                    
                      <td>
                 {{ $contract_type}}
                    </td>
                          <td>
                {{ $dat->frequency }}
                    </td>
                    
                             <td>
                {{ $unit }}
                    </td>
                             <td>
                {{ $dat->contractor_name?? $dat->supplier_name }}
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