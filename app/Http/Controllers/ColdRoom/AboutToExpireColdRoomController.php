<?php

namespace App\Http\Controllers\ColdRoom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
class AboutToExpireColdRoomController extends Controller
{
    //
    public function loadExpiryTable(Request $request){
            if($request->selected){
           
         $this->getTableData($request);
        }
        else{
        $location="Cold Room";
       $date= \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDays(90);
    $from_date=date('Y-m-d');
         $columns = array(
         0 =>'id',
            1=>'item',
            2=>'batch_number',
            3=>'expire_date',
            4=>'quantity',
            5=>'cost',
            6=>'est_loss',
            7=>'status',
            8=>'lab'
        ); 
    
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')

              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
         ->where('t.storage_location',$location)
           ->where('t.quantity','>',0)
             ->whereBetween(DB::raw('DATE(t.expiry_date)'), array($from_date, $date))
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
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
         ->where('t.storage_location','=',$location)
        ->whereBetween(DB::raw('DATE(t.expiry_date)'), array($from_date, $date))
          //->where('t.expiry_date', '<', $date )
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.expiry_date','asc')
            ->groupBy('s.item_name')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
 
 $t=0;
$total=0;
            foreach ($terms as $term) {


$cost=$term->quantity* $term->cost;
                $nestedData['id']=$x;
                $nestedData['item']=$term->item_name;
              
                    $nestedData['batch_number']= $term->batch_number;
                // $nestedData['name']= $term->item_name;
                    $nestedData['location']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
               $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                 if($diff_in_days<0){
                    $nestedData['status']="<span class='text-danger'>  Expired</span>";  
                 }
                  if($diff_in_days==0){
                    $nestedData['status']="<span class='text-danger'> Expiring Today</span>";  
                 }
                elseif( $diff_in_days>=1 && $diff_in_days <30){
                    $nestedData['status']="<span class='text-danger'>  expiring (".$diff_in_days. " day(s)) </span>";
                }
  else if( $diff_in_days>=30 && $diff_in_days <60){
                    $nestedData['status']="<span class='text-warning'>expiring (".$diff_in_days. " days)</span>";
                }
      elseif( $diff_in_days>=60 && $diff_in_days <90){
                    $nestedData['status']="<span class='text-success'>expiring (".$diff_in_days." days)</span>";
                }
                  else{
                    $nestedData['status']="<span class='text-success'>".$diff_in_days." days  remaining</span>";
                }
            
                   $x++;
                $data[] = $nestedData;
                  $t= $cost;
   $total=$total+$t;
           }
      }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
        "total"=>$total,
        "quantity"=>$totalData,
    );

      echo json_encode($json_data);

} 
}

