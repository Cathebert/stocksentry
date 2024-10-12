<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\Laboratory;
use App\Models\LaboratorySection;
use App\Models\LabSection;
use DataTables;

class LabManagerReportController extends Controller
{
    //

    public function loadConsumptionTable(Request $request){
       // $start = Carbon::now()->subWeek()->startOfWeek();
//$end = Carbon::now()->subWeek()->endOfWeek();
       $start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');
            $data =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
              ->where('c.lab_id','=',auth()->user()->laboratory_id)
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                't.unit_issue',
                
                DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
            ->whereBetween('c.created_at', [$start, $end])
            ->orderBy('t.item_name','asc')
            ->get();


    return response()->json(['data'=>$data]);
    }
public function labConsumptionReport(Request $request){
       // $start = Carbon::now()->subWeek()->startOfWeek();
//$end = Carbon::now()->subWeek()->endOfWeek();
       $start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');
            $data =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
              ->where('c.lab_id','=',auth()->user()->laboratory_id)
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                't.unit_issue',
                
                DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
            ->whereBetween('c.created_at', [$start, $end])
            ->orderBy('t.item_name','asc')
            ->get();


    return response()->json(['data'=>$data]);
    }


    public function loadStockLevel(Request $request){
     $terms = DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                    ->where('i.lab_id','=',auth()->user()->laboratory_id)
                  ->select(
                    
                    't.item_name',
                    'i.batch_number',
                    't.minimum_level',
                    't.maximum_level',
                    'i.quantity',
                     DB::raw('SUM(i.quantity) as stock_on_hand'))
        
            
           
            ->orderBy('i.id','desc')
            ->groupBy('t.id','t.item_name')
            ->paginate(2);


    return response()->json(['data'=>$terms]);
}
public function loadRequisition(Request $request){
    $terms=DB::table('requisitions')
       ->where('lab_id','=',auth()->user()->laboratory_id)
    ->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
        ->take(5)
        ->get();

    return response()->json(['data'=>$terms]);
}

public function loadOrders(Request $request){
   $terms=DB::table('item_orders')
      ->where('lab_id','=',auth()->user()->laboratory_id)
   ->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
        ->take(5)
        ->get();  
       
        return response()->json(['data'=>$terms]);

}
public function labUsagePercentage(Request $request){
    $sum=DB::table('consumption_details')->
    select(DB::raw('SUM(consumed_quantity) as sum_total'))->get();
   // dd($sum) ;  
    $term=DB::table('consumption_details as inv')
                ->join('laboratories as l','l.id','=','inv.lab_id')
                ->select('l.lab_name','inv.consumed_quantity',DB::raw('sum(inv.consumed_quantity) as percentage') , DB::raw('round(avg(inv.consumed_quantity),2) as avg') )
                ->groupBy('l.lab_name')->get();

                //dd($term);
         return response()->json(['data'=>$term,'total'=>$sum]);           
}

public function reportPeriod(Request $request){

   switch ($request->type) {
    //consumption selected
       case 0:
          switch ($request->period) {
            //today
              case 0:
                  $date = date('Y-m-d');
//$end = Carbon::now()->subWeek()->endOfWeek();
       
            $data =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
                ->where('c.lab_id','=',auth()->user()->laboratory_id)
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                't.unit_issue',
                
                DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
              ->where('c.created_at','=', $date)
            ->orderBy('t.item_name','asc')
            ->get();


    return response()->json(['data'=>$data]);
                  break;
              //yesterday
              case 1:
                $date=date('Y-m-d', strtotime("-1 days"));
$data =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
            ->where('c.lab_id','=',auth()->user()->laboratory_id)
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                't.unit_issue',
                
                DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
              ->where('c.created_at','=', $date)
            ->orderBy('t.item_name','asc')
            ->get();
         return response()->json(['data'=>$data]);
              break;
                //this week
              case 2:
        $start = Carbon::now()->subWeek()->endOfWeek();
        $end = Carbon::now()->addWeek()->startOfWeek();
$data =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
            ->where('c.lab_id','=',auth()->user()->laboratory_id)
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                't.unit_issue',
                
                DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
           
              ->whereBetween('c.created_at', [$start, $end])
            ->orderBy('t.item_name','asc')
            ->get();
              return response()->json(['data'=>$data]);
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
$data =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
            ->where('c.lab_id','=',auth()->user()->laboratory_id)
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                't.unit_issue',
                
                DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
           
              ->whereBetween('c.created_at', [$start, $end])
            ->orderBy('t.item_name','asc')
            ->get();
              return response()->json(['data'=>$data]);
            break ;
            //this quarter
            case 4:

$start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');


$data =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
            ->where('c.lab_id','=',auth()->user()->laboratory_id)
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                't.unit_issue',
                
                DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
           
              ->whereBetween('c.created_at', [$start, $end])
            ->orderBy('t.item_name','asc')
            ->get();
              return response()->json(['data'=>$data]);
            break;

            //this year
            case 5:
$start = Carbon::now()->startOfYear()->format('Y-m-d');
$end = Carbon::now()->endOfYear()->format('Y-m-d');
$data =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')

              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
                ->where('c.lab_id','=',auth()->user()->laboratory_id)
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                't.unit_issue',
                
                DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
            ->whereBetween('c.created_at', [$start, $end])
            ->orderBy('t.item_name','asc')
            ->get();
             return response()->json(['data'=>$data]);
            break;
// previous week
            case 6:


            $start = Carbon::now()->subWeek()->startOfWeek();
            $end = Carbon::now()->subWeek()->endOfWeek();
       
            $data =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
            ->where('c.lab_id','=',auth()->user()->laboratory_id)
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                't.unit_issue',
                
                DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
            ->whereBetween('c.created_at', [$start, $end])
            ->orderBy('t.item_name','asc')
            ->get();
             return response()->json(['data'=>$data]);
             break;
//previous month
             case 7:

                        $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
                        $end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
                         $data =DB::table('items as t') 
                                      ->join('inventories AS l', 'l.item_id', '=', 't.id')
                      ->leftjoin('consumption_details as c','c.item_id','=','l.id')
                         ->where('c.lab_id','=',auth()->user()->laboratory_id)
                      ->select(
                         't.id as item_id',
                        'l.id as id',
                        't.item_name',
                        'l.batch_number',
                        't.catalog_number',
                        't.unit_issue',
                        
                        DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
                    ->whereBetween('c.created_at', [$start, $end])
                    ->orderBy('t.item_name','asc')
                    ->get();
             return response()->json(['data'=>$data]);

             break;
//previous quarter
            case 8:
            $start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
            $end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');

                         $data =DB::table('items as t') 
                                      ->join('inventories AS l', 'l.item_id', '=', 't.id')
                      ->leftjoin('consumption_details as c','c.item_id','=','l.id')
                    ->where('c.lab_id','=',auth()->user()->laboratory_id)
                      ->select(
                         't.id as item_id',
                        'l.id as id',
                        't.item_name',
                        'l.batch_number',
                        't.catalog_number',
                        't.unit_issue',
                        
                        DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
                    ->whereBetween('c.created_at', [$start, $end])
                    ->orderBy('t.item_name','asc')
                    ->get();
             return response()->json(['data'=>$data]);
             break;

//previous year

             case 9:

             $start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
             $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');
                 $data =DB::table('items as t') 
                                      ->join('inventories AS l', 'l.item_id', '=', 't.id')
                      ->leftjoin('consumption_details as c','c.item_id','=','l.id')
                         ->where('c.lab_id','=',auth()->user()->laboratory_id)
                      ->select(
                         't.id as item_id',
                        'l.id as id',
                        't.item_name',
                        'l.batch_number',
                        't.catalog_number',
                        't.unit_issue',
                        
                        DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
                    ->whereBetween('c.created_at', [$start, $end])
                    ->orderBy('t.item_name','asc')
                    ->get();
             return response()->json(['data'=>$data]);
             break;
              default:
                  // code...
                  break;
          }
 break;
       
/**
 * 
 * Requisitions case
 * 
 * */

case 2:
    switch($request->period){
        //today
        case 0:
          $date = date('Y-m-d');
          $terms=DB::table('requisitions')
             ->where('lab_id','=',auth()->user()->laboratory_id)
          ->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
        ->where('created_at','=',$date)
        ->get();

    return response()->json(['data'=>$terms]);
        break;


        //yesterday

        case 1:
     $date=date('Y-m-d', strtotime("-1 days"));

     $date = date('Y-m-d');
          $terms=DB::table('requisitions')
         ->where('lab_id','=',auth()->user()->laboratory_id)
          ->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
        ->where('created_at','=',$date)
        ->get();

    return response()->json(['data'=>$terms]);
    break;

//this week
    case 2:
  $start = Carbon::now()->subWeek()->endOfWeek();
        $end = Carbon::now()->addWeek()->startOfWeek();

    
          $terms=DB::table('requisitions')->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
      ->whereBetween('created_at', [$start, $end])
        ->get();

       return response()->json(['data'=>$terms]);
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
          $terms=DB::table('requisitions')->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
             ->where('lab_id','=',auth()->user()->laboratory_id)
      ->whereBetween('created_at', [$start, $end])
        ->get();

    return response()->json(['data'=>$terms]);
    break;

 //this quarter 
     case 4:

     $start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
     $end = Carbon::now()->endOfQuarter()->format('Y-m-d');
  $terms=DB::table('requisitions')
    ->where('lab_id','=',auth()->user()->laboratory_id)
  ->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
      ->whereBetween('created_at', [$start, $end])
      ->get();

       return response()->json(['data'=>$terms]);
     break;

     //this year
     case 5:
$start = Carbon::now()->startOfYear()->format('Y-m-d');
$end = Carbon::now()->endOfYear()->format('Y-m-d');
 $terms=DB::table('requisitions')->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
   ->where('lab_id','=',auth()->user()->laboratory_id)
      ->whereBetween('created_at', [$start, $end])
      ->get();
      
       return response()->json(['data'=>$terms]);

     break;
//previous week;
     case 6:
         $start = Carbon::now()->subWeek()->startOfWeek();
            $end = Carbon::now()->subWeek()->endOfWeek();
      $terms=DB::table('requisitions')
        ->where('lab_id','=',auth()->user()->laboratory_id)
      ->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
      ->whereBetween('created_at', [$start, $end])
      ->get();
      
       return response()->json(['data'=>$terms]);
     break;

     //previous month
        case 7:
        $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
          $end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
       $terms=DB::table('requisitions')
         ->where('lab_id','=',auth()->user()->laboratory_id)
       ->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
      ->whereBetween('created_at', [$start, $end])
      ->get();
      
            return response()->json(['data'=>$terms]);
        break;

     
     //previous quarter;
      case 8:
   $start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
    $end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');
       $terms=DB::table('requisitions')
         ->where('lab_id','=',auth()->user()->laboratory_id)
       ->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
      ->whereBetween('created_at', [$start, $end])
      ->get();
      
            return response()->json(['data'=>$terms]);
        break;

//previous year;
    case 9:
$start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
$end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');
 $terms=DB::table('requisitions')->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
   ->where('lab_id','=',auth()->user()->laboratory_id)
      ->whereBetween('created_at', [$start, $end])
      ->get();
      
            return response()->json(['data'=>$terms]);
    break;

    }

break;

/**
 * Orders
 * 
 * */
case 3:
     switch($request->period)  {
        //today
        case 0:
                $date = date('Y-m-d');
                   $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
        ->where('lab_id','=',auth()->user()->laboratory_id)
       ->where('created_at','=',$date)
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;
     } 

     //yesterday
        case 1:
         $date=date('Y-m-d', strtotime("-1 days"));

        $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
          ->where('lab_id','=',auth()->user()->laboratory_id)
       ->where('created_at','=',$date)
        ->get();  
       
        return response()->json(['data'=>$terms]);

        break;

    //this week

        case 2:
        $start = Carbon::now()->subWeek()->endOfWeek();
        $end = Carbon::now()->addWeek()->startOfWeek();

      $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
        ->where('lab_id','=',auth()->user()->laboratory_id)
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);

        break;


//this month;
        case 3:
  $myDate = date('Y-m-d');
        $start = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->firstOfMonth()
                        ->format('Y-m-d');

$end = Carbon::createFromFormat('Y-m-d', $myDate)
                        ->lastOfMonth()
                        ->format('Y-m-d');


       $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
         ->where('lab_id','=',auth()->user()->laboratory_id)
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;

//this quarter

        case 4:

        $start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
        $end = Carbon::now()->endOfQuarter()->format('Y-m-d');
        $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
          ->where('lab_id','=',auth()->user()->laboratory_id)
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;

//this year
        case 5:
            $start = Carbon::now()->startOfYear()->format('Y-m-d');
            $end = Carbon::now()->endOfYear()->format('Y-m-d');
            $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
        ->where('lab_id','=',auth()->user()->laboratory_id)
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;

        //previous week
        case 6:

        $start = Carbon::now()->subWeek()->startOfWeek();
        $end = Carbon::now()->subWeek()->endOfWeek();
         $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
        ->where('lab_id','=',auth()->user()->laboratory_id)
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;

//previous month
        case 7:
$start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
            $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
        ->where('lab_id','=',auth()->user()->laboratory_id)
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;

//previous quarter
        case 8:

 $start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');
           $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
          ->where('lab_id','=',auth()->user()->laboratory_id)
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;

//previous year
        case 9:
 $start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
             $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');

 $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
   ->where('lab_id','=',auth()->user()->laboratory_id)
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
     break;
   }
}

public function compareReport(Request $request){
    switch ($request->type) {
        case 0:
            switch ($request->from) {
                case 0:
        $start = Carbon::now()->subWeek()->endOfWeek();
        $end = Carbon::now()->addWeek()->startOfWeek();
$from =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                't.unit_issue',
                
                DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
           
            ->whereBetween('c.created_at', [$start, $end])
            ->orderBy('t.item_name','asc')
            ->get();



            
                    break;
                
                default:
                    // code...
                    break;
            }

              return response()->json(['from'=>$from,'to'=>$to]);
            break;
        
        default:
            // code...
            break;
    }
}
public function itemDetails(Request $request){
 $low_stock_level = DB::table('inventories')
    ->join('items', 'inventories.item_id', '=', 'items.id')
    ->whereColumn('inventories.quantity', '<', 'items.minimum_level')
    ->count();
                  
$medium_stock_level=DB::table('inventories')
    ->join('items', 'inventories.item_id', '=', 'items.id')
    ->where([['inventories.quantity', '>', 'items.minimum_level'],['inventories.quantity', '<', 'items.maximum_level']])
    ->count();
                  
$high_stock_level=DB::table('inventories')
    ->join('items', 'inventories.item_id', '=', 'items.id')
    ->where('inventories.quantity', '>', 'items.maximum_level')
    ->count();
return response()->json([
    'low'=>$low_stock_level,
    'medium'=>$medium_stock_level,
    'high'=>$high_stock_level
]);
 
}

public function getTopConsumed(Request $request){
    $topConsumedItems = DB::table('consumption_details as c') 
              ->join('inventories AS l', 'c.item_id', '=', 'l.id')
              ->join('items as t','l.item_id','=','t.id')
              ->select(

                't.item_name',
                't.item_image',
               
                't.code',
             
                DB::raw('SUM(c.consumed_quantity) as total_consumed'))
                 ->groupBy('t.item_name')
    ->orderByDesc('total_consumed')
    ->limit(10)
    ->get();

   // $c=url('/'). "/public/upload/items/".$term->item_image ;
 
    return Datatables::of( $topConsumedItems )
    ->addIndexColumn()
->addColumn('preview', function($term) {
 if(empty($term->item_image)){
     $default=url('/')."/assets/icon/not_available.jpg";

                       return "<img src='$default' class='img-thumbnail' alt='...' width='50px' height='50px'>";  
                  }
                  else{
                    $c=url('/'). "/public/upload/items/".$term->item_image ;
                 return "<img src='$c' class='img-thumbnail' alt='...' width='50px' height='50px'>";
                  }
    })
                 ->rawColumns(['preview'])
                ->make(true)
    ;
}
public function getLatestOrders(Request $request){
   $columns = array(
            0 =>'sr',
            1=>'request_lab',
            2=>'request_date',
            3=>'options',
            4=>'marked',
          
        ); 

         $totalData = DB::table('requisitions') 
          ->where('status','=','approved')
           // ->where('t.expiry_date', '>', date('Y-m-d') )
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('requisitions') 
          ->where('status','=','approved')
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('sr_number', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit(5)
            ->orderBy('id','desc')
            ->get();

          $totalFiltered =  $totalRec ;




        $data = array();
          if (!empty($terms)) {
$x=1;
  $section_name="";

            foreach ($terms as $term) {
$lab=Laboratory::where('id',$term->lab_id)->select('lab_name','has_section')->first();

if($lab->has_section=="yes"){
$section=LaboratorySection::where('id',$term->section_id)->select('section_name')->first();

$section_name=$section->section_name??'';

}
else{
  $section_name='';
}
                $nestedData['sr']="<a class='btn btn-info btn-sm' id='$term->id' onclick='ViewApprovedRequest(this.id)'><i class='fa fa-eye'></i>".$term->sr_number."</a> ";
                $nestedData['request_lab']=$lab->lab_name;
                $nestedData['request_date']= date('d, M Y',strtotime($term->requested_date));
             
                $nestedData['options']= " <a class='btn btn-info btn-sm' id='$term->id' onclick='ViewApprovedRequest(this.id)'><i class='fa fa-eye'></i> View</a>  | <a class='btn btn-primary btn-sm' id='$term->id' onclick='AcceptApprovedRequest(this.id)'><i class='fa fa-check'></i> Accept</a> | <a class='btn btn-danger  btn-sm' id='$term->id' onclick='Remove(this.id)'><i class='fa fa-check'></i> Remove</a>   ";
      switch($term->is_marked){
            case 'no':
                $nestedData['marked']="<a class='btn btn-success btn-sm'   id='$term->id' onclick='MarkForConsolidation(this.id)' ><i class='fa fa-plus'></i> add</a> ";
              break;
             
           case 'yes':
             
 $nestedData['marked']="<a class='btn btn-secondary btn-sm'   id='$term->id' onclick='MarkForConsolidation(this.id)' ><i class='fa fa-minus'></i> Remove </a> ";
            break;
          case 'done':
  $nestedData['marked']="<span class='badge badge-info'>Consolidated</span> ";
             }
               
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