<?php
 namespace App\Services;
 use App\Models\Inventory;
 use App\Models\Issue;
 use App\Models\User;
 use App\Services\BinCardService;
 use DB;

 class AcceptIssueService{

    public function acceptIssue($inventory){
            $issue= $inventory['issue'][0]['detailIssue'];
             $info=$inventory['issue'][0];
      
       try{
        DB::beginTransaction();
$doupdate=$this->updateInventoryDetails($issue,$info);
$updateIssue=$this->updateIssue($info);
DB::commit();
return 0;
       }
       catch(Exception $e){
DB::rollback();
return 1;
       }
        

    }
    protected function updateInventoryDetails($details, $issue)
    {
        //dd(count($details));
        $issue_id=$issue->id;
        foreach ($details as $detail) {
            $inventory = Inventory::where('id', $detail->item_id)
                ->where('lab_id', $issue->from_lab_id)
                ->first();

           $matchingInventory = Inventory::where('item_id', $inventory->item_id)
                ->where('lab_id', $issue->to_lab_id)
                ->exists();
                

                //check if lab has a record and increment otherwise createnew
            if ($matchingInventory) {
                 DB::table('inventories')
                    ->where('item_id', $inventory->item_id)
                    ->where('lab_id', $issue->to_lab_id)
                     
                    ->increment('quantity', $detail->quantity, ['updated_at' => now()]);
//update Bincard for the item

            $inv= Inventory::where('recieved_id', $inventory->recieved_id)
                ->where('lab_id', auth()->user()->laboratory_id)->select('id')->first();
        $bincard= new BinCardService();

     $bincard->acceptIssuedCard($issue_id,$inv->id,$detail->quantity);
            } else {
                $this->createInventory($inventory, $detail->quantity, $issue->to_section_id,$issue->id);
            }
        }
    }
    
protected function createInventory($data ,$quantity,$section_id,$issue_id){

$inventory =new Inventory();
$inventory->lab_id=auth()->user()->laboratory_id;
$inventory->section_id=$section_id;
$inventory->recieved_id=$data->recieved_id;
$inventory->grn_number=$data->grn_number;
$inventory->item_id=$data->item_id;
$inventory->batch_number= $data->batch_number;
$inventory->storage_location=$data->storage_location;
$inventory->quantity=$quantity;
$inventory->expiry_date=$data->expiry_date;
$inventory->cost=$data->cost;
$inventory->pp_no= $data->pp_no;
$inventory->created_at=now();
$inventory->updated_at=NULL;
$inventory->save();
$id=$inventory->id;

 $bincard= new BinCardService();
 $bincard->acceptIssuedCard($issue_id,$id,$quantity);

    }

    protected  function updateIssue($issue){
        Issue::where('siv_number',$issue->siv_number)
->update([

'approve_status'=>'received',
]);
    }

 }