<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemDisposal;
use App\Models\ItemDisposalDetail;
use App\Models\Inventory;
use App\Models\User;
use App\Services\BinCardService;
use App\Models\Laboratory;
use App\Models\UserSetting;
use App\Models\LaboratorySection;
use App\Notifications\DisposalNotification;
use DB;
class ItemDisposalController extends Controller
{


    // Dispose items

    public function runItemDisposal(Request $request){
 
    //
    if(!empty($request->quantity) && count($request->quantity)>0){
        $ids=array();
        $quantity=array();
        $reason=array();
        for($j=0; $j<count($request->quantity);$j++){
 $f=explode("_",$request->quantity[$j]);
 $ids[]=$f[0];
$quantity[]=$f[1];
$reason[]=$f[2];

}
if(auth()->user()->authority==1 ||auth()->user()->authority==2 ){
    $approved='yes';
    $approved_by=auth()->user()->id;
}
else{
  $approved='no';
   $approved_by=NULL;   
}
try{
    DB::beginTransaction();
$dispose=new ItemDisposal();
$dispose->lab_id=auth()->user()->laboratory_id;
$dispose->section_id=auth()->user()->section_id;
$dispose->disposal_date=now();
$dispose->disposed_by=auth()->user()->id;
$dispose->is_approved=$approved;
$dispose->approved_by=$approved_by;
$dispose->created_at=now();
$dispose->updated_at=NULL;
$dispose->save();
$disposal_id=$dispose->id;

for($x=0;$x<count($ids);$x++){

    $dispose_details=new ItemDisposalDetail();
    $dispose_details->item_disposal_id= $disposal_id;
    $dispose_details->lab_id=auth()->user()->laboratory_id;
    $dispose_details->section_id= auth()->user()->section_id;
    $dispose_details->item_id=$ids[$x];
    $dispose_details->dispose_quantity=$quantity[$x];
    $dispose_details->remarks=$reason[$x];
    $dispose_details->created_at=now();
    $dispose_details->updated_at=NULL;
    $dispose_details->save();
}
if(auth()->user()->authority==1 ||auth()->user()->authority==2 ){
   $this->UpdateInventoryInfo($ids,$quantity,$reason);
   $bincard=new BinCardService();
   $bincard->updateDisposalBinCard($ids,$quantity,$reason);

}
DB::commit();
if(auth()->user()->authority==1 ||auth()->user()->authority==2 ){
return response()->json([
    'message'=>config('stocksentry.disposal.admin_created'),
    'error' =>false

]);
}
else{
$approvers=User::where([['authority','=',2],['laboratory_id','=',auth()->user()->laboratory_id]])->get();
$approver_list=UserSetting::where('lab_id',auth()->user()->laboratory_id)->select('user_id')->get();

$disposed_by=auth()->user()->name.' '.auth()->user()->last_name;
if(!empty($approver_list) && count($approver_list)>0){
foreach ($approver_list as $list) {
$user=User::find($list->user_id);
  $user->notify(new DisposalNotification($disposed_by));
}
}
else{

foreach($approvers as $user){

  $user->notify(new DisposalNotification($disposed_by));
}
}
 return response()->json([
    'message'=>config('stocksentry.disposal.created'),
    'error' =>false

]);   
}
}
catch(Exception $e){
DB::rollback();
return response()->json([
    'message'=>'Failed to run disposal',
    'error' =>true
]);
    }

}
else{
  return response()->json([
    'message'=>'Empty Items',
    'error' =>true
]);  
}

}
protected function UpdateInventoryInfo($ids,$quantity,$reason){
   for($x=0;$x<count($ids);$x++){
   
     DB::table('inventories')->where([['id','=',$ids[$x]]])
         ->decrement('quantity',$quantity[$x],['updated_at'=>now()]);
         if($reason[$x]=='expired'){
         Inventory::where('id',$ids[$x])->update([
         'is_disposed'=>'yes'
         ]);
    
   }
   }
}
public function showDisposalList(){
    return view('inventory.modal.disposal_list');
}
public function loadDisposedItems(Request $request){
    
    $columns = array(
            0=>'id',
            1=>'dispose_date',
            2=> 'disposed_by',
            3=>'approved_by',
            4=>'items',
            5=>'action',
            
           
           
        ); 
   $totalData = ItemDisposal::where([['lab_id','=',auth()->user()->laboratory_id]])
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =ItemDisposal::where([['lab_id','=',auth()->user()->laboratory_id]])

                ->where(function ($query) use ($search){
                  return  $query->where('disposed_by', 'LIKE', "%{$search}%")
                  ->orWhere('approved_by','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
         
            ->limit($limit)
            ->orderBy('id','desc')
            ->get(); 

          $totalFiltered =  $totalRec ;

      
          $data = array();
          
          if (!empty($terms)) {
$x=1;
 
  
         
            foreach ($terms as $term) {
             $user_disposer=User::where('id',$term->disposed_by)->select('name','last_name')->withTrashed()->first();
             $user_approver=User::where('id',$term->approved_by)->select('name','last_name')->withTrashed()->first();
 if(!$user_approver){
                $approver="";
             }
             else{
                $approver=$user_approver->name.' '.$user_approver->last_name;
             }
             $item_count=ItemDisposalDetail::where('item_disposal_id',$term->id)->count();
   

                 $nestedData['id']= $x;
                $nestedData['dispose_date']=date('d, M Y',strtotime($term->disposal_date));
              
               if(!$user_disposer){
            $nestedData['disposed_by']= $term->disposed_by;
               }
               else{
               $nestedData['disposed_by']= $user_disposer->name." ".$user_disposer->last_name;
               }
                // $nestedData['disposed_by']= $user_disposer->name." ".$user_disposer->last_name;
                   
                $nestedData['approved_by'] = $approver;
                $nestedData['items']=$item_count;
if(auth()->user()->authority==1 || auth()->user()->authority==2){
                if($term->is_approved=="no" ){
                    $nestedData['action']='<button type="button" id='.$term->id.' class="btn btn-success" onclick="ApproveDisposal(this.id)"><i class="fa fa-check" aria-hidden="true"> </i> Approve</button> |<button type="button" id='.$term->id.' class="btn btn-danger" onclick="DenyDisposal(this.id)"><i class="fa fa-trash" aria-hidden="true"> </i> Cancel</button>
                    | <button type="button" id='.$term->id.' class="btn btn-info" onclick="ViewDisposal(this.id)"><i class="fa fa-eye" aria-hidden="true"> </i> View</button>';
                
                     }
                     if($term->is_approved=="yes"){
                         $nestedData['action']='<i class="fa fa-check" aria-hidden="true"> </i> Approved || <button type="button" id='.$term->id.' class="btn btn-info" onclick="ViewDisposal(this.id)"><i class="fa fa-eye" aria-hidden="true"> </i> View</button>';
                     }
                     
                     if($term->is_approved=="cancel"){
                         $nestedData['action']='<i class="fa fa-trash" aria-hidden="true"> </i> Cancelled || <button type="button" id='.$term->id.' class="btn btn-info" onclick="ViewDisposal(this.id)"><i class="fa fa-eye" aria-hidden="true"> </i> View</button>';
                     }    
                    
               
              }
              if(auth()->user()->authority==4){
                 if($term->is_approved=="no" ){
                    $nestedData['action']='<button type="button" id='.$term->id.' class="btn btn-success" onclick="ApproveDisposal(this.id)"><i class="fa fa-check" aria-hidden="true"> </i> Approve</button> |<button type="button" id='.$term->id.' class="btn btn-danger" onclick="DenyDisposal(this.id)"><i class="fa fa-trash" aria-hidden="true"> </i> Cancel</button>
                    | <button type="button" id='.$term->id.' class="btn btn-info" onclick="ViewDisposal(this.id)"><i class="fa fa-eye" aria-hidden="true"> </i> View</button>';
                
                     }
                              if($term->is_approved=="yes"){
                         $nestedData['action']='<i class="fa fa-check" aria-hidden="true"> </i> Approved || <button type="button" id='.$term->id.' class="btn btn-info" onclick="ViewDisposal(this.id)"><i class="fa fa-eye" aria-hidden="true"> </i> View</button>';
                     }
                     
                     if($term->is_approved=="cancel"){
                         $nestedData['action']='<i class="fa fa-trash" aria-hidden="true"> </i> Cancelled || <button type="button" id='.$term->id.' class="btn btn-info" onclick="ViewDisposal(this.id)"><i class="fa fa-eye" aria-hidden="true"> </i> View</button>';
                     }
              }

          
            if(auth()->user()->authority==3){
            if($term->is_approved=='no'){
                $nestedData['action']='<i class="fa fa-hourglass-half" aria-hidden="true"> </i> Not Approved || <button type="button" id='.$term->id.' class="btn btn-info" onclick="ViewDisposal(this.id)"><i class="fa fa-eye" aria-hidden="true"> </i> View</button>';
        }
                 if($term->is_approved=="yes"){
                         $nestedData['action']='<i class="fa fa-check" aria-hidden="true"> </i> Approved || <button type="button" id='.$term->id.' class="btn btn-info" onclick="ViewDisposal(this.id)"><i class="fa fa-eye" aria-hidden="true"> </i> View</button>';
                     }
                     
                     if($term->is_approved=="cancel"){
                         $nestedData['action']='<i class="fa fa-trash" aria-hidden="true"> </i> Cancelled || <button type="button" id='.$term->id.' class="btn btn-info" onclick="ViewDisposal(this.id)"><i class="fa fa-eye" aria-hidden="true"> </i> View</button>';
                     }
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
public function viewDisposal(Request $request){
    $disposal=ItemDisposal::where('id',$request->id)->first();
   $user=User::where('id',$disposal->disposed_by)->select('name','last_name','signature')->withTrashed()->first();
  
   $lab=Laboratory::where('id',$disposal->lab_id)->select('lab_name')->withTrashed()->first();;
   $section=LaboratorySection::where('id',$disposal->section_id)->select('section_name')->first();
   if($section){
       $section_name=$section->section_name;
       $lab_name=$lab->lab_name.' / '.$section_name;
   }
   else{
       $lab_name=$lab->lab_name; 
   }
    $user_approver = User::where('id',$disposal->approved_by)->select('name','last_name','signature')->withTrashed()->first();;
    if($user_approver==NULL){
        $name="";
    }
    else{
$name=$user_approver->name.' '.$user_approver->last_name;
$sig=$user_approver->signature;
    }
    if($disposal->is_approved=="yes"){
        $status="Approved";
    }
    else{
        $status="Not Approved";
    }
    $data['id']    = $request->id;
    $data['status'] = $status;
    $data['is_approved']=$disposal->is_approved;
   $data['date']    =   date('d,M Y',strtotime($disposal->disposal_date));
   $data['disposed_by'] = $user->name.' '.$user->last_name;
   $data['signature']  = $user->signature??'';
  $data['approved_by'] = $name;
   $data['approver_sign']=$sig??"";
   $data['disposal_lab']=$lab_name;
   $data['disposal_details']=DB::table('items as t')
                    ->join('inventories as i','i.item_id','=','t.id')
                    ->join('item_disposal_details as d','d.item_id','=','i.id')
                    ->select('d.id as id','t.code','t.catalog_number','i.batch_number','t.item_name','t.unit_issue','i.expiry_date','i.cost','d.dispose_quantity','d.remarks')
                    ->where('d.item_disposal_id',$request->id)->get();
    //$disposal_details=ItemDisposalDetail::where('item_disposal_id',$request->id)->get();
    return view('inventory.modal.disposal_view_details',$data);
}
public function getDisposedItem(Request $request){
    
   $columns = array(
            0=>'id',
            1=>'code',
            2=> 'batch_number',
            3=>'brand',
            4=>'name',
            5=>'unit_issue',
            6=>'disposed',
            5=>'reason',
            
           
           
        ); 
   $totalData = DB::table('items as t')
                    ->join('inventories as i','i.item_id','=','t.id')
                    ->join('item_disposal_details as d','d.item_id','=','i.id')
                    ->where('d.item_disposal_id',$request->id)
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms =DB::table('items as t')
                    ->join('inventories as i','i.item_id','=','t.id')
                    ->join('item_disposal_details as d','d.item_id','=','i.id')
                    ->select('d.id as id','t.code','i.batch_number','t.item_name','t.unit_issue','d.expiry_date','d.cost','d.dispose_quantity','d.reason')
                    ->where('d.item_disposal_id',$request->id)
                ->where(function ($query) use ($search){
                  return  $query->where('t.item_name', 'LIKE', "%{$search}%")
                  ->orWhere('t.code','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
         
            ->limit($limit)
            ->orderBy('d.id','asc')
            ->get(); 

          $totalFiltered =  $totalRec ;

      
          $data = array();
          
          if (!empty($terms)) {
$x=1;
 
         
            foreach ($terms as $term) {
   

                 $nestedData['id']= $x;
                $nestedData['batch_number']=$term->batch_number;
             
                 $nestedData['brand']= $term->brand;
                   
                $nestedData['name'] = $term->item_name;
                $nestedData['unit_issue']=$term->unit_issue;
                $nestedData['disposed'] = $term->dispose_quantity;
                $nestedData['reason']='<span class="badge bg-success font-size-12 ms-2">'.$term->reason.'</span>';

               
              
            
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

public function approveDisposedItem(Request $request){
    $dispose_details=ItemDisposalDetail::where('item_disposal_id',$request->id)->select('item_id','dispose_quantity','remarks')->get();
    try{
        DB::beginTransaction();
       $this->updateInventory($dispose_details);
    $bincard =new BinCardService();
    $bincard->approveItemDisposedCard($dispose_details);
ItemDisposal::where('id',$request->id)->update([
    'approved_by'=>auth()->user()->id,
    'is_approved'=>'yes',
    'updated_at'=>now(),
]);
       DB::commit();
return response()->json([
    'message'=>config('stocksentry.disposal.admin_created'),
    'error'=>false
]);
    }
catch(Exception $e){
 DB::rollback();
return response()->json([
    'message'=>'Failed to approve item  disposal',
    'error'=>true
]);
    }
   
}

private function updateInventory($disposed){
foreach($disposed as $disposed){
   
     DB::table('inventories')->where([['id','=',$disposed->item_id]])
         ->decrement('quantity',$disposed->dispose_quantity,['updated_at'=>now()]);
}
   
}
public function cancelDisposedItem(Request $request){
ItemDisposal::where('id',$request->id)->update([
    'approved_by'=>auth()->user()->id,
    'is_approved'=>'cancel',
    'updated_at'=>now(),
]);
     
return response()->json([
    'message'=>"Disposal Cancelled",
    'error'=>false
]);
}
}