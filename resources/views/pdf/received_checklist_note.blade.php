<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'StockSentry') }} | GRN #{{ $value ?? ''}}</title>
     <link rel="stylesheet" href="{{ asset('assets/css/pdf.css') }}" type="text/css"> 
</head>
<body>
    
                <img src="{{ asset('assets/icon/logo_black.png') }}" style="margin-bottom: 0px;" height=70px >
           
           <hr>
    <table class="w-full">
        <tr>
           <td class="w-half" style="text-align:center">REAGENTS AND CONSUMABLES RECEIVED CHECK OFF NOTE</td>
        </tr>
    </table>
 
    <div class="margin-top">
        <table class="w-full">
            <tr>
                <td class="w-half">
                    <div><h4>Supplied By:</h4></div>
                    <div>{{$supplier->supplier_name?? ''}}</div>
                    <div>{{$supplier->address?? ''}}</div>
                     <div> {{$supplier->email?? ''}}</div>
                      <div>{{$supplier->phone_number?? ''}}</div>
                </td>
                <td class="w-half">
</td>
                <td class="w-half">
                    <div><h4>Supplied To:</h4></div>
                    <div>{{$lab?? ''}}</div>
                    <div></div>
                    <div>Received Date: {{$date_received ??""}}</div>
                     <div><strong>GRN #: {{ $value ?? ''}}</strong></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="margin-top">
         <span class="heading4" style="text-align: center;"><strong>Received Check List</strong></span> <br> 
        <table class="products">
            <tr>
               <th>Item Name</th>
                                        <th>ULN</th>
                                       
                                         <th>Code</th>
                                         <th>Batch Number</th>
                                        <th>Quantity</th>
                                         <th>Any  expired? (Y/N)</th>
                                          <th>Any Damaged? (Y/N)</th>
                                           <th>Correct temperature? (Y/N)</th>
                                             <th>suitable for use? (Y/N)</th> 
            </tr>
                         

                @if(!empty($print_data) && count($print_data)>0)

                  
         
                   @foreach ($print_data  as $data )
                     <tr class="items">
 
              
                                         <td>{{$data->item_name}}</td>
                                          <td>{{$data->uln}}</td>
                                         <td>{{$data->code}}</td>
                                     
                                           <td>{{$data->batch_number}}</td>
                            
                                      
                                       
                                        <td>{{$data->quantity}}</td>
                                         <td>{{$data->any_expired}}</td>
                                           <td>{{$data->any_damaged}}</td>
                                          <td>{{$data->correct_temp}}</td>
                                           <td>{{$data->suitable_for_use}}</td>
                                    </tr>
                                   
                       
                    @endforeach
       
            @endif
        </table>
    </div>
 
   
    <hr>

     <table class="w-full">
                               
                                    <tr >
                                        <th>Checked By :   {{$checker??""}}</th>
                                          <th>Checked Date:   {{$checker_date??""}}</th>
                                          
                                             <th>Signature: @if($checker_signature!="") <img src="{{ url('/').'/public/upload/signatures/'.$checker_signature}}" width="40px" height="40px"/>@endif</th>
</tr>
        
                            </table>
 <table class="w-full">
 <tr>
                <td class="w-half">
                    <div><h4>Checked By Comment :</h4></div>
                    <div> {{$checker_comment??"Not Available"}}</div>
                    
                </td>
</tr>
</table>

                       <!---reviewer !-->
                       <table class="w-full">
                                <thead>
                                    <tr >
                                        <th>Reviewed By:  {{$reviewer??""}}</th>
                                          <th>Reviewed Date:   {{$reviewer_date??""}}</th>
                                             <th>Signature : @if($reviewer_signature!="") <img src="{{ url('/').'/public/upload/signatures/'.$reviewer_signature}}" width="40px" height="40px"/>@endif</th>
</tr>
</thead>
                            </table>

         <table class="w-full">
 <tr>
                <td class="w-half">
                    <div><h4>Reviewed By Comment :</h4></div>
                    <div> {{$reviewer_comment??"Not Available"}}</div>
                    
                </td>
</tr>
</table>
                       
               <table class="w-full">
                                <thead>
                                    <tr >
                                        <th>Reviewed By:  {{$received_by??""}}</th>
                                          <th>Reviewed Date:   {{$date_received??""}}</th>
                                             <th>Signature :@if($signature!="")  <img src="{{ url('/').'/public/upload/signatures/'.$signature}}" width="40px" height="40px"/>@endif</th>
</tr>
</thead>
                            </table>
    <div class="footer margin-top" >
        <div style="text-align:center">Thank you</div>
        
    </div>
</body>
</html>