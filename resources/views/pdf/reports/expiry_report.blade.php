<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>StockSentry | Expiry report </title>
    <link rel="shortcut icon"
                href="{{asset('assets/icon/logo_black.png')}}">
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
<table border="0" cellpadding="0" cellspacing="0" width="100%">

    <tr>
        <td>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="2%">
                      <td width="2%">
                      
                        <img src="{{ public_path('/assets/icon/logo_black.png')}}" style="margin-bottom: 0px;" height=70px >
                      
                    </td> 
                    </td>
                    <td width="75%" valign="top">
                       
<center>Expiry Report  </center>
                       
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

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
                                <td style="">: {{ date('Y-m-d')}}</td>
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
                        <strong>Code</strong></td>
                    <td width="25%" align="left"
                        style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;"><strong>Name</strong>
                    </td>
                    <td width="10%" align="left"
                        style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;"><strong>Batch Number</strong>
                    </td>
            <td width="10%" align="left"
                        style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;"><strong>Expiry Date</strong>
                    </td>
                    <td width="9%" align="center"
                        style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;"><strong>Location</strong>
                    </td>
                    <td width="7%" align="center"
                        style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;">
                        <strong>Cost</strong></td>
                          <td width="7%" align="center"
                        style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;">
                        <strong>Quantity</strong></td>

                    <td width="15%" align="center" style="border-bottom: 1px solid #000;font-size: 9pt;"><strong>Estimated Loss</strong></td>
                </tr>


                @php $i=1;
                  $Total=0;
               
              
                 
  $sub=0;
                 @endphp
                @if(!empty($info) && count($info)>0)

                   @for ($x=0; $x<count($info); $x++)
                  
 
        
               
             
                 
                        
                    @php
                  
                    
                          $sub=$info[$x]->cost*$info[$x]->quantity;
                                          $Total=$Total+$sub;
                                         
                    @endphp
              
                        <tr>
                            <td align="center"
                                style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;">{{$i}}</td>
                            <td align="left"
                                style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;">{{$info[$x]->item_name?? '' }}</td>
                        
                            <td align="center"
                                style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;">{{ $info[$x]->batch_number }}</td>
                                  <td align="center"
                                style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;">{{ $info[$x]->expiry_date }}</td>
                                
                                 <td align="center"
                                style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;">{{ $info[$x]->lab_name}}</td>
                            <td align="right"
                                style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;">{{ $info[$x]->cost }}</td>
  <td align="right"
                                style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;">{{ $info[$x]->quantity}}</td>
                            <td align="center"
                                style="border-right: 1px solid #000;border-bottom: 1px solid #000;font-size: 9pt;">{{ $info[$x]->quantity*$info[$x]->cost }}
                            </td>
                        
                        @php $i++; @endphp
                        </tr>
                   
                    
  
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
                    <td width="72%" valign="top"><strong>Total</strong></br></br>
                    </td>
                    <td width="28%" border="0" style="border-left:1px solid black;">
                        <table width="100%" border="0">
                            <tr>
                           
                                <td width="25%" style="font-weight: bolder;" align="right">{{number_format($Total,2)  }}</td>
                            </tr>

                           

                        </table>
                    </td>
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
