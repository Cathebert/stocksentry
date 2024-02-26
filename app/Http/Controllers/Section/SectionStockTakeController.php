<?php

namespace App\Http\Controllers\Section;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
class SectionStockTakeController extends Controller
{
    

    public function filterSectionStockTake(Request $request){

      $columns = array(
            0 =>'id',
            1=>'code',
            2=>'batch_number',
            3=>'brand',
            4=>'name',
            5=>'unit',
            6=>'consumed',
           
        ); 
      switch($request->id){
        case 0:
  $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.quantity','t.batch_number','t.cost','s.item_name','t.expiry_date')
       ->where([['t.lab_id','=',auth()->user()->laboratory_id],['t.section_id','=',auth()->user()->section_id]])
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
         ->where([['t.lab_id','=',auth()->user()->laboratory_id],['t.section_id','=',auth()->user()->section_id]])
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
        break;

    case 1:
$totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.quantity','t.batch_number','t.cost','s.item_name','t.expiry_date')
          ->where([['t.lab_id','=',auth()->user()->laboratory_id],['t.section_id','=',auth()->user()->section_id],['t.storage_location','=',$request->id]])
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
         ->where([['t.lab_id','=',auth()->user()->laboratory_id],['t.section_id','=',auth()->user()->section_id],['t.storage_location','=',$request->id]])
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
      break;


      case 3:

$totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.quantity','t.batch_number','t.cost','s.item_name','t.expiry_date')
          ->where('t.lab_id',auth()->user()->laboratory_id)
          ->where('t.section_id',auth()->user()->section_id)
          ->where('t.storage_location',$request->id)
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
         ->where('t.lab_id',auth()->user()->laboratory_id)
          ->where('t.section_id',auth()->user()->section_id)
          ->where('t.storage_location',$request->id)
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

      break;


      }
 

         
    
          $data = array();
          if (!empty($terms)) {
$x=1;
  

            foreach ($terms as $term) {



    $nestedData['item_id']=$term->id;

                    $nestedData['id']="<input type='checkbox' id='se_$term->id' name='selected_check' onclick='AddIdToList(this.id)'/>";
                  $nestedData['batch_number']=$term->batch_number;
                    $nestedData['brand']= $term->brand;
                $nestedData['code']=$term->code;
             
                 $nestedData['name']= $term->item_name;
                  $nestedData['unit']= $term->unit_issue;
               
                 $nestedData['consumed'] = "<input type='number'  size='5' id='s_$term->id' min='0' class='form-control' placeholder='Enter Here' name='$term->id' onchange='getPhysicalCount(this.id,this.name)'/>";
                 
                   
                   
     
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
