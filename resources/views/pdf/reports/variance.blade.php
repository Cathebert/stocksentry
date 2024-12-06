<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'StockSentry ') }} | Stock Level</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
     <link rel="stylesheet" href="https://stocksentry.org/assets/css/pdf.css" type="text/css">

</head>
<body>

                <img src="https://stocksentry.org/assets/icon/logo_black.png" style="margin-bottom: 0px;" height=70px >

           <hr>
    <table class="w-full">
        <tr>
           <td class="w-half" style="text-align:center"><strong>{{$user->lab_name ??"" }}</strong> Stock Variance</td>
           <td class="w-half">
                    <div>Supervised By:<strong>{{$user->name.' '.$user->last_name?? ''}}</strong></div>

                    <div></div>
                    <div>Approved By: <strong>{{$approved_by}}</strong></div>
                     <div>Date: <strong>{{ $user->stock_date ?? ''}}</strong></div>
                </td>
        </tr>
    </table>

    <div class="margin-top">

    </div>

    <div class="margin-top">
         <span class="heading4" style="text-align: center;"><strong>Variance Details</strong></span> <br>
        <table class="products">
            <tr>
                <th>Sr.</th>
                <th>Item Name</th>
                   <th>	System Quantity</th>

                 <th>Pysical Count</th>
                  <th>Status</th>


            </tr>
@if(!empty($variance) && count($variance)>0)

                    @php $i=1;

                 @endphp

                @foreach ($variance as $dat)
                     <tr class="items">

               @php
                   if($dat->system_quantity==$dat->physical_count){
$status='<span class="badge badge-success">Good</span>';
}
if($dat->system_quantity>$dat->physical_count){
$status='<span class="badge badge-warning">Underage</span>';
}
if($dat->system_quantity<$dat->physical_count){
$status='<span class="badge badge-danger">Overage</span>';
}
               @endphp





                    <td>
                        {{ $i }}
                    </td>
                     <td>
                      {{$dat->item_name?? '' }}
                    </td>

                       <td>
                      {{$dat->system_quantity?? '' }}
                    </td>
                     <td>
                      {{$dat->physical_count?? '' }}
                    </td>

                     <td>
                     {!! $status !!}
                    </td>



                         </tr>
                       @php
                           $i++
                       @endphp
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
