<?php

namespace App\Http\Controllers\Section;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BinCard;
use App\Models\Inventory;
use App\Models\Supplier;
use App\Models\ReceivedItem;
use App\Models\Item;

class SectionBinCardController extends Controller
{
    //

    public function loadSectionBinCard(Request $request){
         $date=date('Y-m-d');
   
         $columns = array(
            0=>'id',
            1=>'date',
            2=>'quantity_in',
            3=>'batch_number',
            4=> 'description',
            5=>'supplier',
            6=>'cost',
            7=>'expiry_date',
            8=>'quantity_out',
            9=>'balance',
           
        ); 
          $invent=BinCard::where([['inventory_id','=',$request->id],['lab_id','=',auth()->user()->laboratory_id]])->select('item_id')->first();
        if($request->id){
           
      $totalData = Inventory::where([['item_id','=',$invent->item_id],['lab_id','=',auth()->user()->laboratory_id]])->count();
     
      $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          //$order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');
   
$terms=BinCard::where([['item_id','=',$invent->item_id],['lab_id','=',auth()->user()->laboratory_id]])->get();


        }
        else{
          $totalData = BinCard::where([['inventory_id','=',1],['lab_id','=',auth()->user()->laboratory_id]])->count();
    $totalRec = $totalData;
$terms=BinCard::where([['inventory_id','=',1],['lab_id','=',auth()->user()->laboratory_id]])->get();
          }
          $totalFiltered =  $totalRec ;
//  0 => 'id',
         
          $data = array();
          if (!empty($terms)) {
$x=1;
 
 $t=0;
 $total=0;
 $stocktaken=1;
 if($invent!=NULL){
 $consumed=BinCard::where([['item_id','=',$invent->item_id],['lab_id','=',auth()->user()->laboratory_id],['transaction_type','=','consumed']])->sum('quantity');;
$item_name="";
 $issued_to=BinCard::where([['item_id','=',$invent->item_id],['lab_id','=',auth()->user()->laboratory_id],['transaction_type','=','issued_out']])->sum('quantity');
 $stocktaken_date=BinCard::where([['item_id','=',$invent->item_id],['lab_id','=',auth()->user()->laboratory_id],['transaction_type','=','stocktaken']])->select('date')->latest('id')->first();
  $total_balance = Inventory::where([['item_id','=',$invent->item_id],['lab_id','=',auth()->user()->laboratory_id]])->sum('quantity');
}
else{
   $consumed="";
     $issued_to="";
     $stocktaken_date=NULL;
      $total_balance=0;
}
$image_name="";
//dd($terms);
       $c=url('/').'/assets/icon/pdf.png'; 
      //dd($terms);

       
    
     //
        foreach ($terms as $term) {

         $inventory=Inventory::where('id',$term->inventory_id)->select('cost','quantity','expiry_date','recieved_id')->first();
        $received=ReceivedItem::where('id',$inventory->recieved_id)->select('supplier_id')->first();
        $supplier=Supplier::where('id',$received->supplier_id)->select('supplier_name')->first();
        $items=Item::where('id',$term->item_id)->select('item_name','item_image')->first();  

             $nestedData['id']=$x;
            $nestedData['date']= $term->date; 
            
             switch($term->transaction_type){
            case "supplier":
                    $nestedData['quantity_in'] = $term->quantity;
                    $nestedData['quantity_out']="";
                    $nestedData['balance']= $term->balance;
                break;
            case "issued_out":
                    $nestedData['quantity_in'] = '';
                    $nestedData['quantity_out']=$term->quantity;
                    $nestedData['balance']= $term->balance; 

                break;

            case "consumed":
                    $nestedData['quantity_in'] = '';
                    $nestedData['quantity_out']=$term->quantity;
                    $nestedData['balance']= $term->balance;

                break;
            case "adjusted":
                $nestedData['quantity_in'] = $term->quantity;
                $nestedData['quantity_out']="";
                $nestedData['balance']= $term->balance;

                break;

            case "issue_in":
                $nestedData['quantity_in'] =$term->quantity;
                $nestedData['quantity_out']="";
                $nestedData['balance']= $term->balance;

                break;
            case "stocktaken":
                $nestedData['quantity_in'] =$term->quantity;
                $nestedData['quantity_out']='';
                $nestedData['balance']= $term->balance;
                $stocktaken=0;
             break;

            case "order_sent":
                $nestedData['quantity_in'] = '';
                $nestedData['quantity_out']=$term->quantity;
                $nestedData['balance']= $term->balance;
             break;

             case "order_received":
                $nestedData['quantity_in'] = $term->quantity;
                $nestedData['quantity_out']="";
                $nestedData['balance']= $term->balance;
                break;

            case "disposed":
                $nestedData['quantity_in'] = '';
                $nestedData['quantity_out']=$term->quantity;
                $nestedData['balance']= '';
                break;
             }
    

     
     
  
     
    if($stocktaken_date!=NULL){
    if($term->date > $stocktaken_date->date){
    $stocktaken=1;    
    }
}
            $nestedData['batch_number']= $term->batch_number; 
            $nestedData['description']= $term->description; 
            $nestedData['supplier']= $supplier->supplier_name; 
            $nestedData['cost']  =$inventory->cost;
            $nestedData['expiry_date']=date('d M Y',strtotime($inventory->expiry_date));
                
              
               $t=$term->balance;
               $total= $total_balance;
              
               $image_name=$items->item_image;
               $item_name=$items->item_name;
             
                   $x++;
                $data[] = $nestedData;
           
      }

    }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
        "total"=>$total,
        "consumed"=>$consumed,
        "item_name"=>$item_name??'',
        "out"=>$issued_to??'',
        'stock_taken'=>$stocktaken??'',
        'image'=>$image_name,
    );

      echo json_encode($json_data);

    }
}
