<?php

namespace App\Http\Controllers\Section;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use App\Models\Laboratory;
use App\Models\ScheduleReport;
use App\Models\User;
use App\Models\LaboratorySection;
use App\Models\Supplier;
class SectionReportController extends Controller
{
public function  showUserReport(){
$lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('clerk.reports.show',$data);
}


public function showUserExpiredReport(){
$data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
           $data['laboratories']=Laboratory::get();
           
                $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   
$data['lab_name']='Logged Into: '.$lab->lab_name;
return view('clerk.reports.list.expired',$data);

  }
  
public function showUserExpiryReport(Request $request){
             $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
           $data['laboratories']=Laboratory::get();
           
                $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
return view('clerk.reports.list.expiry',$data);
    }

public function showUserConsumptionReport(){
     $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
           $data['laboratories']=Laboratory::get();
                $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('clerk.reports.list.consumption_report',$data);
}

public function stockLevelReport(){
     $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('clerk.reports.list.stock_level',$data);
  }
  
 public function showOutOfStockReport(){
      
           $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('clerk.reports.list.out_of_stock',$data);
  }
 public function showDisposal(){
              $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
              $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
                    $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
  
   
$data['lab_name']='Logged Into: '.$lab->lab_name;

$data['laboratories']=Laboratory::get();
           
return view('clerk.reports.list.disposal',$data);
        
    }
    
    
    public function showIssueReport(Request $request){
     $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
           $data['laboratories']=Laboratory::get();
                $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('clerk.reports.list.issue',$data);
  }

public function showRequisitionReport(){
    $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
           $data['laboratories']=Laboratory::get();
                $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('clerk.reports.list.requisition_report',$data);
}



public function showSupplierOrderReport(){
     $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
           $data['laboratories']=Laboratory::select('id','lab_name')->get();
           $data['suppliers']=Supplier::select('id','supplier_name')->get();
                $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('clerk.reports.list.supplier_order_report',$data);  
}

public  function loadConsumptionTable(Request $request){
   
         
 $start = Carbon::now()->subWeek()->startOfWeek();
$end = Carbon::now()->subWeek()->endOfWeek();
       
            $data =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->leftjoin('consumption_details as c','c.item_id','=','l.id')
              ->where('c.section_id',auth()->user()->section_id)
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
              
              ->where('c.section_id',auth()->user()->section_id)
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
                   ->where('i.section_id',auth()->user()->section_id)
                  ->select(
                    
                    't.item_name',
                    'i.batch_number',
                    't.minimum_level',
                    't.maximum_level',
                    'i.quantity',
                     DB::raw('SUM(i.quantity) as stock_on_hand'))
        
            
            ->take(5)
            ->orderBy('i.id','desc')
            ->groupBy('t.id','t.item_name')
            ->get();


    return response()->json(['data'=>$terms]);
}
public function loadRequisition(Request $request){
    $terms=DB::table('requisitions')
     ->where('section_id',auth()->user()->section_id)
    ->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
        ->take(5)
        ->get();

    return response()->json(['data'=>$terms]);
}

public function loadOrders(Request $request){
   $terms=DB::table('item_orders')
    ->where('section_id',auth()->user()->section_id)
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
               ->where('c.section_id',auth()->user()->section_id)
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
          ->where('c.section_id',auth()->user()->section_id)
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
          ->where('c.section_id',auth()->user()->section_id)
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
            ->where('c.section_id',auth()->user()->section_id)
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
           ->where('c.section_id',auth()->user()->section_id)
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
               ->where('c.section_id',auth()->user()->section_id)
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
              ->where('c.section_id',auth()->user()->section_id)
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
             ->where('c.section_id',auth()->user()->section_id)
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
                   ->where('c.section_id',auth()->user()->section_id)
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
                     ->where('c.section_id',auth()->user()->section_id)
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
        ->where('section_id',auth()->user()->section_id)
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
        ->where('section_id',auth()->user()->section_id)
          ->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
        ->where('created_at','=',$date)
        ->get();

    return response()->json(['data'=>$terms]);
    break;

//this week
    case 2:
  $start = Carbon::now()->subWeek()->endOfWeek();
        $end = Carbon::now()->addWeek()->startOfWeek();

    
          $terms=DB::table('requisitions')
          ->where('section_id',auth()->user()->section_id)
          ->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
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
            ->where('section_id',auth()->user()->section_id)
      ->whereBetween('created_at', [$start, $end])
        ->get();

    return response()->json(['data'=>$terms]);
    break;

 //this quarter 
     case 4:

     $start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
     $end = Carbon::now()->endOfQuarter()->format('Y-m-d');
  $terms=DB::table('requisitions')
    ->where('section_id',auth()->user()->section_id)
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
  ->where('section_id',auth()->user()->section_id)
      ->whereBetween('created_at', [$start, $end])
      ->get();
      
       return response()->json(['data'=>$terms]);

     break;
//previous week;
     case 6:
         $start = Carbon::now()->subWeek()->startOfWeek();
            $end = Carbon::now()->subWeek()->endOfWeek();
      $terms=DB::table('requisitions')
       ->where('section_id',auth()->user()->section_id)
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
       ->where('section_id',auth()->user()->section_id)
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
      ->where('section_id',auth()->user()->section_id)
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
   ->where('section_id',auth()->user()->section_id)
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
       ->where('created_at','=',$date)
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;
     } 

     //yesterday
        case 1:
         $date=date('Y-m-d', strtotime("-1 days"));

        $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
         ->where('section_id',auth()->user()->section_id)
       ->where('created_at','=',$date)
        ->get();  
       
        return response()->json(['data'=>$terms]);

        break;

    //this week

        case 2:
        $start = Carbon::now()->subWeek()->endOfWeek();
        $end = Carbon::now()->addWeek()->startOfWeek();

      $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
      ->where('section_id',auth()->user()->section_id)
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
        ->where('section_id',auth()->user()->section_id)
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;

//this quarter

        case 4:

        $start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
        $end = Carbon::now()->endOfQuarter()->format('Y-m-d');
        $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
        ->where('section_id',auth()->user()->section_id)
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;

//this year
        case 5:
            $start = Carbon::now()->startOfYear()->format('Y-m-d');
            $end = Carbon::now()->endOfYear()->format('Y-m-d');
            $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
      ->where('section_id',auth()->user()->section_id)
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;

        //previous week
        case 6:

        $start = Carbon::now()->subWeek()->startOfWeek();
        $end = Carbon::now()->subWeek()->endOfWeek();
         $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
      ->where('section_id',auth()->user()->section_id)
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;

//previous month
        case 7:
$start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
            $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
    ->where('section_id',auth()->user()->section_id)
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;

//previous quarter
        case 8:

 $start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');
           $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
         ->where('section_id',auth()->user()->section_id)
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;

//previous year
        case 9:
 $start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
             $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');

 $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
 ->where('section_id',auth()->user()->section_id)
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
public function showStockVarianceReport(Request $request){
   $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('clerk.reports.list.variance',$data);    
}
}