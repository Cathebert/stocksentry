<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Laboratory;
use App\Models\LaboratorySection;
use App\Models\Item;
use DB;
use Carbon\Carbon;
class StatsController extends Controller
{
    public function showTopUsedItems(Request $request){
        $startDate = Carbon::now()->subMonths(2); // default 2 previous months
    $endDate = Carbon::now();

    $result = DB::table('consumption_details')
        ->select('section_id', DB::raw('MONTH(created_at) as month'),DB::raw('MONTHNAME(created_at) as monthname'), 'item_id', DB::raw('SUM(consumed_quantity) as usage_count'))
        
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('month')
        ->orderByDesc('usage_count')
        ->get();

    $mostUsedItems = collect();
     $topUsed = collect();
  $data = array();
    $result->each(function ($row) use ($mostUsedItems) {
        $userId = $row->section_id;
        $month = $row->month;
        $monthName=$row->monthname;
        $itemId = $row->item_id;
        $usageCount = $row->usage_count;

        // Check if the user already has an entry for the month
        if (!$mostUsedItems->has($userId)) {
            $mostUsedItems[$userId] = collect();
        }

        if (!$mostUsedItems[$userId]->has($month) || $mostUsedItems[$userId][$month]['usage_count'] < $usageCount) {
             $inventory=Inventory::where('id',$itemId)->select('item_id')->first();
            $section_name=LaboratorySection::find($userId)->section_name;
             $item_name=Item::find($inventory->item_id)->item_name;
            $mostUsedItems->push([
                'section_id' => $userId,
                'section_name'=>$section_name,
                'month' => $monthName,
                'item_id' => $itemId,
                'item_name' =>  $item_name, 
                'usage_count' => $usageCount,
            ]);
             //dd($month);
        }
      
    });
    //dd($mostUsedItems);
           return response()->json(['mostUsedItems' => $mostUsedItems]);

    }
    //
public function showInventoryHealth(Request $request){
$health = Inventory::pluck('expiry_date','id');
$expired=0;
$about_to_in_30=0;
$expiring_in_60=0;
$expirin_in_90=0;
$good=0;
$dat= array();
foreach ($health as $key => $value) {
   
          $to = \Carbon\Carbon::createFromFormat('Y-m-d', $value);
         $from = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
        $diff_in_days = $from->diffInDays($to); 
       

         switch($diff_in_days){
            case  $diff_in_days < 1:
                  $expired++;
            break;

            case $diff_in_days>= 1 && $diff_in_days < 30:
                 $about_to_in_30++;
                break;

            case  $diff_in_days>=30 && $diff_in_days <60:
                    $expiring_in_60++;

                    break;
            case $diff_in_days>=60 && $diff_in_days <90:
                  $expirin_in_90++;
                  break;

            case $diff_in_days>90:

 $good++;
 break;
            }
  
        }
/*     if($diff_in_days < 1){
    $expired++;
}           
    if( $diff_in_days>=1 && $diff_in_days <30){
                    $about_to_in_30++;
                 
                }
if( $diff_in_days>=30 && $diff_in_days <60){
    $expiring_in_60++;
 
                   // $nestedData['status']="<span class='text-warning'>expiring (".$diff_in_days. " days)</span>";
                }
     if( $diff_in_days>=60 && $diff_in_days <90){
        $expirin_in_90++;
      
                  //  $nestedData['status']="<span class='text-success'>expiring (".$diff_in_days." days)/span>";
                }
                if($diff_in_days>90){
                  $good++;
                  
                } */
               // $cars = array("Expired", "Expire In 30 days", "T");
                $data[]=$expired;
                 $data[]=$about_to_in_30;
                 $data[]=$expiring_in_60;
                 $data[]=$expirin_in_90;
                 $data[]=$good;

                 return response()->json([
                    'data'=>$data,
                 ]);
    
}
public function showHealthModal(Request $request){
    $data['id']=$request->id;
    $data['label']=$request->label;
    return view('inventory.modal.stats',$data);
}
public function showHealthTable (Request $request){
 $columns = array(
            0 =>'id',
            1=>'lab',
            2=>'count'
          
        ); 
        $selected=$request->id;
       
        switch ($selected) {
            case 1:

                $start=date('Y-m-d');
                $end= \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDays(30);
                                            $terms = DB::table('laboratories as l') 
                                        ->join('inventories AS s', 's.lab_id', '=', 'l.id')
                                        ->select('l.id as id','l.lab_name as lab','s.expiry_date',DB::Raw('count(s.lab_id) as `count`'))
                                        ->whereBetween('s.expiry_date', [$start,$end])
                                                ->groupBy('lab')->orderBy('lab')
                                    
                                    
                                    //->count();
                                    ->get();
                                $data = array();
                                $x=1;
                                        foreach ($terms as $term) {



                                            $nestedData['id']=$x;
                                            $nestedData['lab']=$term->lab;
                                                $nestedData['count']= $term->count;
                                        
                                            
                                        
                                
                                            $x++;
                                            $data[] = $nestedData;
                                    }
                                

                                $json_data = array(
                                
                                    "data" => $data,
                                );

                                echo json_encode($json_data);
                break;
            




                case 2:
                    $terms = DB::table('laboratories as l') 
                                ->join('inventories AS s', 's.lab_id', '=', 'l.id')
                                ->select('l.id as id','l.lab_name as lab',DB::Raw('count(s.lab_id) as `count`'))
                                        ->groupBy('lab')->orderBy('lab')
                            
                            // ->where('t.expiry_date', '>', date('Y-m-d') )
                            //->count();
                            ->get();
                        $data = array();
                        $x=1;
                                foreach ($terms as $term) {



                                    $nestedData['id']=$x;
                                    $nestedData['lab']=$term->lab;
                                        $nestedData['count']= $term->count;
                                
                                    
                                
                        
                                    $x++;
                                    $data[] = $nestedData;
                            }
                        

                        $json_data = array(
                        
                            "data" => $data,
                        );

                        echo json_encode($json_data);
                break;


                case 3:

                                $terms = DB::table('laboratories as l') 
                                            ->join('inventories AS s', 's.lab_id', '=', 'l.id')
                                            ->select('l.id as id','l.lab_name as lab',DB::Raw('count(s.lab_id) as `count`'))
                                                    ->groupBy('lab')->orderBy('lab')
                                        
                                        // ->where('t.expiry_date', '>', date('Y-m-d') )
                                        //->count();
                                        ->get();
                                    $data = array();
                                    $x=1;
                                            foreach ($terms as $term) {



                                                $nestedData['id']=$x;
                                                $nestedData['lab']=$term->lab;
                                                    $nestedData['count']= $term->count;
                                            
                                                
                                            
                                    
                                                $x++;
                                                $data[] = $nestedData;
                                        }
                                    

                                    $json_data = array(
                                    
                                        "data" => $data,
                                    );

                                    echo json_encode($json_data);

            break;


            case 4:
                            $terms = DB::table('laboratories as l') 
                                        ->join('inventories AS s', 's.lab_id', '=', 'l.id')
                                        ->select('l.id as id','l.lab_name as lab',DB::Raw('count(s.lab_id) as `count`'))
                                                ->groupBy('lab')->orderBy('lab')
                                    
                                    // ->where('t.expiry_date', '>', date('Y-m-d') )
                                    //->count();
                                    ->get();
                                $data = array();
                                $x=1;
                                        foreach ($terms as $term) {



                                            $nestedData['id']=$x;
                                            $nestedData['lab']=$term->lab;
                                                $nestedData['count']= $term->count;
                                        
                                            
                                        
                                
                                            $x++;
                                            $data[] = $nestedData;
                                    }
                                

                                $json_data = array(
                                
                                    "data" => $data,
                                );

                                echo json_encode($json_data);


                break;

         case 5:
                    $terms = DB::table('laboratories as l') 
                                ->join('inventories AS s', 's.lab_id', '=', 'l.id')
                                ->select('l.id as id','l.lab_name as lab',DB::Raw('count(s.lab_id) as `count`'))
                                        ->groupBy('lab')->orderBy('lab')
                            
                            // ->where('t.expiry_date', '>', date('Y-m-d') )
                            //->count();
                            ->get();
                        $data = array();
                        $x=1;
                                foreach ($terms as $term) {



                                    $nestedData['id']=$x;
                                    $nestedData['lab']=$term->lab;
                                        $nestedData['count']= $term->count;
                                
                                    
                                
                        
                                    $x++;
                                    $data[] = $nestedData;
                            }
                        

                        $json_data = array(
                        
                            "data" => $data,
                        );

                        echo json_encode($json_data);

            break;
           
                    }
        

}
public function getUsage(Request $request){
    
}

public  function loadConsumptionTable(Request $request){
   
         
 $start = Carbon::now()->subWeek()->startOfWeek();
$end = Carbon::now()->subWeek()->endOfWeek();
       
            $data =DB::table('items as t') 
              ->join('inventories AS l', 't.id', '=', 'l.item_id')
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                't.unit_issue',
                
                DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
           //->whereBetween('c.created_at', [$start, $end])
            ->groupBy('t.item_name','t.id')
            ->get();


    return response()->json(['data'=>$data]);
}

public function loadStockLevel(Request $request){
     $terms = DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
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
    $terms=DB::table('requisitions')->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
        ->take(5)
        ->get();

    return response()->json(['data'=>$terms]);
}

public function loadOrders(Request $request){
   $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
        ->take(5)
        ->get();  
       
        return response()->json(['data'=>$terms]);

}
public function labUsagePercentage(Request $request){
    $sum=DB::table('consumption_details')->select(DB::raw('SUM(consumed_quantity) as sum_total'))->get();
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
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                't.unit_issue',
                
                DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
              ->where('c.created_at','=', $date)
              ->groupBy('t.item_name','t.id')
            ->get();


    return response()->json(['data'=>$data]);
                  break;
              //yesterday
              case 1:
                $date=date('Y-m-d', strtotime("-1 days"));
$data =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                't.unit_issue',
                
                DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
              ->where('c.created_at','=', $date)
            ->groupBy('t.item_name','t.id')
            ->get();
         return response()->json(['data'=>$data]);
              break;
                //this week
              case 2:
        $start = Carbon::now()->subWeek()->endOfWeek();
        $end = Carbon::now()->addWeek()->startOfWeek();
$data =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                't.unit_issue',
                
                DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
           
              ->whereBetween('c.created_at', [$start, $end])
              ->groupBy('t.item_name','t.id')
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
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                't.unit_issue',
                
                DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
           
              ->whereBetween('c.created_at', [$start, $end])
             ->groupBy('t.item_name','t.id')
            ->get();
              return response()->json(['data'=>$data]);
            break ;
            //this quarter
            case 4:

$start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->endOfQuarter()->format('Y-m-d');
$data =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                't.unit_issue',
                
                DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
           
              ->whereBetween('c.created_at', [$start, $end])
              ->groupBy('t.item_name','t.id')
            ->get();
              return response()->json(['data'=>$data]);
            break;

            //this year
            case 5:
$start = Carbon::now()->startOfYear()->format('Y-m-d');
$end = Carbon::now()->endOfYear()->format('Y-m-d');
$data =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                't.unit_issue',
                
                DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
            ->whereBetween('c.created_at', [$start, $end])
             ->groupBy('t.item_name','t.id')
            ->get();
             return response()->json(['data'=>$data]);
            break;
// previous week
            case 6:


            $start = Carbon::now()->subWeek()->startOfWeek();
            $end = Carbon::now()->subWeek()->endOfWeek();
       
            $data =DB::table('items as t') 
              ->join('inventories AS l', 'l.item_id', '=', 't.id')
              ->join('consumption_details as c','c.item_id','=','l.id')
              ->select(
                 't.id as item_id',
                'l.id as id',
                't.item_name',
                'l.batch_number',
                't.catalog_number',
                't.unit_issue',
                
                DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
            ->whereBetween('c.created_at', [$start, $end])
              ->groupBy('t.item_name','t.id')
            ->get();
             return response()->json(['data'=>$data]);
             break;
//previous month
             case 7:

                        $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
                        $end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
                         $data =DB::table('items as t') 
                                      ->join('inventories AS l', 'l.item_id', '=', 't.id')
                      ->join('consumption_details as c','c.item_id','=','l.id')
                      ->select(
                         't.id as item_id',
                        'l.id as id',
                        't.item_name',
                        'l.batch_number',
                        't.catalog_number',
                        't.unit_issue',
                        
                        DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
                    ->whereBetween('c.created_at', [$start, $end])
                    ->groupBy('t.item_name','t.id')
                    ->get();
             return response()->json(['data'=>$data]);

             break;
//previous quarter
            case 8:
            $start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
            $end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');

                         $data =DB::table('items as t') 
                                      ->join('inventories AS l', 'l.item_id', '=', 't.id')
                      ->join('consumption_details as c','c.item_id','=','l.id')
                      ->select(
                         't.id as item_id',
                        'l.id as id',
                        't.item_name',
                        'l.batch_number',
                        't.catalog_number',
                        't.unit_issue',
                        
                        DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
                    ->whereBetween('c.created_at', [$start, $end])
                    ->groupBy('t.item_name','t.id')
                    ->get();
             return response()->json(['data'=>$data]);
             break;

//previous year

             case 9:

             $start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
             $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');
                 $data =DB::table('items as t') 
                                      ->join('inventories AS l', 'l.item_id', '=', 't.id')
                      ->join('consumption_details as c','c.item_id','=','l.id')
                      ->select(
                         't.id as item_id',
                        'l.id as id',
                        't.item_name',
                        'l.batch_number',
                        't.catalog_number',
                        't.unit_issue',
                        
                        DB::raw('SUM(c.consumed_quantity) as `consumed_quantity`'))
                    ->whereBetween('c.created_at', [$start, $end])
                    ->groupBy('t.item_name','t.id')
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
          $terms=DB::table('requisitions')->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
        ->where('created_at','=',$date)
        ->get();

    return response()->json(['data'=>$terms]);
        break;


        //yesterday

        case 1:
     $date=date('Y-m-d', strtotime("-1 days"));

     $date = date('Y-m-d');
          $terms=DB::table('requisitions')->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
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
      ->whereBetween('created_at', [$start, $end])
        ->get();

    return response()->json(['data'=>$terms]);
    break;

 //this quarter 
     case 4:

     $start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
     $end = Carbon::now()->endOfQuarter()->format('Y-m-d');
  $terms=DB::table('requisitions')->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
      ->whereBetween('created_at', [$start, $end])
      ->get();

       return response()->json(['data'=>$terms]);
     break;

     //this year
     case 5:
$start = Carbon::now()->startOfYear()->format('Y-m-d');
$end = Carbon::now()->endOfYear()->format('Y-m-d');
 $terms=DB::table('requisitions')->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
      ->whereBetween('created_at', [$start, $end])
      ->get();
      
       return response()->json(['data'=>$terms]);

     break;
//previous week;
     case 6:
         $start = Carbon::now()->subWeek()->startOfWeek();
            $end = Carbon::now()->subWeek()->endOfWeek();
      $terms=DB::table('requisitions')->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
      ->whereBetween('created_at', [$start, $end])
      ->get();
      
       return response()->json(['data'=>$terms]);
     break;

     //previous month
        case 7:
        $start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
          $end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
       $terms=DB::table('requisitions')->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
      ->whereBetween('created_at', [$start, $end])
      ->get();
      
            return response()->json(['data'=>$terms]);
        break;

     
     //previous quarter;
      case 8:
   $start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
    $end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');
       $terms=DB::table('requisitions')->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
      ->whereBetween('created_at', [$start, $end])
      ->get();
      
            return response()->json(['data'=>$terms]);
        break;

//previous year;
    case 9:
$start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
$end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');
 $terms=DB::table('requisitions')->select('sr_number','status',DB::raw('COUNT(status) as count'))->groupBy('status')
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
       ->where('created_at','=',$date)
        ->get();  
       
        return response()->json(['data'=>$terms]);

        break;

    //this week

        case 2:
        $start = Carbon::now()->subWeek()->endOfWeek();
        $end = Carbon::now()->addWeek()->startOfWeek();

      $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
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
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;

//this quarter

        case 4:

        $start = Carbon::now()->firstOfQuarter()->format('Y-m-d');
        $end = Carbon::now()->endOfQuarter()->format('Y-m-d');
        $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;

//this year
        case 5:
            $start = Carbon::now()->startOfYear()->format('Y-m-d');
            $end = Carbon::now()->endOfYear()->format('Y-m-d');
            $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;

        //previous week
        case 6:

        $start = Carbon::now()->subWeek()->startOfWeek();
        $end = Carbon::now()->subWeek()->endOfWeek();
         $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;

//previous month
        case 7:
$start = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
$end = Carbon::now()->subMonthsNoOverflow()->endOfMonth()->toDateString();
            $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;

//previous quarter
        case 8:

 $start = Carbon::now()->subMonths(3)->firstOfQuarter()->format('Y-m-d');
$end = Carbon::now()->subMonths(3)->endOfQuarter()->format('Y-m-d');
           $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
        break;

//previous year
        case 9:
 $start = Carbon::now()->subYear()->startOfYear()->format('Y-m-d');
             $end = Carbon::now()->subYear()->endOfYear()->format('Y-m-d');

 $terms=DB::table('item_orders')->select('order_number','is_delivered as status',DB::raw('COUNT(is_delivered) as count'))->groupBy('status')
        ->whereBetween('c.created_at', [$start, $end])
        ->get();  
       
        return response()->json(['data'=>$terms]);
     break;
   }
}
}