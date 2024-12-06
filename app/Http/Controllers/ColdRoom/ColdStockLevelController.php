<?php

namespace App\Http\Controllers\ColdRoom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use DB;
class ColdStockLevelController extends Controller
{

     public function loadStockLevelReport(Request $request){
        $location="Cold Room";
    if ($request->ajax()) {
    $stock_level = DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->select(
                    't.id as id',
                    't.uln',
                    't.code',
                    't.item_name',
                    'i.batch_number',
                    't.catalog_number',
                    't.place_of_purchase',
                    't.unit_issue',
                    't.minimum_level',
                    't.maximum_level',
                    'i.quantity',
                    'i.expiry_date',
                     DB::raw('SUM(i.quantity) as stock_on_hand'))
                  ->where('i.storage_location',$location)
                  ->where('i.quantity','>',0)
                  ->groupBy('t.id','t.item_name')
                  ->get();
                 
 return Datatables::of($stock_level)
                ->addIndexColumn()
                
                ->addColumn('status', function($row) {
                    if($row->stock_on_hand>$row->minimum_level){
                    return   '<span class="badge badge-info">Good</span>' ;
            }
            if ($row->stock_on_hand >$row->maximum_level) {
                return '<span class="badge badge-success">More than enough</span>';
            }
             if ($row->stock_on_hand <$row->minimum_level) {
                return '<span class="badge badge-danger">Bad</span>';
            }
if ($row->stock_on_hand <$row->maximum_level) {
                return '<span class="badge badge-info">Good</span>';
            }
                })
                
                   ->rawColumns(['status'])
                ->make(true);

}
            
  }
  public function loadStockLevelDetails(Request $request){
   
 
    $data = array();
 
$record= DB::table('inventories as inv')
                  ->join('laboratories  as r','r.id','=','inv.lab_id')
                 
                  ->select(
                    'r.lab_name',
                    'inv.section_id',
                    'inv.quantity',
                     DB::raw('SUM(inv.quantity) as stock_on_hand'))
                  
                    ->where('inv.item_id',$request->id)
                   
                  
                   // ->where('rd.status','approved')
                    ->groupBy('r.lab_name')
                    ->get();
         //dd($record);
             
             foreach($record as $r)  { 
  //dd($record);
  
       $lab=$r->lab_name;   
     
 
   $nested['lab_name']=$lab;

$nested['quantity']=$r->stock_on_hand;
$data[]= $nested;

}


  return response()->json([
   'data'=>$data,
  ]);
  }
public function selectedLabStockLevel(Request $request){
$lab=$request->id;
$location="Cold Room";
    if ($request->ajax()) {
    $stock_level = DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->select(
                    't.id as id',
                    't.uln',
                    't.code',
                    't.item_name',
                    'i.batch_number',
                    't.catalog_number',
                    't.place_of_purchase',
                    't.unit_issue',
                    't.minimum_level',
                    't.maximum_level',
                    'i.quantity',
                    'i.expiry_date',
                     DB::raw('SUM(i.quantity) as stock_on_hand'))
                  ->where('i.storage_location',$location)
                  ->where('i.lab_id',$lab)
                  ->where('i.quantity','>',0)
                   ->groupBy('t.id','t.item_name')
                  ->get();
 return Datatables::of($stock_level)
                ->addIndexColumn()
                ->addColumn('status', function($row) {
                    if($row->stock_on_hand>$row->minimum_level){
                    return   '<span class="badge badge-info">Good</span>' ;
            }
            if ($row->stock_on_hand >$row->maximum_level) {
                return '<span class="badge badge-success">More than enough</span>';
            }
             if ($row->stock_on_hand <$row->minimum_level) {
                return '<span class="badge badge-danger">Bad</span>';
            }
if ($row->stock_on_hand <$row->maximum_level) {
                return '<span class="badge badge-info">Good</span>';
            }
                })
                 ->rawColumns(['status'])
                ->make(true);

}
         
  }
  
}
