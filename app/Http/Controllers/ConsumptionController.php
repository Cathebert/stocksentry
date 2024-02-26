<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\Consumption;
use App\Models\ConsumptionDetail;
use App\Models\Laboratory;
use App\Models\LaboratorySection;
use App\Models\Inventory;
use App\Models\Adjustment;
use App\Models\BinCard;
use App\Models\User;

use App\Services\BinCardService;
class ConsumptionController extends Controller
{
    //
    public function updateSelected(Request $request){
    
     // dd($request);
     $period=$request->period;
       $date=date('Y-m-d');
if($period==NULL){
return response()->json([
'message'=>"Something went wrong ",
'error'=>true,
      ]);
}
if($period==1){
$start_date=$date;
$specificTime = '13:00:00'; // Format: 'HH:mm:ss'

// Create a Carbon instance for tomorrow
$tomorrow = Carbon::tomorrow();

// Set the specific time for tomorrow
$tomorrowWithTime = $tomorrow->setTimeFromTimeString($specificTime);
$end_date=$tomorrowWithTime;
}
if($period==2){
  $dat = Carbon::now();
  $start_date=$dat->sub('7 days')->format('Y-m-d');
  $end_date=Carbon::now()->subDay();
}
if($period==3){

   $start_date= Carbon::createFromFormat('Y-m-d',$date)->startOfMonth()->format('Y-m-d');
 $end_date= Carbon::createFromFormat('Y-m-d',$date)->endOfMonth()->format('Y-m-d');
}
if($period==4){
    parse_str($request->consumed_form,$out);
  $start_date=$out['start_date'];
  $end_date=$out['end_date'];
}

try{
DB::beginTransaction();
$consumption=new Consumption();
$consumption->lab_id=auth()->user()->laboratory_id;
$consumption->section_id=auth()->user()->section_id;
$consumption->consumption_type_id=$period;
$consumption->consumption_updated_by=auth()->user()->id;
$consumption->start_date=$start_date;
$consumption->end_date=$end_date;
$consumption->created_at=now();
$consumption->updated_at=now();
$consumption->save();
$consumption_id=$consumption->id;
 
  $consump_details=new ConsumptionDetail();
 $consump_details->consumption_id=$consumption_id;
 $consump_details->consumption_type_id=$period;
  $consump_details->lab_id=auth()->user()->laboratory_id;
  $consump_details->section_id=auth()->user()->section_id;
  $consump_details->item_id=$request->id;
  $consump_details->consumed_quantity=$request->consumed;
  $consump_details->consumption_date_id= $consumption_id;
  $consump_details->created_at=now();
  $consump_details->updated_at=now();
$consump_details->save();
   DB::table('inventories')->where([['id','=',$request->id],['lab_id','=',auth()->user()->laboratory_id]])
         ->decrement('quantity',$request->consumed,['updated_at'=>now()]);
    $bincard=new BinCardService();
    $bincard->updateItemConsumedCard($request->id,$request->consumed);
 DB::commit();
 return response()->json([
'message'=>config('stocksentry.consumed.single_item_consumption'),
'error'=>false,
      ]);
}
catch(Exception $e){
  DB::rollback();
  return response()->json([
'message'=>"Something went wrong",
'error'=>true,
      ]); 
}
     
}

