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
class DisposalReportController extends Controller
{
    //

    public function showDisposal(){
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
           
return view('reports.list.disposed',$data);
        
    }
public function  loadDisposal(Request $request){
     
    $columns = array(
            0 =>'id',
            1=>'item',
            2=>'brand',
            3=>'batch_number',
            4=>'name',
            5=>'quantity',
            6=>'remark',
            7=>'lab',
            8=>'date'
           
        ); 
    
   $totalData = DB::table('inventories as t')
                ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
      
  
         
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('s.item_name','asc')
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
                    $nestedData['lab']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->dispose_quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
   $nestedData['date']= $term->created_at;
                    $nestedData['remark']=$term->remarks;  
                 
                  
 
    
            
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
protected function load(Request $request){

     $columns = array(
            0 =>'id',
            1=>'item',
            2=>'brand',
            3=>'batch_number',
            4=>'name',
            5=>'quantity',
            6=>'remark',
            7=>'lab',
            8=>'date'
           
        ); 
  
   $totalData = DB::table('inventories as t')
                ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
             
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
  
  
         
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('s.item_name','asc')
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
                    $nestedData['lab']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->dispose_quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
                 $nestedData['date']= $term->created_at;
                $nestedData['remark']=$term->remarks;  

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
protected function byLab($request){
 parse_str($request->expiry_form,$out);
 $expired= $out;
 $lab=$expired['lab'];
 $period=$expired['period'];
     $columns = array(
            0 =>'id',
            1=>'item',
            2=>'brand',
            3=>'batch_number',
            4=>'name',
            5=>'quantity',
            6=>'remark',
            7=>'lab',
            8=>'date'
           
        ); 
  
   $totalData = DB::table('inventories as t')
                ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->where('d.lab_id',$lab)
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
                ->where('d.lab_id',$lab)
  
         
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('s.item_name','asc')
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
                    $nestedData['lab']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->dispose_quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
                 $nestedData['date']= $term->created_at;
                $nestedData['remark']=$term->remarks;  

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
protected function getDisposedRangePreviousWithLab($request, $date){
    parse_str($request->expiry_form,$out);
 $expired= $out;
 $lab=$expired['lab']; 
$columns = array(
            0 =>'id',
            1=>'item',
            2=>'brand',
            3=>'batch_number',
            4=>'name',
            5=>'quantity',
            6=>'remark',
            7=>'lab',
            8=>'date'
           
        ); 
  
   $totalData = DB::table('inventories as t')
                ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
                ->where('d.lab_id',$lab)
              ->where('d.created_at',$date)

          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
      ->where('d.lab_id',$lab)
      ->where('d.created_at',$date)
  
         
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('s.item_name','asc')
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
                    $nestedData['lab']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->dispose_quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
   $nestedData['date']= $term->created_at;
                    $nestedData['remark']=$term->remarks;  

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
        "data" =>$data,
        "total"=>$total,
        "quantity"=>$totalData,
    );

      echo json_encode($json_data);    

}
protected function getDisposedRangeWithLab($request,$start_date, $end){
    parse_str($request->expiry_form,$out);
 $expired= $out;
 $lab=$expired['lab']; 
$columns = array(
            0 =>'id',
            1=>'item',
            2=>'brand',
            3=>'batch_number',
            4=>'name',
            5=>'quantity',
            6=>'remark',
            7=>'lab',
            8=>'date'
           
        ); 
  
   $totalData = DB::table('inventories as t')
                ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
                ->where('d.lab_id',$lab)
              ->whereBetween('d.created_at',[$start_date,$end])

          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
      ->where('d.lab_id',$lab)
      ->whereBetween('d.created_at',[$start_date,$end])
         
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('s.item_name','asc')
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
                    $nestedData['lab']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->dispose_quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
   $nestedData['date']= $term->created_at;
                    $nestedData['remark']=$term->remarks;  

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
        "data" =>$data,
        "total"=>$total,
        "quantity"=>$totalData,
    );

      echo json_encode($json_data);    

}
public function loadByRange(Request $request){
 parse_str($request->expiry_form,$out);
 $expired= $out;
 $lab=$expired['lab'];
 $period=$expired['period'];
 if($lab==-1 && $period==-1){
   $this->load($request);
 }
 if($lab!=-1 && $period==-1){
  $this->byLab($request);
 }
 if($lab!=-1 && $period!=-1){
  switch ($period) {
        case 0:
             $date=date('Y-m-d');
              $this->getDisposedRangePreviousWithLab($request,$date);
        break;
        //yesterday
        case 1:
             $date=date('Y-m-d', strtotime("-1 days"));
           $this->getDisposedRangePreviousWithLab($request,$date);
            break;
            //this week
        case 2:
           $start = Carbon::now()->subWeek()->endOfWeek()->format('Y-m-d');
            $end = Carbon::now()->addWeek()->startOfWeek()->format('Y-m-d');
            //dd($start->format('Y-m-d'));
            $this->getDisposedRangeWithLab($request,$start, $end);
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
    $this->getDisposedRangeWithLab($request,$start, $end);
            break;
            //this quarter
        case 4:
            $start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');
$this->getDisposedRangeWithLab($request,$start, $end);
            break;
            //this year
    case 5:
 $start = Carbon::now()->startOfYear()->format('Y-m-d');
   $end = Carbon::now()->endOfYear()->format('Y-m-d');
   $this->getDisposedRangeWithLab($request,$start, $end);
    break;
    //previous week
 case 6:
 $start = Carbon::now()->subWeek()->startOfWeek();
$end = Carbon::now()->subWeek()->endOfWeek();
$this->getDisposedRangeWithLab($request,$start, $end);
        break;
   //previous month     
case 7:
 $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
$this->getDisposedRangeWithLab($request,$start, $end);
break;

//previous quarter
case 8:
$start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');
$this->getDisposedRangeWithLab($request,$start, $end);
break;

//previous year
case 9:
 $start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
 $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');
 $this->getDisposedRangeWithLab($request,$start, $end);
break;
case 10:
$start=$expired['start'];
$end=$expired['end'];
$this->getDisposedRangeWithLab($request,$start, $end);
break;
    } 
 }
 if($lab==-1 && $period!=-1){
    switch ($period) {
        case 0:
             $date=date('Y-m-d');
              $this->getDisposedRangePrevious($request,$date);
        break;
        //yesterday
        case 1:
             $date=date('Y-m-d', strtotime("-1 days"));
           $this->getDisposedRangePrevious($request,$date);
            break;
            //this week
        case 2:
           $start = Carbon::now()->subWeek()->endOfWeek()->format('Y-m-d');
            $end = Carbon::now()->addWeek()->startOfWeek()->format('Y-m-d');
            //dd($start->format('Y-m-d'));
            $this->getDisposedRangeData($request,$start, $end);
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
    $this->getDisposedRangeData($request,$start, $end);
            break;
            //this quarter
        case 4:
            $start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');
$this->getDisposedRangeData($request,$start, $end);
            break;
            //this year
    case 5:
 $start = Carbon::now()->startOfYear()->format('Y-m-d');
   $end = Carbon::now()->endOfYear()->format('Y-m-d');
   $this->getDisposedRangeData($request,$start, $end);
    break;
    //previous week
 case 6:
 $start = Carbon::now()->subWeek()->startOfWeek();
$end = Carbon::now()->subWeek()->endOfWeek();
$this->getDisposedRangeData($request,$start, $end);
        break;
   //previous month     
case 7:
 $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
$this->getDisposedRangeData($request,$start, $end);
break;

//previous quarter
case 8:
$start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');
$this->getDisposedRangeData($request,$start, $end);
break;

//previous year
case 9:
 $start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
 $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');
 $this->getDisposedRangeData($request,$start, $end);
break;
case 10:
$start=$expired['start'];
$end=$expired['end'];
$this->getDisposedRangeData($request,$start, $end);
break;
    }
  }
        
}
protected function getDisposedRangeData($request,$start_date, $end){

$columns = array(
            0 =>'id',
            1=>'item',
            2=>'brand',
            3=>'batch_number',
            4=>'name',
            5=>'quantity',
            6=>'remark',
            7=>'lab',
            8=>'date'
           
        ); 
  
   $totalData = DB::table('inventories as t')
                ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
             ->whereBetween('d.created_at', [$start_date, $end])          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
      ->whereBetween('d.created_at', [$start_date, $end])
  
       
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('s.item_name','asc')
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
                    $nestedData['lab']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->dispose_quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
   $nestedData['date']= $term->created_at;
                    $nestedData['remark']=$term->remarks;  

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

public function getDisposedRangePrevious($request,$date){

$columns = array(
            0 =>'id',
            1=>'item',
            2=>'brand',
            3=>'batch_number',
            4=>'name',
            5=>'quantity',
            6=>'remark',
            7=>'lab',
            8=>'date'
           
        ); 
  
   $totalData = DB::table('inventories as t')
                ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('laboratories as l','l.id','=','t.lab_id')
              ->where('d.created_at',$date)
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('item_disposal_details as d','d.item_id','=','t.id') 
              ->join('items AS s', 's.id', '=', 't.item_id')
               ->join('laboratories as l','l.id','=','d.lab_id')
              ->select('t.id as id','l.lab_name','s.code','s.brand','s.item_description','t.batch_number','d.dispose_quantity','t.quantity','d.remarks','d.created_at','t.cost','s.item_name','t.expiry_date')
      ->where('d.created_at',$date)
  
         
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('s.item_name','asc')
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
                    $nestedData['lab']= $term->lab_name;
                 $nestedData['expire_date'] = $term->expiry_date;
              $nestedData['quantity'] = $term->dispose_quantity;
               $nestedData['cost'] = $term->cost;
                $nestedData['est_loss'] = $cost;
   $nestedData['date']= $term->created_at;
                    $nestedData['remark']=$term->remarks;  

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
