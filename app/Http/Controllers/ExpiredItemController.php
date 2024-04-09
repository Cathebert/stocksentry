<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laboratory;
 use App\Models\ScheduleReport;
use App\Models\User;
use App\Models\LaboratorySection;
use App\Models\Supplier;
use DB;
use PDF;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class ExpiredItemController extends Controller
{
    //show expired items for the whole labs

    public function showExpiredItemsReport(){

         $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
              $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
                    $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
$data['laboratories']=Laboratory::get();
           
return view('reports.list.expired',$data);
    }

public function loadExpiredItemsReport(Request $request){

    $date=Carbon::now();
         $columns = array(
            0 =>'id',
            1=>'item',
            2=> 'brand',
            3=>'batch_number',
            4=>'Name',
            5=>'location',
            6=>'expire_date',
            7=>'quantity',
            8=>'cost',
            9=>'est_loss',
            10=>'status',
        ); 
    
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
         
             ->where('t.expiry_date', '<', $date)
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
      
     ->where('t.expiry_date', '<', $date)
         
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
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['location']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
     $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                 
                    $nestedData['status']="<span class='text-danger'>  Expired (".$diff_in_days. " day(s)) ago</span>";  
                 
                  
 
    
            
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

public function loadExpiredItemByPeriod(Request $request){


      parse_str($request->expiry_form,$out);
 $expired= $out;
 $period=$expired['period'];
 $lab= $expired['lab'];
if($period==-1 && $lab==-1){
$this->getMoreData($request);
}
if($lab!=-1 && $period==-1){
  $this->loadExpiredByLab($request);
  
}
if($lab==-1 && $period!=-1){
 switch ($expired['period']) {
        case 0:
             $date=date('Y-m-d');
              $this->getPrevious($request,$date);
        break;
        //yesterday
        case 1:
             $date=date('Y-m-d', strtotime("-1 days"));
           $this->getPrevious($request,$date);
            break;
            //last week
        case 2:
           $start = Carbon::now()->subWeek()->endOfWeek();
            $end = Carbon::now()->addWeek()->startOfWeek();
            $this->getData($request,$start, $end);
            break;
            //this month
        case 3:
        $myDate = date('Y-m-d');
        $start = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->firstOfMonth()
                        ->format('Y-m-d');

        $end = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->lastOfMonth()
                        ->format('Y-m-d');
            $this->getData($request,$start, $end);
            break;
            //this quarter
        case 4:
            $start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');
$this->getData($request,$start, $end);
            break;
            //this year
    case 5:
 $start = Carbon::now()->startOfYear()->format('Y-m-d');
   $end = Carbon::now()->endOfYear()->format('Y-m-d');
   $this->getData($request,$start, $end);
    break;
    //previous week
 case 6:
 $start = Carbon::now()->subWeek()->startOfWeek();
$end = Carbon::now()->subWeek()->endOfWeek();
$this->getData($request,$start, $end);
        break;
   //previous month     
case 7:
 $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
$this->getData($request,$start, $end);
break;

//previous quarter
case 8:
$start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');
$this->getData($request,$start, $end);
break;

//previous year
case 9:
 $start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
 $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');
 $this->getData($request,$start, $end);
break;
case 10:
 $start =$expired['start'];
 $end = $expired['end'];
 $this->getData($request,$start, $end);
break;
    }  
}
if($lab!=-1 && $period!=-1){
 switch ($expired['period']) {
        case 0:
             $date=date('Y-m-d');
              $this->getPreviousWithLab($request,$date);
        break;
        //yesterday
        case 1:
             $date=date('Y-m-d', strtotime("-1 days"));
           $this->getPreviousWithLab($request,$date);
            break;
            //last week
        case 2:
           $start = Carbon::now()->subWeek()->endOfWeek();
            $end = Carbon::now()->addWeek()->startOfWeek();
            $this->getDataWithLab($request,$start, $end);
            break;
            //this month
        case 3:
        $myDate = date('Y-m-d');
        $start = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->firstOfMonth()
                        ->format('Y-m-d');

        $end = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->lastOfMonth()
                        ->format('Y-m-d');
            $this->getDataWithLab($request,$start, $end);
            break;
            //this quarter
        case 4:
            $start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');
$this->getDataWithLab($request,$start, $end);
            break;
            //this year
    case 5:
 $start = Carbon::now()->startOfYear()->format('Y-m-d');
   $end = Carbon::now()->endOfYear()->format('Y-m-d');
   $this->getDataWithLab($request,$start, $end);
    break;
    //previous week
 case 6:
 $start = Carbon::now()->subWeek()->startOfWeek();
$end = Carbon::now()->subWeek()->endOfWeek();
$this->getDataWithLab($request,$start, $end);
        break;
   //previous month     
case 7:
 $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
$this->getDataWithLab($request,$start, $end);
break;

//previous quarter
case 8:
$start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');
$this->getDataWithLab($request,$start, $end);
break;

//previous year
case 9:
 $start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
 $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');
 $this->getDataWithLab($request,$start, $end);
break;
case 10:
 $start =$expired['start'];
 $end = $expired['end'];
 $this->getDataWithLab($request,$start, $end);
break;
    } 
}



}
protected function getMoreData($request){
  $date=Carbon::now();
         $columns = array(
            0 =>'id',
            1=>'item',
            2=> 'brand',
            3=>'batch_number',
            4=>'Name',
            5=>'location',
            6=>'expire_date',
            7=>'quantity',
            8=>'cost',
            9=>'est_loss',
            10=>'status',
        ); 
    
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
         
             ->where('t.expiry_date', '<', $date)
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
      
     ->where('t.expiry_date', '<', $date)
         
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
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['location']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
     $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                 
                    $nestedData['status']="<span class='text-danger'>  Expired (".$diff_in_days. " day(s)) ago</span>";  
                 
                  
 
    
            
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
protected function getData($request,$start_date, $end){
     parse_str($request->expiry_form,$out);
 $expired= $out;
 $lab=$expired['lab'];
     $columns = array(
            0 =>'id',
            1=>'item',
            2=> 'brand',
            3=>'batch_number',
            4=>'Name',
            5=>'location',
            6=>'expire_date',
            7=>'quantity',
            8=>'cost',
            9=>'est_loss',
            10=>'status',
        ); 
 $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
        //->where('t.lab_id',$lab)
             ->whereBetween('t.expiry_date', [$start_date, $end])
           
          ->count();



            $totalRec = $totalData;
$limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
        //->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start_date, $end])
 
         
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.expiry_date','asc')
            ->get();

          $totalFiltered =  $totalRec ;

          $data = array();
          if (!empty($terms)) {
$x=1;
 
 $t=0;
$total=0;
            foreach ($terms as $term) {


$cost=$term->quantity* $term->cost;
                $nestedData['id']=$x;
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['location']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
     $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                 
                    $nestedData['status']="<span class='text-danger'>  Expired (".$diff_in_days. " day(s)) ago</span>";  
                 
                  
 
    
            
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

protected function getDataWithLab($request,$start_date, $end){
     parse_str($request->expiry_form,$out);
 $expired= $out;
 $lab=$expired['lab'];
     $columns = array(
            0 =>'id',
            1=>'item',
            2=> 'brand',
            3=>'batch_number',
            4=>'Name',
            5=>'location',
            6=>'expire_date',
            7=>'quantity',
            8=>'cost',
            9=>'est_loss',
            10=>'status',
        ); 
 $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
        ->where('t.lab_id',$lab)
             ->whereBetween('t.expiry_date', [$start_date, $end])
           
          ->count();



            $totalRec = $totalData;
$limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
        ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start_date, $end])
 
         
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.expiry_date','asc')
            ->get();

          $totalFiltered =  $totalRec ;

          $data = array();
          if (!empty($terms)) {
$x=1;
 
 $t=0;
$total=0;
            foreach ($terms as $term) {


$cost=$term->quantity* $term->cost;
                $nestedData['id']=$x;
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['location']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
     $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                 
                    $nestedData['status']="<span class='text-danger'>  Expired (".$diff_in_days. " day(s)) ago</span>";  
                 
                  
 
    
            
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

protected function getPrevious($request,$date){
     parse_str($request->expiry_form,$out);
 $expired= $out;
 
    $date=Carbon::now();
         $columns = array(
            0 =>'id',
            1=>'item',
            2=> 'brand',
            3=>'batch_number',
            4=>'Name',
            5=>'location',
            6=>'expire_date',
            7=>'quantity',
            8=>'cost',
            9=>'est_loss',
            10=>'status',
        ); 
    
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
           //->where('t.lab_id',$lab)
             ->where('t.expiry_date', '=', $date)
             
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
       // ->where('t.lab_id',$lab)
     ->where('t.expiry_date', '=', $date)
          
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
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['location']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
     $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                 
                    $nestedData['status']="<span class='text-danger'>  Expired (".$diff_in_days. " day(s)) ago</span>";  
                 
                  
 
    
            
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

protected function getPreviousWithLab($request,$date){
     parse_str($request->expiry_form,$out);
 $expired= $out;
 $lab=$expired['lab'];
    $date=Carbon::now();
         $columns = array(
            0 =>'id',
            1=>'item',
            2=> 'brand',
            3=>'batch_number',
            4=>'Name',
            5=>'location',
            6=>'expire_date',
            7=>'quantity',
            8=>'cost',
            9=>'est_loss',
            10=>'status',
        ); 
    
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
           ->where('t.lab_id',$lab)
             ->where('t.expiry_date', '=', $date)
             
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
        ->where('t.lab_id',$lab)
     ->where('t.expiry_date', '=', $date)
          
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
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['location']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
     $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                 
                    $nestedData['status']="<span class='text-danger'>  Expired (".$diff_in_days. " day(s)) ago</span>";  
                 
                  
 
    
            
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
protected function getTableData($request){
      $date=Carbon::now();
         $columns = array(
            0 =>'id',
            1=>'item',
            2=> 'brand',
            3=>'batch_number',
            4=>'Name',
            5=>'location',
            6=>'expire_date',
            7=>'quantity',
            8=>'cost',
            9=>'est_loss',
            10=>'status',
        ); 
    
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
         
             ->where('t.expiry_date', '<', $date)

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
      
     ->where('t.expiry_date', '<', $date)
         
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
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['location']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
     $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                 
                    $nestedData['status']="<span class='text-danger'>  Expired (".$diff_in_days. " day(s)) ago</span>";  
                 
                  
 
    
            
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
public function loadExpiredItemByRange(Request $request){
   
    $range=$request->range;

    switch ($range) {
        case 0:
             $date=date('Y-m-d');
              $this->getRangePrevious($request,$date);
        break;
        //yesterday
        case 1:
             $date=date('Y-m-d', strtotime("-1 days"));
           $this->getRangePrevious($request,$date);
            break;
            //this week
        case 2:
           $start = Carbon::now()->subWeek()->endOfWeek();
            $end = Carbon::now()->addWeek()->startOfWeek();
            $this->getRangeData($request,$start, $end);
            break;
            //this month
        case 3:
        $myDate = date('Y-m-d');
        $start = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->firstOfMonth()
                        ->format('Y-m-d');

        $end = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->lastOfMonth()
                        ->format('Y-m-d');
            $this->getRangeData($request,$start, $end);
            break;
            //this quarter
        case 4:
            $start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');
$this->getRangeData($request,$start, $end);
            break;
            //this year
    case 5:
 $start = Carbon::now()->startOfYear()->format('Y-m-d');
   $end = Carbon::now()->endOfYear()->format('Y-m-d');
   $this->getRangeData($request,$start, $end);
    break;
    //previous week
 case 6:
 $start = Carbon::now()->subWeek()->startOfWeek();
$end = Carbon::now()->subWeek()->endOfWeek();
$this->getRangeData($request,$start, $end);
        break;
   //previous month     
case 7:
 $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
$this->getRangeData($request,$start, $end);
break;

//previous quarter
case 8:
$start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');
$this->getRangeData($request,$start, $end);
break;

//previous year
case 9:
 $start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
 $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');
 $this->getRangeData($request,$start, $end);
break;

    }

}
protected function getRangeData($request,$start_date, $end){
   


     $columns = array(
            0 =>'id',
            1=>'item',
            2=> 'brand',
            3=>'batch_number',
            4=>'Name',
            5=>'location',
            6=>'expire_date',
            7=>'quantity',
            8=>'cost',
            9=>'est_loss',
            10=>'status',
        ); 
 $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
      
             ->whereBetween('t.expiry_date', [$start_date, $end])
           
          ->count();



            $totalRec = $totalData;
$limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
     
     ->whereBetween('t.expiry_date', [$start_date, $end])
 
         
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.expiry_date','asc')
            ->get();

          $totalFiltered =  $totalRec ;

          $data = array();
          if (!empty($terms)) {
$x=1;
 
 $t=0;
$total=0;
            foreach ($terms as $term) {


$cost=$term->quantity* $term->cost;
                $nestedData['id']=$x;
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['location']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
     $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                 
                    $nestedData['status']="<span class='text-danger'>  Expired (".$diff_in_days. " day(s)) ago</span>";  
                 
                  
 
    
            
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
protected function getRangePrevious($request,$date){
    $date=$date;
 
    $date=Carbon::now();
         $columns = array(
            0 =>'id',
            1=>'item',
            2=> 'brand',
            3=>'batch_number',
            4=>'Name',
            5=>'location',
            6=>'expire_date',
            7=>'quantity',
            8=>'cost',
            9=>'est_loss',
            10=>'status',
        ); 
    
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
         
             ->where('t.expiry_date', '=', $date)
             
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
      
     ->where('t.expiry_date', '=', $date)
          
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
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['location']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
     $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                 
                    $nestedData['status']="<span class='text-danger'>  Expired (".$diff_in_days. " day(s)) ago</span>";  
                 
                  
 
    
            
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

protected function loadExpiredByLab($request){
    $lab=$request->lab;


     $date=Carbon::now();
  $columns = array(
            0 =>'id',
            1=>'item',
            2=> 'brand',
            3=>'batch_number',
            4=>'Name',
            5=>'location',
            6=>'expire_date',
            7=>'quantity',
            8=>'cost',
            9=>'est_loss',
            10=>'status',
        ); 
    
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.quantity','t.cost','s.item_name','t.expiry_date')
           ->where('t.lab_id',$lab)
             ->where('t.expiry_date', '<', $date)
             
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
        ->where('t.lab_id',$lab)
     ->where('t.expiry_date', '<', $date)
          
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
                $nestedData['item']=$term->code;
                $nestedData['brand']= $term->brand;
                    $nestedData['batch_number']= $term->batch_number;
                 $nestedData['name']= $term->item_name;
                    $nestedData['location']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
     $to = \Carbon\Carbon::createFromFormat('Y-m-d', $term->expiry_date);
      $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                 $diff_in_days = $from->diffInDays($to);
                 
                    $nestedData['status']="<span class='text-danger'>  Expired (".$diff_in_days. " day(s)) ago</span>";  
                 
                  
 
    
            
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

public function downloadExpired(Request $request){
   

  parse_str($request->expiry_form,$out);
 $expired= $out;
 $lab=$expired['lab'];

 $period=$expired['period'];
  $date=date('Y-m-d');
if($lab==-1 && $period==-1){
    
   $data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
        
     ->where('t.expiry_date', '<', $date)
     ->get();
}
if($lab!=-1 && $period==-1){
 $data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    ->where('t.lab_id',$lab)
     ->where('t.expiry_date', '<', $date)
     ->get();
}

if($lab==-1 && $period!=-1){
    switch ($period) {
        case 0:
         $date=date('Y-m-d');
         $data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
   // ->where('t.lab_id',$lab)
     ->where('t.expiry_date', '=', $date)
     ->get();
        break;
        case 1:
     $date=date('Y-m-d', strtotime("-1 days"));

     $data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    
     ->where('t.expiry_date', '=', $date)
     ->get();
            break;
        
        case 2:
$start = Carbon::now()->subWeek()->endOfWeek();
$end = Carbon::now()->addWeek()->startOfWeek();


$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    //->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start,$end])
     ->get();
            break;

        case 3:
 $myDate = date('Y-m-d');
$start = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->firstOfMonth()
                        ->format('Y-m-d');

$end = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->lastOfMonth()
                        ->format('Y-m-d');
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    //->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
        
        case 4:
$start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');

$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    //->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;

        case 5:
$start = Carbon::now()->startOfYear()->format('Y-m-d');
$end = Carbon::now()->endOfYear()->format('Y-m-d');
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    //->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
        
        case 6:
$start = Carbon::now()->subWeek()->startOfWeek();
$end = Carbon::now()->subWeek()->endOfWeek();
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    //->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
        break;


        case 7:
 $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    //->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
        
        case 8:
$start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');

$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    //->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;

        case 9:
$start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
 $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');

 $data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    //->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
        
        case 10:
$start= $expired['start'];
$end=   $expired['end'];
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    //->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
    }
}

if($lab!=-1 && $period!=-1){
 switch ($period) {
        case 0:
         $date=date('Y-m-d');
         $data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    ->where('t.lab_id',$lab)
     ->where('t.expiry_date', '=', $date)
     ->get();
        break;
        case 1:
     $date=date('Y-m-d', strtotime("-1 days"));

     $data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
      ->where('t.lab_id',$lab)
     ->where('t.expiry_date', '=', $date)
     ->get();
            break;
        
        case 2:
$start = Carbon::now()->subWeek()->endOfWeek();
$end = Carbon::now()->addWeek()->startOfWeek();


$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();

    break;

        case 3:
 $myDate = date('Y-m-d');
$start = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->firstOfMonth()
                        ->format('Y-m-d');

$end = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->lastOfMonth()
                        ->format('Y-m-d');
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
        
        case 4:
$start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');

$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;

        case 5:
$start = Carbon::now()->startOfYear()->format('Y-m-d');
$end = Carbon::now()->endOfYear()->format('Y-m-d');
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
        
        case 6:
$start = Carbon::now()->subWeek()->startOfWeek();
$end = Carbon::now()->subWeek()->endOfWeek();
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
        break;


        case 7:
 $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
        
        case 8:
$start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');

$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;

        case 9:
$start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
 $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');

 $data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
        
        case 10:
$start= $expired['start'];
$end=   $expired['end'];
$data['info'] = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','t.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','t.item_id','t.quantity','t.cost','s.item_name','t.expiry_date')
    ->where('t.lab_id',$lab)
     ->whereBetween('t.expiry_date', [$start, $end])
     ->get();
            break;
    }
   
}
if($request->type=="download"){
 
    $name="expired.pdf";
    $path=public_path('reports').'/expired.pdf';
        
    $pdf=PDF::loadView('pdf.reports.expired_report',$data);
        $pdf->save($path); 
$url=route('report.get_expired_download',['name'=>$name]);

return response()->json([
    'path'=>$name,
    'url'=>$url,

]);

}
if($request->type=="excel"){
$spreadsheet = new Spreadsheet();

     $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(100, 'pt');
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
      $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
 $image = file_get_contents(url('/').'/assets/icon/logo_black.png');
$imageName = 'logo.png';
$temp_image=tempnam(sys_get_temp_dir(), $imageName);
file_put_contents($temp_image, $image);
$drawing->setName('Logo');
$drawing->setDescription('Logo');
$drawing->setPath($temp_image); 
$drawing->setHeight(70);
$drawing->setCoordinates('C2');
$drawing->setOffsetX(110);


$drawing->getShadow()->setDirection(45);
$drawing->setWorksheet($spreadsheet->getActiveSheet());

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->setCellValue('D7', ' EXPIRED ITEM LIST ');
$spreadsheet->getActiveSheet()->getStyle('D7')->getFont()->setBold(true);
     
$spreadsheet->getProperties()->setCreator('cathebert muyila')
    ->setLastModifiedBy('Cathebert muyila')
    ->setTitle('PhpSpreadsheet Table Test Document')
    ->setSubject('PhpSpreadsheet Table Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Table');

// Create the worksheet

    

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()
    ->setCellValue('A8', 'Name')
    ->setCellValue('B8', 'Code')
    ->setCellValue('C8', 'Batch Number')
    ->setCellValue('D8', 'Location')
    ->setCellValue('E8', 'Expiration')
    ->setCellValue('F8', 'Quantity')
    ->setCellValue('G8', 'Cost')
    ->setCellValue('H8', 'Loss');

$num=9;
$total=0;
$overall_total=0;

  for ($x=0; $x<count($data['info']); $x++){
$total=$data['info'][$x]->cost*$data['info'][$x]->quantity;
$overall_total=$overall_total+$total;
  $dat=[

    [
    $data['info'][$x]->item_name,
    $data['info'][$x]->code, 
    $data['info'][$x]->batch_number,
    $data['info'][$x]->lab_name,
    $data['info'][$x]->expiry_date,
    $data['info'][$x]->quantity,
    $data['info'][$x]->cost,
    $total
]
  

  ];
 $spreadsheet->getActiveSheet()->fromArray($dat, null, 'A'.$num);
$num++;
}

$step=$num+1;
//$spreadsheet->getActiveSheet()->fromArray($data, null, 'A2');
$spreadsheet->getActiveSheet()
    ->setCellValue('A'.$step, 'Total');
  $spreadsheet->getActiveSheet()->getStyle('A'.$step)->getFont()->setBold(true);
    $spreadsheet->getActiveSheet()
    ->setCellValue('H'.$step, $overall_total);
    $spreadsheet->getActiveSheet()->getStyle('H'.$step)->getNumberFormat()
    ->setFormatCode('#,##0.00');
    $spreadsheet->getActiveSheet()->getStyle('H'.$step)->getFont()->setBold(true);

// Create Table

$table = new Table('A8:H'.$num, 'Expired_Data');

// Create Columns

// Create Table Style

$tableStyle = new TableStyle();
$tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM2);
$tableStyle->setShowRowStripes(true);
$tableStyle->setShowColumnStripes(true);
$tableStyle->setShowFirstColumn(true);
$tableStyle->setShowLastColumn(true);
$table->setStyle($tableStyle);

// Add Table to Worksheet

$spreadsheet->getActiveSheet()->addTable($table);



// Save

$writer = new Xlsx($spreadsheet);
$writer->save(public_path('reports').'/expired_report.xlsx');
$path=public_path('reports').'/expiry_report.xlsx';
$name='expired_report.xlsx';
$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

];
$url=route('report.expired_download-excel',['name'=>$name]); 
return response()->json([
    'path'=>$name,
    'url'=>$url,
]);

}
}
public function getExpiredFile($name){
   $path=public_path('reports').'/'.$name;
$name=now().'_expired_item_report.pdf';
$headers = [
  'Content-type' => 'application/pdf', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

]; 
return response()->download($path,$name, $headers);

}

public function getExpiredExcelFile($name){
   
    $path=public_path('reports').'/'.$name;
$name=now().'_about_expired_items_report.xlsx';
$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

]; 
return response()->download($path,$name, $headers);
}
}