  public function show(Request $request) {
      
         $columns = array(
            0 =>'id',
            1=>'code',
            2=>'batch_number',
            3=>'catalog',
            4=>'name',
            5=>'unit',
            5=>'available',
            6=>'consumed',
            7=>'status',
            8=>'last_update',
            9=>'next_update',
            10=>'expiry'
        ); 
   $totalData = DB::table('items as s') 
              ->join('inventories AS t', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.quantity','t.batch_number','t.cost','s.item_name','t.expiry_date')
             
            ->where('t.lab_id','=',auth()->user()->laboratory_id)
            ->where('t.quantity', '>',0)
             ->where('t.expiry_date', '>', date('Y-m-d') )
                ->count();


            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('items as s') 
              ->join('inventories AS t', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.catalog_number','s.item_description','t.item_id','t.quantity','t.batch_number','t.cost','s.unit_issue','s.item_name','t.expiry_date')
              
          ->where('t.lab_id','=',auth()->user()->laboratory_id)
            ->where('t.quantity', '>',0)
             ->where('t.expiry_date', '>', date('Y-m-d') )
   
        
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.brand','LIKE',"%{$search}%")
                   ->orWhere('t.batch_number','LIKE',"%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%") ;
                      
                     
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
  

            foreach ($terms as $term) {

$item=ConsumptionDetail::where([['item_id',$term->id],['lab_id',auth()->user()->laboratory_id]])->latest('id')->first();
if($item){
$cons=Consumption::where('id',$item->consumption_id)->select('consumption_type_id','start_date','end_date')->first();
$timeZone = 'Africa/Blantyre';
   $currentDateTime = Carbon::now($timeZone);
   $onePM = Carbon::createFromTime(13, 0, 0, $timeZone); // 13:00:00

 $today=date('Y-m-d');
        $date =$cons->end_date;
        $daysToAdd = 1;
       // $date = $date->addDays($daysToAdd);


        $today=date('Y-m-d');
        $date =$cons->end_date;
        $daysToAdd = 1;
       // $date = $date->addDays($daysToAdd);
        
       // $currentDateTime = Carbon::now();
$onePM = Carbon::createFromTime(13, 0, 0); // 13:00:00
$test=$currentDateTime->greaterThanOrEqualTo($cons->end_date);

         if ($currentDateTime->greaterThanOrEqualTo($cons->end_date)) {
      
            
    $nestedData['last_update']=$cons->start_date;
    $nestedData['next_update']="";
    $nestedData['consumed'] = "<input type='number'  min='0' id='c_$term->id' size='4' class='form-control' placeholder='Enter Here' name='$term->id' onchange='getText(this.id,this.name)'/>";
    $nestedData['status']="<button class='btn btn-outline-primary' id='$term->id' onclick='saveConsumed(this.id)'> <i class='fa fa-save' arial-hidden='true' id='fa_$term->id'></i></button>";
              
     } 
   

     else{

   $nestedData['last_update']=$cons->start_date;
        $nestedData['next_update']=$cons->end_date;
     $nestedData['consumed'] =$item->consumed_quantity;;
      $nestedData['status'] ="<i class='fa fa-lock' aria-hidden='true'></i>";
    
}


}
else
{
      
$nestedData['last_update']='Unavailable';
        $nestedData['next_update']='Unavailable';
    $nestedData['consumed'] = "<input type='number'  min='0' id='c_$term->id' size='4' class='form-control' placeholder='Enter Here' name='$term->id' onchange='getText(this.id,this.name)'/>";
    $nestedData['status']="<button class='btn btn-outline-primary' id='$term->id' onclick='saveConsumed(this.id)'> <i class='fa fa-save' arial-hidden='true' id='fa_$term->id'></i></button>";
}

                  $nestedData['id']="<input type='checkbox' id='sel_$term->id' name='selected_check' onclick='AddIdToArray(this.id)'/>";
                  $nestedData['batch_number']=$term->batch_number;
                    $nestedData['catalog'] = $term->catalog_number;
                $nestedData['code']=$term->code;
              
            $nestedData['expiry']=date('d,M Y',strtotime($term->expiry_date));
               
                 $nestedData['name']= $term->item_name;
                  $nestedData['unit']= $term->unit_issue;
                    $nestedData['available']= $term->quantity;  
                    
                  
                $data[] = $nestedData;
                 $x++;
}
}
       


                
           
            
          

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalFiltered),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
    );

