<?php

namespace App\Http\Controllers\ColdRoom;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Requisition;
use App\Models\LaboratorySection;
use App\Models\Laboratory;
use App\Models\Inventory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use DataTables;
class ColdRoomController extends Controller
{
    //

    public function index(){
                    $data['total']=DB::table('consumption_details')->sum('consumed_quantity');
   // dd($sum) ;  
    $data['item']=Inventory::where([['lab_id', '=',auth()->user()->laboratory_id],[ 'expiry_date', '>', date('Y-m-d') ]])->sum('quantity');
    $data['consumption']=DB::table('consumption_details as inv')
                ->join('laboratories as l','l.id','=','inv.lab_id')
                ->select('l.lab_name','inv.consumed_quantity',DB::raw('sum(inv.consumed_quantity) as percentage') , DB::raw('round(avg(inv.consumed_quantity),2) as avg') )
                ->where('inv.section_id',auth()->user()->section_id)
                ->groupBy('l.lab_name')->get();
                
                 $data['requests']=Requisition::where('section_id',auth()->user()->section_id)->count();
        $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['section_id','=',auth()->user()->section_id]])->count();
           $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}

        return view('cold.show',$data);
    }
 public function coldProfile(){
         $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
     
        $data['users'] = Auth::user();
        return view('cold.user.profile',$data);
    }

    public function coldSignature(){

         $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
     
        $data['users'] = Auth::user();

        return view('cold.user.signature',$data);
    }

public function coldPassword(){
     $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
     
        $data['users'] = Auth::user();

        return view('cold.user.password.change-password',$data);

}
public function showReport(){
    $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
              $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
                   
$data['lab_name']='Logged Into: '.$lab->lab_name;

$data['laboratories']=Laboratory::get();
    return view('cold.reports.show',$data);
}
public function showExpired(){
    $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
              $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
                    $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();

$data['lab_name']='Logged Into: '.$lab->lab_name;

$data['laboratories']=Laboratory::get();
    return view('cold.reports.list.expired',$data);
}
public function showAboutToExpire(){

         $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
              $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
                    $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();

$data['lab_name']='Logged Into: '.$lab->lab_name;

$data['laboratories']=Laboratory::get();
    return view('cold.reports.list.expiry',$data);
}

public function showConsumptionReport(){

         $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
              $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
                    $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();

$data['lab_name']='Logged Into: '.$lab->lab_name;
$data['laboratories']=Laboratory::get();
    return view('cold.reports.list.consumption_report',$data);
}


public function showStockLevelReport(){
         $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
              $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
                    $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();

$data['lab_name']='Logged Into: '.$lab->lab_name;
$data['laboratories']=Laboratory::get();
    return view('cold.reports.list.stock_level',$data);
}


public function showOutOfStock(){
         $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
              $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
                    $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();

$data['lab_name']='Logged Into: '.$lab->lab_name;
$data['laboratories']=Laboratory::get();
    return view('cold.reports.list.out_of_stock',$data);
}
public function showDisposed(){
         $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
    $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
    $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();

$data['lab_name']='Logged Into: '.$lab->lab_name;
$data['laboratories']=Laboratory::get();
    return view('cold.reports.list.disposal',$data);
}

public function showIssue(){
         $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])
             ->select('id','email')->get();
              $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
                    $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();

$data['lab_name']='Logged Into: '.$lab->lab_name;
$data['laboratories']=Laboratory::get();
    return view('cold.reports.list.issue',$data);
}
public function showLabStockLevel(Request $request){
$lab=$request->id;
/*$columns = array(
            0 =>'id',
            1=>'item_name',
            2=>'catalog_number',
            4=>'place_purchase',
            5=>'unit_issue',
            6=>'min',
            7=>'max',
            8=>'available',
            9=>'status',
          
        );
    $totalData =DB::table('items as t')
                  ->join('inventories as i','i.item_id','=','t.id')
                  ->where('i.lab_id',$lab) 
                  
                   ->count();

           // ->where('t.expiry_date', '>', date('Y-m-d') )
         

            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('items as t')
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
                     DB::raw('SUM(i.quantity) as stock_on_hand'))
                  ->where('i.lab_id',$lab) 
                  // ->where([['r.status','=','approved']])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->groupBy('t.id','t.item_name')
            ->get();

          $totalFiltered =  $totalRec ;

 $data = array();
          if (!empty($terms)) {
$x=1;



            foreach ($terms as $term) {
                    $nestedData['id']=$x;
               $nestedData['item_id']=$term->id;
                $nestedData['item_name']=$term->item_name;
                $nestedData['catalog_number']=$term->catalog_number;
                $nestedData['place_purchase']=$term->place_of_purchase;
                 $nestedData['unit_issue']= $term->unit_issue;
               $nestedData['min']= $term->minimum_level;
                $nestedData['max']= $term->maximum_level;
                 $nestedData['available']= '<strong>'.$term->stock_on_hand.'</strong>';
                if($term->minimum_level>$term->stock_on_hand){
                $nestedData['status']='<span class="badge badge-danger">Below Minimum</span>';
      }
      if($term->stock_on_hand>$term->minimum_level){
         $nestedData['status']= '<span class="badge badge-warning">Good</span>';
      }
       if($term->stock_on_hand>$term->maximum_level){
        $nestedData['status']=  '<span class="badge badge-success">More than enough</span>';
      }
      else{
          $nestedData['status']='<span class="badge badge-success">Good</span>';
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

      echo json_encode($json_data);*/
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
  
  public function showStockVariance(Request $request){
   $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('cold.reports.list.variance',$data);    
}
}