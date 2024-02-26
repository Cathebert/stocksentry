<?php
 namespace App\Services;
 use App\Models\Inventory;
 use App\Models\Issue;
 use App\Models\User;
  use App\Models\Laboratory;
 use DB;
 use PDF;
use App\Services\BinCardService;
 class UpdateInventoryService{

    public function UpdateInventory($inventory){
      try{
      DB::beginTransaction();
       $issue= $inventory['issue'][0]['detailIssue'];
       $info=$inventory['issue'][0];
    $doupdate=$this->updateInventoryDetails($inventory['issue'][0]['detailIssue']);
  
     $receipt= $this->generateReceipt($info,$issue);
       $status=$this->changeStatus($inventory['issue'][0],$receipt);
       //$doupdate=$this->updateInventoryDetails($inventory['issue'][0]['detailIssue']);
    DB::commit();
     return config('stocksentry.issues.approved');
      }
      catch(Exception $e){
        DB::rollback();
        return  config('stocksentry.issues.cancelled');;
      }
    }
    protected  function updateInventoryDetails($details){
    
      for($i=0;$i<count($details);$i++){
         DB::table('inventories')->where([['id','=',$details[$i]->item_id],['lab_id','=',auth()->user()->laboratory_id]])
         ->decrement('quantity',$details[$i]->quantity,['updated_at'=>now()]);

      }
         $bincard= new BinCardService();
         $bincard->updateApprovedIssueCard($details);
    }
    protected function changeStatus($issue,$path){
      //dd($issue->siv_number);
Issue::where('siv_number',$issue->siv_number)
->update([
'approved_by'=>auth()->user()->id,
'approve_status'=>'approved',
'issue_document'=>$path,
]);

    }
    public function generateReceipt($inform, $issue_details){
      $info =$inform;
      $issued_by = User::select('name','last_name','email','signature')->where('id',$info->issued_by)->first();
      $data['issued_by'] = $issued_by->name.' '.$issued_by->last_name; 
      $data['email'] = $issued_by->email;
      $data['siv_number']=$info->siv_number;
      $data['issue_date']=$info->issuing_date;
      
         //start 
       
      //aapprove
     if($info->approve_status=='approved'){
         $approver=User::select('name','last_name','email','signature')->where('id',$info->approved_by)->first();
         $data['approved_by']=$approver->name.' '.$approver->last_name;
         $data['approver_sign']=$approver->signature??'';
     }
     else
     {
       $data['approved_by']='';
         $data['approver_sign']='';   
     }
      
      //
      
      $from_lab=Laboratory::where('id',$info->from_lab_id)->select('lab_name')->first();
    $to_lab= Laboratory::where('id',$info->to_lab_id)->select('lab_name')->first();
    $data['from_lab']= $from_lab->lab_name;
    $data['to_lab']= $to_lab->lab_name;
    $data['status']=$info->approve_status;
    $data['issued_by']=  $issued_by->name.' '. $issued_by->last_name;
    $data['signature']=  $issued_by->signature??'';
          
      
      
      //end
$details=array();

    // dd($issue_details);
     for($i=0;$i<count($issue_details); $i++){
      $inventory=Inventory::where('id',$issue_details[$i]->item_id)->select('item_id')->first();
      $issues=DB::table('items as itm')
      ->join('inventories as inv','inv.item_id','=','itm.id')
      ->join('issue_details as d','d.item_id','=','inv.id')
      ->join('issues as iss','iss.id','=','d.issue_id')
      ->select('itm.item_name','itm.unit_issue','inv.cost','d.quantity', 'inv.batch_number','iss.siv_number','inv.item_id')
     ->where([['inv.item_id','=',$inventory->item_id],['iss.from_lab_id','=',auth()->user()->laboratory_id]])
      ->distinct()
      ->get();
     /*/ 
      DB::table('issue_details as i')
      ->join('issues as s','s.id','=','i.issue_id')
      ->join('inventories as in', 'in.item_id','=','i.item_id')

      ->join('items as itm','itm.id','=','i.item_id')
     
      ->select('itm.item_name','in.cost','i.quantity')
      ->where('in.item_id',$inventory->item_id)
      ->distinct()
      ->get();*/

$details[]= $issues;
    }
   // dd($details);
$path=public_path('issues');
    $pdf=PDF::loadView('pdf.siv_receipt',['info'=>$details,'user'=>$data]);
     //$pdf->save($path.'/SIV_'.$info->siv_number.'.pdf'); 
return  'SIV_'.$info->siv_number.'.pdf';
                 // return $pdf->stream();
  }
}