      echo json_encode($json_data);


    }
  
    
 public function updateMany(Request $request){
  // dd($request);
   try{
       if(!$request->ids){
            return response()->json([
  'message'=>"Failed to update Stock. Failed to run selected data",
  'error'=>true,
]); 
       }
if(count($request->ids)>0 && count($request->consumed)>0){
     $period=$request->period;
       $date=date('Y-m-d');
if($period==NULL){
return response()->json([
'message'=>"Something went wrong ",
'error'=>true,
      ]);
}
if($period==1){
    $start_date=$date;
$specificTime = '13:00:00'; // Format: 'HH:mm:ss'

// Create a Carbon instance for tomorrow
$tomorrow = Carbon::tomorrow();

// Set the specific time for tomorrow
$tomorrowWithTime = $tomorrow->setTimeFromTimeString($specificTime);
$end_date=$tomorrowWithTime;
}
if($period==2){
  $dat = Carbon::now();
  $start_date=$dat->sub('7 days')->format('Y-m-d');
  $end_date=$date;
}
if($period==3){

   $start_date= Carbon::createFromFormat('Y-m-d',$date)->startOfMonth()->format('Y-m-d');
 $end_date= Carbon::createFromFormat('Y-m-d',$date)->endOfMonth()->format('Y-m-d');
}
if($period==4){
    parse_str($request->consumed_form,$out);
  $start_date=$out['start_date'];
  $end_date=$out['end_date'];
}

}
DB::beginTransaction();
$consumption=new Consumption();
$consumption->lab_id=auth()->user()->laboratory_id;
$consumption->section_id=auth()->user()->section_id;
$consumption->consumption_type_id=$period;
$consumption->consumption_updated_by=auth()->user()->id;
$consumption->start_date=$start_date;
$consumption->end_date=$end_date;
$consumption->created_at=now();
$consumption->updated_at=now();
$consumption->save();
$consumption_id=$consumption->id;

for($i=0;$i<count($request->ids);$i++){
    $consump_details=new ConsumptionDetail();
 $consump_details->consumption_id=$consumption_id;
 $consump_details->consumption_type_id=$period;
  $consump_details->lab_id=auth()->user()->laboratory_id;
  $consump_details->section_id=auth()->user()->section_id;
  $consump_details->item_id=$request->ids[$i];
  $consump_details->consumed_quantity=$request->consumed[$i];
  $consump_details->consumption_date_id= $consumption_id;
  $consump_details->created_at=now();
  $consump_details->updated_at=now();
  $consump_details->save();
   DB::table('inventories')->where([['id','=',$request->ids[$i]],['lab_id','=',auth()->user()->laboratory_id]])
         ->decrement('quantity',$request->consumed[$i],['updated_at'=>now()]);
$bincard=new BinCardService();
$bincard->updateItemConsumedCard($request->ids[$i],$request->consumed[$i]);

}

DB::commit();
     return response()->json([
        'message'=>config('stocksentry.consumed.multiple_items_consumption'),
        'error'=>false
      ]);
   }
   catch(Exception $e){
    DB::rollback();
     return response()->json([
        'message'=>"Failed to update",
        'error'=>true
      ]);
   }
     
    }
    public function searchItemAdjustment(Request $request){
       $data =  DB::table('items as s') 
              ->join('inventories AS t', 's.id', '=', 't.item_id')
              ->select('t.id as id','t.batch_number','s.item_name as value')
          ->where('t.lab_id','=',auth()->user()->laboratory_id)
          ->where('s.item_name', 'LIKE', '%'. $request->get('search'). '%')
          ->orWhere('t.batch_number','LIKE','%'. $request->get('search'). '%')
           ->where('t.expiry_date', '>', date('Y-m-d') )
           ->groupBy('t.batch_number')
          ->get();
       /** $data = Item::select("item_name as value", "id")
                    ->where('item_name', 'LIKE', '%'. $request->get('search'). '%')
                    ->get();**/
    
        return response()->json($data);
    }
     public function inventoryAdjustment(Request $request){
      
         $columns = array(
            0 =>'id',
            1=>'code',
            2=>'batch_number',
            3=>'catalog',
            4=>'name',
            5=>'unit',
            5=>'available',
            6=>'consumed',
            7=>'status',
            8=>'action'
        ); 
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.quantity','t.batch_number','t.cost','s.item_name','t.expiry_date')
          ->where('t.lab_id','=',auth()->user()->laboratory_id)
             ->where('t.id', '=', $request->id )
           // ->where('t.expiry_date', '>', date('Y-m-d') )
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->search;
            $terms = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.catalog_number','s.item_description','t.item_id','t.quantity','t.batch_number','t.cost','s.unit_issue','s.item_name','t.expiry_date')
         ->where('t.lab_id','=',auth()->user()->laboratory_id)
         
        ->where('t.id', '=', $request->id )
                //->where(function ($query) use ($search){
                  //return  $query->where('s.code', 'LIKE', "%{$search}%")
                  // ->orWhere('s.code','LIKE',"%{$search}%")
                  //->orWhere('s.item_name','LIKE',"%{$search}%") ;
                      
                     
           // })
           // ->offset($start)
          //  ->limit($limit)
            ->orderBy('t.expiry_date','asc')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
  

            foreach ($terms as $term) {



                $nestedData['id']="<input type='checkbox' id='sel_$term->id' name='selected_check' onclick='AddIdToArray(this.id)'/>";
                  $nestedData['batch_number']=$term->batch_number;
                    $nestedData['catalog']= $term->catalog_number;
                $nestedData['code']=$term->code;
             
                 $nestedData['name']= $term->item_name;
                  $nestedData['unit']= $term->unit_issue;
                    $nestedData['available']=  $term->quantity;
                 $nestedData['consumed'] = '<div class="input-group">
  <div class="input-group-prepend">
    <span class="input-group-text btn btn-primary" onclick="decrementValue()">-</span>
  </div>
  <input type="number" class="form-control" id="adjusted" min=0  value=1 size="1"  style="width: 50px;">
   <span class="input-group-text btn btn-primary" onclick="incrementValue()">+</span>
</div>';
                 
                    $nestedData['status']=' <div class="form-group">
    <select class="form-control" id="type" name="type">
      <option>Addition</option>
      <option>Subtraction</option>
    </select>
  </div>';
            $nestedData['action']  ='';
     
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


/***
 * 
 * Load adjustment modal 
 * 
 * */

public function loadAdjustedItems(Request $request){

 $columns = array(
            0 =>'code',
            1=>'item',
            2=>'available',
            3=>'adjusted',
            4=>'type',
            5=>'remarks',
            6=>'status',
            7=>'action',
            8=>'adjusted_by',
            9=>'id',
            10=>'batch_number',
            11=>'date'
        ); 
   $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->join('adjustment_details as a','a.item_id','=','t.id')
              ->where('t.lab_id','=',auth()->user()->laboratory_id)
         
           // ->where('t.expiry_date', '>', date('Y-m-d') )
              ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->search;
            $terms = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
           ->join('adjustment_details as a','a.item_id','=','t.id')
              ->select('t.id as id','s.code','s.brand','t.quantity as available','t.batch_number','s.item_name','a.id as adjustment_id','a.quantity','a.is_approved','a.approved_by','a.type','a.notes','a.adjusted_by','a.created_at as adjusted_date')
         ->where('t.lab_id','=',auth()->user()->laboratory_id)
         ->where('a.is_approved','<>','cancel')
         
       
            
           // ->offset($start)
           //->limit($limit)
            ->orderBy('a.id','desc')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
  

            foreach ($terms as $term) {
$user=User::where('id',$term->adjusted_by)->select('name','last_name')->first();
$user_name=$user->name.' '.$user->last_name;
            $nestedData['id']=$x;
                $nestedData['code']=$term->code;
                  $nestedData['item']=$term->item_name;
                    $nestedData['available']= $term->available;
                     $nestedData['date']= date('d,M Y',strtotime($term->adjusted_date));
                $nestedData['adjusted']=$term->quantity;
              $nestedData['batch_number']=$term->batch_number;
                 $nestedData['type']= $term->type;
                  $nestedData['remarks']= $term->notes;
                  $nestedData['adjusted_by']= $user_name;
                  
                  if($term->is_approved=="yes"){
                    $user=User::where('id',$term->approved_by)->select('name','last_name')->first();
                       $approver=$user->name.' '.$user->last_name; 
                    $nestedData['status']= "Approved by: <strong>".$approver." </strong>";
                    $nestedData['action']  ="";
            }
               
               else{
                  $nestedData['status']= "No Approved";
                   $nestedData['action']  ='<button type="button" id='.$term->adjustment_id.' class="btn btn-success" onclick="ApproveAdjustment(this.id)"><i class="fa fa-check" aria-hidden="true"> </i> Approve</button>  | <button type="button" id='.$term->adjustment_id.' class="btn btn-danger" onclick="cancelAdjustment(this.id)"><i class="fa fa-trash" aria-hidden="true"> </i> Cancel</button> ';
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
//approve adjustment  
public function approveAdjusted(Request $request){

$adjst=Adjustment::where('id',$request->id)->first();
try{
    DB::beginTransaction();
if($adjst->type=="add"){
    DB::table('inventories')->where([['id','=',$adjst->item_id],['lab_id','=',auth()->user()->laboratory_id]])
         ->increment('quantity',$adjst->quantity,['updated_at'=>now()]);
$bincard=new BinCardService();
$bincard->updateItemAdjustment($adjst->item_id,$adjst->quantity,$adjst->type);
 }
 else{
 DB::table('inventories')->where([['id','=',$adjst->item_id],['lab_id','=',auth()->user()->laboratory_id]])
         ->decrement('quantity',$adjst->quantity,['updated_at'=>now()]);
 $bincard=new BinCardService();
$bincard->updateItemAdjustment($adjst->item_id,$adjst->quantity,$adjst->type);
 }
 Adjustment::where('id',$request->id)->update([
    'approved_by'=>auth()->user()->id,
    'is_approved'=>'yes',
    'updated_at'=>now(),
 ]);
DB::commit();
 return response()->json([
'message'=>config('stocksentry.adjustment.adjustment_success'),
'error'=>false,
      ]);
}
catch(Exception $e){
DB::rollback();
 return response()->json([
'message'=>config('stocksentry.adjustment.adjustment_failed'),
'error'=>true,
      ]);
}
}

//cancel adjustment
public function cancelAdjusted(Request $request){


 Adjustment::where('id',$request->id)->update([
    'approved_by'=>auth()->user()->id,
    'is_approved'=>'cancel',
    'updated_at'=>now(),
 ]);

 return response()->json([
'message'=>'Adjustment has been cancelled ',
'error'=>false,
      ]);


}
 public function viewAdjustments(){
    return view('inventory.modal.adjustment');
 }


    /**
     * adjust the available amount 
     * 
     **/

    public function adjustSelected(Request $request){
    
      //dd($request);

try{


DB::beginTransaction();
 if($request->type=="Addition"){
    $type="add";
     
 }
if($request->type=="Subtraction"){
    $type="sub";
   
 
 }
$adjustment=new Adjustment();
$adjustment->item_id=$request->item;
$adjustment->lab_id= auth()->user()->laboratory_id;
$adjustment->section_id=auth()->user()->section_id;
$adjustment->quantity=$request->adjustment;
$adjustment->type=$type;
$adjustment->adjusted_by=auth()->user()->id;
$adjustment->approved_by=NULL;
$adjustment->notes=$request->notes;
$adjustment->is_approved="no";
$adjustment->created_at=now();
$adjustment->updated_at=NULL;

$adjustment->save();
/**nventory::where([['id','=',$request->id],['lab_id','=',auth()->user()->laboratory_id]])
            ->update([
                'quantity'=>$request->consumed,

                'updated_at'=>now(),
            ]);**/
  
 DB::commit();
 return response()->json([
'message'=>"Your adjustment is pending approval ",
'error'=>false,
      ]);
}
catch(Exception $e){
  DB::rollback();
  return response()->json([
'message'=>"Failed to adjust Item",
'error'=>true,
      ]); 
}
     
}

 public function searchSectionItemAdjustment(Request $request){
       $data =  DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.item_name as value')
          ->where([['t.lab_id','=',auth()->user()->laboratory_id],
            ['t.section_id','=',auth()->user()->section_id]])
          ->where('s.item_name', 'LIKE', '%'. $request->get('search'). '%')
          ->orWhere('s.code','LIKE','%'. $request->get('search'). '%')
           // ->where('t.expiry_date', '>', date('Y-m-d') )
          ->get();
       /** $data = Item::select("item_name as value", "id")
                    ->where('item_name', 'LIKE', '%'. $request->get('search'). '%')
                    ->get();**/
    
        return response()->json($data);
    }
     public function labConsumptionHistory(){
            $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        return view('provider.inventory.inventory_tabs.lab_consumption_history',$data);
    }
    public function loadLabConsumptionHistory(Request $request){
        
$columns = array(
            0 =>'id',
            1=>'consumption_type',
            2=>'range',
            3=>'consumed_count',
            4=>'updated_by',
            5=>'action',
            
        ); 
   $totalData = DB::table('consumptions') 
              
              ->where('lab_id','=',auth()->user()->laboratory_id)
         
           // ->where('t.expiry_date', '>', date('Y-m-d') )
              ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->search;
            $terms = DB::table('consumptions') 
              ->where('lab_id','=',auth()->user()->laboratory_id)      
       
            
            ->offset($start)
           ->limit($limit)
            ->orderBy('id','desc')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
  

            foreach ($terms as $term) {
$count=ConsumptionDetail::where('consumption_id',$term->id)->count();
$type=DB::table('consumption_types')->where('id',$term->consumption_type_id)->select('name')->first();
$user=User::where('id',$term->consumption_updated_by)->select('name','last_name')->first();

                $nestedData['id']=$x;
                  $nestedData['consumption_type']=$type->name;
                  if($term->consumption_type_id==1){
$nestedData['range']= date('d, M Y',strtotime($term->start_date));
                  }
                  else{
                    $nestedData['range']= date('d, M Y',strtotime($term->start_date)).' <b>To</b> '.date('d,M Y',strtotime($term->end_date));
                }
                $nestedData['consumed_count']=$count;
             $nestedData['updated_by']= $user->name??'';
              
                    $nestedData['action']  ='<button type="button" id='.$term->id.' class="btn btn-info" onclick="ViewConsumptionDetails(this.id)"><i class="fa fa-eye" aria-hidden="true"> </i> View Details</button>';
            
     
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
 public function consumptionDetails(Request $request){
     $details=DB::table('consumptions')
                ->where('id',$request->id)->first();
$data['id']=$request->id;
    $data['date']   = date('d,M Y',strtotime($details->created_at)) ;     
  $consume_taker    = User::where('id',$details->consumption_updated_by)->select('name','last_name','signature')->first();
  $consumption_type = DB::table('consumption_types')->where('id',$details->consumption_type_id)->select('name')->first();
$data['consumption_taker']= $consume_taker->name??"";
$data['type']=$consumption_type->name;

 return view('inventory.modal.consumption_history_details',$data);
 }
 public function loadConsumptionData(Request $request){
  
$columns = array(
            0 =>'id',
            1=>'code',
            2=>'batch_number',
            3=>'brand',
            4=>'name',
            5=>'unit_issue',
            6=>'consumed'
            
        ); 
   $totalData = DB::table('consumption_details') 
              
              ->where('consumption_id','=',$request->id)
         
           // ->where('t.expiry_date', '>', date('Y-m-d') )
              ->count();
 


            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->search;
            $terms = DB::table('items as t')
              ->join('inventories as inv','inv.item_id','=','t.id')
              ->join('consumption_details as c','c.item_id','=','inv.id')
              ->select('c.id as id','t.code','inv.batch_number','t.brand','t.item_name','t.unit_issue','c.consumed_quantity')
              ->where('c.consumption_id','=',$request->id)
                   
       
            
            ->offset($start)
           ->limit($limit)
            ->orderBy('id','desc')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
  

            foreach ($terms as $term) {

            $nestedData['id'] = $x;
            $nestedData['code'] = $term->code;
            $nestedData['batch_number'] = $term->batch_number;
            $nestedData['brand'] = $term->brand;
            $nestedData['name']  = $term->item_name;
            $nestedData['unit_issue'] = $term->unit_issue;
            $nestedData['consumed']  =$term->consumed_quantity;
     
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