private function getTableData($request) {
     // dd($request);
    $location="Cold Room";
    parse_str($request->expiry_form,$out);
 $expiry_details= $out;
  //dd($expiry_details['lab']);
         $columns = array(
          0 =>'id',
            1=>'item',
            2=>'batch_number',
            3=>'expire_date',
            4=>'quantity',
            5=>'cost',
            6=>'est_loss',
            7=>'status',
            8=>'lab'
        ); 
   if($expiry_details['lab']!=99 ||  $expiry_details['period']!=99){
    switch ($expiry_details['period']) {
        case 1:
           
              $from_date=  date('Y-m-d', strtotime("-30 days"));
           
                $to_date=date('Y-m-d');
           
            break;
        case 2:
            $days=30;
               $to_date= \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDays($days);
                 $from_date=date('Y-m-d');
            break;
        case 3:
            $days=60;
                $to_date= \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDays($days);
                 $from_date=date('Y-m-d');
            break;
        case 4:
            $days=90;
        $to_date= \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDays($days);
         $from_date=date('Y-m-d');
            break;
        default:
        $days=90;
            $to_date= \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDays($days);
             $from_date=date('Y-m-d');
    }
  
   
      $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
         ->where('t.lab_id','=',$expiry_details['lab'])
          ->where('t.storage_location','=',$location)
             ->whereBetween(DB::raw('DATE(t.expiry_date)'), array($from_date, $to_date))
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
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
          ->where('t.lab_id','=',$expiry_details['lab'])
           ->where('t.storage_location','=',$location)
        ->whereBetween(DB::raw('DATE(t.expiry_date)'), array($from_date, $to_date))
          //->where('t.expiry_date', '<', $date )
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.expiry_date','asc')
            ->get();

          $totalFiltered =  $totalRec ;
           $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
 
 $t=0;
$total=0;
            foreach ($terms as $term) {


$cost=$term->quantity* $term->cost;
                $nestedData['id']=$x;
                $nestedData['item']=$term->item_name;
               
                    $nestedData['batch_number']= $term->batch_number;
                 //$nestedData['name']= $term->item_name;
                    $nestedData['lab']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
           
                 switch ($expiry_details['period']) {
                  case 1:
                     $to =strtotime( $term->expiry_date);
      $from = strtotime(date('Y-m-d'));
                 $diff_in_days =(($to-$from)/86400);
            
                    break;
                  
                  default:
                    $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                    break;
                 }
                 if($diff_in_days < 0){
                    $nestedData['status']="<span class='text-danger'>  Expired</span>";  
                 }
                 elseif($diff_in_days==0){
  $nestedData['status']="<span class='text-danger'>  Expiring Today</span>"; 
                 }
                elseif( $diff_in_days>1 && $diff_in_days <30){
                    $nestedData['status']="<span class='text-danger'>  expiring (".$diff_in_days. " day(s)) </span>";
                }
  else if( $diff_in_days>=30 && $diff_in_days <60){
                    $nestedData['status']="<span class='text-warning'>expiring (".$diff_in_days. " days)</span>";
                }
      elseif( $diff_in_days>=60 && $diff_in_days <90){
                    $nestedData['status']="<span class='text-success'>expiring (".$diff_in_days." days)</span>";
                }
                  else{
                    $nestedData['status']="<span class='text-success'>".$diff_in_days." days  remaining</span>";
                }
            
                   $x++;
                $data[] = $nestedData;
                  $t= $cost;
   $total=$total+$t;
           }
      }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
        "total"=>$total,
        "quantity"=>$totalData,
    );

      echo json_encode($json_data);
   
    }
    //end if
    else{

         $date= \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDays(90);
    $from_date=date('Y-m-d');
         $columns = array(
            0 =>'id',
            1=>'item',
            2=>'batch_number',
            3=>'expire_date',
            4=>'quantity',
            5=>'cost',
            6=>'est_loss',
            7=>'status',
            8=>'lab'
        ); 
    
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
         ->where('t.storage_location','=',$location)
             ->whereBetween(DB::raw('DATE(t.expiry_date)'), array($from_date, $date))
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
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
                ->where('t.quantity','>',0)
        ->where('t.storage_location','=',$location)
        ->whereBetween(DB::raw('DATE(t.expiry_date)'), array($from_date, $date))
          //->where('t.expiry_date', '<', $date )
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%");
                      
                     
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
 
 $t=0;
$total=0;
            foreach ($terms as $term) {


$cost=$term->quantity* $term->cost;
                $nestedData['id']=$x;
                $nestedData['item']=$term->item_name;
                    $nestedData['batch_number']= $term->batch_number;
                 
                    $nestedData['lab']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
                    switch ($expiry_details['period']) {
                  case 1:
                     $to =strtotime( $term->expiry_date);
      $from = strtotime(date('Y-m-d'));
                 $diff_in_days =(($to-$from)/86400);
            
                    break;
                  
                  default:
                    $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                    break;
                 }
                 if($diff_in_days <1){
                    $nestedData['status']="<span class='text-danger'>  Expired</span>";  
                 }
                elseif( $diff_in_days >=1 && $diff_in_days <30){
                    $nestedData['status']="<span class='text-danger'>  expiring (".$diff_in_days. " day(s)) </span>";
                }
  else if( $diff_in_days>=30 && $diff_in_days <60){
                    $nestedData['status']="<span class='text-warning'>expiring (".$diff_in_days. " days)</span>";
                }
      elseif( $diff_in_days>=60 && $diff_in_days <90){
                    $nestedData['status']="<span class='text-success'>expiring (".$diff_in_days." days)</span>";
                }
                  else{
                    $nestedData['status']="<span class='text-success'>".$diff_in_days." days  remaining</span>";
                }
            
                   $x++;
                $data[] = $nestedData;
                  $t= $cost;
   $total=$total+$t;
           }
      }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
        "total"=>$total,
        "quantity"=>$totalData,
    );

      echo json_encode($json_data);
    }

    
  } 
  
}
