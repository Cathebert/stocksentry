<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>StockSentry | Expiry report </title>
    <link rel="shortcut icon"
                href="{{asset('assets/icon/logo_white.png')}}">
    <style type="text/css">
        @media print {
            body {
                margin: 3mm 8mm 5mm 5mm;
            }
        }

        @page {
            margin: 3mm 8mm 5mm 5mm;
        }
    </style>
</head>
<body>
       <center>    <img src="https://stocksentry.org/assets/icon/logo_black.png" style="margin-bottom: 0px;" height=70px  ></center>
<table border="0" cellpadding="0" cellspacing="0" width="100%">

    <tr>
        <td>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="2%">
                      <td width="2%">
                      
                 
                      
                    </td> 
                    </td>
                    <td width="75%" valign="top">
                       

                       
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<center>{{$lab_name ??"" }} Consumption Report  </center>
<hr>
<br>

<table border="1" cellpadding="0" cellspacing="0" width="100%"
       style="border-collapse:collapse;font-size: 13px;border: 1px solid #000;">

    <tr>
        <td>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    
                    <td width="36%" valign="top">
                        <table width="100%" border="0" cellpadding="4" cellspacing="0"
                               style="border-left:1px solid black;">
                          
                            <tr>
                                <td style=""><strong>Generated  Date:</strong></td>
                                <td style="">: {{ date('d, F Y')}}</td>
                            </tr>
                           
                            <tr>
                                <td style="border-bottom:0px solid black;" colspan="2"></td>
                                <td style="border-bottom:0px solid black;" colspan="2"></td>
                            </tr>
                            <tr>
                                <td style="border-bottom:0px solid black;" colspan="2"></td>
                                <td style="border-bottom:0px solid black;" colspan="2"></td>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

   
    <tr>
        
        <td height="0" style="border: 0px solid #fff;" border="0" valign="top">
            <table width="100%" border="0" cellpadding="2" cellspacing="0">
                <tr>
                    <td width="3%" align="center"
                        style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;">
                        <strong>#</strong></td>
                    <td width="25%" align="center"
                        style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;">
                        <strong>Name</strong></td>
                    <td width="3%" align="left"
                        style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;"><strong>Catalog Number</strong>
                    </td>
                    <td width="10%" align="left"
                        style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;"><strong>Unit Issue</strong>
                    </td>
            
     <td width="10%" align="left"
                        style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;"><strong>Total Consumption</strong>
                    </td>
                </tr>


                @php
                 $i=1;
                 
                 @endphp
                @if(!empty($items) && count($items)>0)

                  
                  
 
        
               
             
                 
                        
                    @for ($x=0; $x<count($items); $x++ )
                        
                  
              
                        <tr>
                            <td align="center"
                                style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;">{{$i}}</td>
                            <td align="left"
                                style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;"><strong>{{$items[$x]->item_name?? '' }}</strong></td>
                        
                            <td align="center"
                                style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;"><strong>{{ $items[$x]->catalog_number}}</strong></td>
                                  <td align="center"
                                style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;"><strong>{{ $items[$x]->unit_issue}}</strong></td>
                                
                               <td align="center"
                                style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;"><strong>{{ $items[$x]->consumed_quantity}}</strong></td>
                                
                        </tr>        
                        
                        @php $i++; @endphp
                       
                   @if(!empty($consumed)&& count($consumed)>0)
                    @foreach($consumed as $consum)
<tr>
                            <td align="left"
                                style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;"></td>
                        
                                 <td align="left"
                                style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;">{{$consum->lab_name?? '' }}</td>
                        
                            <td align="left"
                                style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;">Batch #:{{ $consum->batch_number }}</td>
                        
                            <td align="center"
                                style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;"></td>
                                  <td align="center"
                                style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;">{{ $consum->consumed_quantity}}</td>
                       </tr>       
                    @endforeach
   @endif
                  @endfor
                @endif
    <tr>
                   
                

                    
                </tr>
            </table>
        </td>
    </tr>


    <tr>
        <td>
            <table border="0" cellpadding="5" cellspacing="0" width="100%">
                <tr>
                    
                      
                    
                      
                    <td style="border-left: 1px solid #000;" valign="top" width="33%">
                     
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>