<?php

namespace App\Http\Controllers\Section;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Consumption;
use App\Models\ConsumptionDetail;
use DB;
use Carbon\Carbon;

class SectionConsumptionController extends Controller
{
    //load section consumption

    public function loadSectionConsumption(Request $request){
          $columns = array(
            0 =>'id',
            1=>'code',
            2=>'batch_number',
            3=>'brand',
            4=>'name',
            5=>'unit',
            5=>'available',
            6=>'consumed',
            7=>'status',
            8=>'last_update',
            9=>'next_update'
        ); 
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.quantity','t.batch_number','t.cost','s.item_name','t.expiry_date')
          ->where('t.section_id','=',auth()->user()->section_id)
           // ->where('t.expiry_date', '>', date('Y-m-d') )
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.item_id','t.quantity','t.batch_number','t.cost','s.unit_issue','s.item_name','t.expiry_date')
            ->where('t.section_id','=',auth()->user()->section_id)
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.brand','LIKE',"%{$search}%")
                   ->orWhere('t.batch_number','LIKE',"%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%") ;
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.expiry_date','asc')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
  

            foreach ($terms as $term) {

$item=ConsumptionDetail::where([['item_id',$term->id],['section_id',auth()->user()->section_id]])->latest('id')->first();
if($item){
$cons=Consumption::where('id',$item->consumption_id)->select('consumption_type_id','start_date','end_date')->first();



if($cons==NULL){
        $nestedData['last_update']='N/A';
        $nestedData['next_update']='N/A' ;
        $nestedData['consumed'] = "<input type='number'  min='0' id='c_$term->id' size='4' class='form-control' placeholder='Enter Here' name='$term->id' onchange='getText(this.id,this.name)'/>";
        $nestedData['status']="<button class='btn btn-outline-primary' id='$term->id' onclick='saveConsumed(this.id)'> <i class='fa fa-save' arial-hidden='true' id='fa_$term->id'></i></button>";
              
                    }
    if($cons->consumption_type_id==1){
        $today=date('Y-m-d');
        $date = Carbon::createFromFormat('Y-m-d', $cons->end_date);
        $daysToAdd = 1;
        $date = $date->addDays($daysToAdd);
if($today >= $date){
        $nestedData['last_update']=$cons->end_date??"N/A";
        $nestedData['next_update']=$date ;
        $nestedData['consumed'] = "<input type='number'  min='0' id='c_$term->id' size='4' class='form-control' placeholder='Enter Here' name='$term->id' onchange='getText(this.id,this.name)'/>";
        $nestedData['status']="<button class='btn btn-outline-primary' id='$term->id' onclick='saveConsumed(this.id)'> <i class='fa fa-save' arial-hidden='true' id='fa_$term->id'></i></button>";
              
}       
else{
    $nestedData['last_update']='<span> Last Update: </span> '.date('d, M Y', strtotime($cons->end_date));
    $nestedData['next_update']='<span> Next Update: </span>'.$date;
     $nestedData['consumed'] =$item->consumed_quantity;;
      $nestedData['status'] ="<i class='fa fa-lock' aria-hidden='true'></i>";
                    }

        }

        if($cons->consumption_type_id==2){
        $today=date('Y-m-d');
        $date = Carbon::createFromFormat('Y-m-d', $cons->end_date);
        $daysToAdd = 7;
        $date = $date->addDays($daysToAdd); 

        if($today>= $date){
             $nestedData['last_update']=$cons->end_date??'';
             $nestedData['next_update']=$date;
            $nestedData['consumed'] = "<input type='number'  min='0' id='c_$term->id' size='4' class='form-control' placeholder='Enter Here' name='$term->id' onchange='getText(this.id,this.name)'/>";
                 
     $nestedData['status']="<button class='btn btn-outline-primary' id='$term->id' onclick='saveConsumed(this.id)'> <i class='fa fa-save' arial-hidden='true' id='fa_$term->id'></i></button>";   
        }
        else{
      $nestedData['last_update']='<span> Last Update: </span>'. $cons->end_date;
    $nestedData['next_update']='<span> Next Update: </span>'.$date;
      $nestedData['consumed'] =$item->consumed_quantity;
      $nestedData['status'] ="<i class='fa fa-lock' aria-hidden='true'></i>";
        }  
        }
}
else{
         $nestedData['last_update']='N/A';
        $nestedData['next_update']='N/A' ;
        $nestedData['consumed'] = "<input type='number'  min='0' id='c_$term->id' size='4' class='form-control' placeholder='Enter Here' name='$term->id' onchange='getText(this.id,this.name)'/>";
                 
        $nestedData['status']="<button class='btn btn-outline-primary' id='$term->id' onclick='saveConsumed(this.id)'> <i class='fa fa-save' arial-hidden='true' id='fa_$term->id'></i></button>";
              
                    } 



                $nestedData['id']="<input type='checkbox' id='sel_$term->id' name='selected_check' onclick='AddIdToArray(this.id)'/>";
                  $nestedData['batch_number']=$term->batch_number;
                    $nestedData['brand'] = $term->brand;
                $nestedData['code']=$term->code;
              

               
                 $nestedData['name']= $term->item_name;
                  $nestedData['unit']= $term->unit_issue;
                    $nestedData['available']= $term->quantity;
                    


                   $x++;
                $data[] = $nestedData;
           }
      }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
    );

      echo json_encode($json_data);
    }
}
