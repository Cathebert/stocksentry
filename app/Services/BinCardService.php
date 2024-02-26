<?php
 namespace App\Services;
 use App\Models\Inventory;
 use App\Models\Issue;
  use App\Models\IssueDetails;
 use App\Models\User;
 use App\Models\BinCard;
  use App\Models\Laboratory;
 use DB;
use Carbon\Carbon;


class BinCardService{

	public function updateReceiveCard($id,$quantity){
	$timeZone = 'Africa/Blantyre';
   $currentDateTime = Carbon::now();
   $currentDateTime->setTimezone($timeZone);
			$item=$id;
			$inventory=Inventory::where('id',$item)->first();
    //dd($inventory);
			try{
				DB::beginTransaction();
    $bincard=new BinCard();
    $bincard->inventory_id=$item;
    $bincard->date= $currentDateTime;
    $bincard->description=config('stocksentry.received_from_supplier');
    $bincard->transaction_type ='supplier';
    $bincard->transaction_number= $inventory->grn_number;
    $bincard->batch_number=$inventory->batch_number;
    $bincard->item_id =$inventory->item_id;
    $bincard->quantity=$quantity;
    $bincard->balance=$inventory->quantity;
    $bincard->lab_id = $inventory->lab_id;
    $bincard->section_id = $inventory->section_id;
    $bincard->created_at = now();
    $bincard->updated_at=NULL;
$bincard->save();
DB::commit();

}
catch(Exception $e){
	DB::rollback();
	
}

			
			
		
		

		
	}
function updateApprovedIssueCard($details){
//dd($details[0]->item_id);
	$timeZone = 'Africa/Blantyre';
   $currentDateTime = Carbon::now();
   $currentDateTime->setTimezone($timeZone);
foreach ($details as $detail) {
	// code...

		$inventory_id=$detail->issue_id;
		$item_id=$detail->item_id;

			$issue = Issue::where('id',$inventory_id)->select('siv_number','from_lab_id','to_lab_id')->first();
			$inventory= DB::table('inventories')->where([['id','=',$item_id],['lab_id','=',auth()->user()->laboratory_id]])->first();
			$lab=Laboratory::where('id',$issue->to_lab_id)->select('lab_name')->first();
				try{
				DB::beginTransaction();
				$value=$lab->lab_name;
				 $configValue = config('stocksentry.issue_to');

    // Replace the placeholder with the dynamic value
    $configValue = str_replace(':name', $value, $configValue);
    $bincard=new BinCard();
    $bincard->inventory_id=$item_id;
    $bincard->date= $currentDateTime;
    $bincard->description=$configValue;
    $bincard->transaction_type ='issued_out';
    $bincard->transaction_number= $issue->siv_number;
    $bincard->batch_number=$inventory->batch_number;
    $bincard->item_id =$inventory->item_id;
    $bincard->quantity=$detail->quantity;
    $bincard->balance=$inventory->quantity;

    $bincard->lab_id = auth()->user()->laboratory_id;
    $bincard->section_id = auth()->user()->section_id;
    $bincard->created_at = now();
    $bincard->updated_at=NULL;
$bincard->save();
DB::commit();

}
catch(Exception $e){
	DB::rollback();
	
}
}

	}

function acceptIssuedCard($issue_id,$item_id,$quantity){
    	$timeZone = 'Africa/Blantyre';
   $currentDateTime = Carbon::now();
   $currentDateTime->setTimezone($timeZone);
$inventory_id=$issue_id;
		$item_id=$item_id;

			$issue = Issue::where('id',$inventory_id)->select('siv_number','from_lab_id','to_lab_id')->first();
		$inventory= DB::table('inventories')->where([['id','=',$item_id],['lab_id','=',$issue->to_lab_id]])->first();
			$lab=Laboratory::where('id',$issue->from_lab_id)->select('lab_name')->first();
				try{
				DB::beginTransaction();
				$value=$lab->lab_name;
				 $configValue = config('stocksentry.issue_received_from');

    // Replace the placeholder with the dynamic value
    $configValue = str_replace(':name', $value, $configValue);
    $bincard=new BinCard();
    $bincard->inventory_id=$item_id;
    $bincard->date= $currentDateTime;
    $bincard->description=$configValue;
    $bincard->transaction_type ='issue_in';
    $bincard->transaction_number= $issue->siv_number;
    $bincard->batch_number=$inventory->batch_number;
    $bincard->item_id =$inventory->item_id;
    $bincard->quantity=$quantity;
     $bincard->balance=$inventory->quantity;
    $bincard->lab_id = $inventory->lab_id;
    $bincard->section_id = $inventory->section_id;
    $bincard->created_at = now();
    $bincard->updated_at=NULL;
$bincard->save();
DB::commit();

}
catch(Exception $e){
	DB::rollback();
	
}

	}

function updateItemConsumedCard($item_id,$consumed){
    	$timeZone = 'Africa/Blantyre';
   $currentDateTime = Carbon::now();
   $currentDateTime->setTimezone($timeZone);
	$inventory= DB::table('inventories')->where([['id','=',$item_id],['lab_id','=',auth()->user()->laboratory_id]])->first();
	try{
				DB::beginTransaction();
				$value=now();
				 $configValue = config('stocksentry.consumed.description');

    // Replace the placeholder with the dynamic value
    $configValue = str_replace(':date', $value, $configValue);
    $bincard=new BinCard();
    $bincard->inventory_id=$item_id;
    $bincard->date= $currentDateTime;
    $bincard->description=$configValue;
    $bincard->transaction_type ='consumed';
    $bincard->transaction_number= $inventory->grn_number;
    $bincard->batch_number=$inventory->batch_number;
    $bincard->item_id =$inventory->item_id;
    $bincard->quantity=$consumed;
     $bincard->balance=$inventory->quantity;
    $bincard->lab_id = $inventory->lab_id;
    $bincard->section_id = $inventory->section_id;
    $bincard->created_at = now();
    $bincard->updated_at=NULL;
$bincard->save();
DB::commit();

}
catch(Exception $e){
	DB::rollback();
	
}

}

function updateItemAdjustment($item_id,$quantity,$type){
	$timeZone = 'Africa/Blantyre';
   $currentDateTime = Carbon::now();
   $currentDateTime->setTimezone($timeZone);
$inventory= DB::table('inventories')->where([['id','=',$item_id],['lab_id','=',auth()->user()->laboratory_id]])->first();
	try{
				DB::beginTransaction();
				if($type=="add"){
				 $configValue = config('stocksentry.adjustment.positive_adjustment');
				}
				else{
				$configValue = config('stocksentry.adjustment.negative_adjustment');	
				}

  
  
    $bincard=new BinCard();
    $bincard->inventory_id=$item_id;
    $bincard->date= $currentDateTime;

    $bincard->description=$configValue;
    $bincard->transaction_type ='adjusted';
    $bincard->transaction_number= $inventory->grn_number;
    $bincard->batch_number=$inventory->batch_number;
    $bincard->item_id =$inventory->item_id;
    $bincard->quantity=$quantity;
    $bincard->balance=$inventory->quantity;
    $bincard->lab_id = $inventory->lab_id;
    $bincard->section_id = $inventory->section_id;
    $bincard->created_at = now();
    $bincard->updated_at=NULL;
$bincard->save();
DB::commit();

}
catch(Exception $e){
	DB::rollback();
	
}
}
public function updateStoreOrder($item_id,$quantity,$lab_id){
    	$timeZone = 'Africa/Blantyre';
   $currentDateTime = Carbon::now();
   $currentDateTime->setTimezone($timeZone);
$inventory= DB::table('inventories')->where([['id','=',$item_id],['lab_id','=',auth()->user()->laboratory_id]])->first();
$lab=Laboratory::where('id',$lab_id)->select('lab_name')->first();

try{
    $value=$lab->lab_name;
	 $configValue = config('stocksentry.order.order_sent');

 $configValue = str_replace(':lab_name', $value, $configValue);

    $bincard=new BinCard();
    $bincard->inventory_id=$item_id;
    $bincard->date= $currentDateTime;

    $bincard->description=$configValue;
    $bincard->transaction_type ='order_sent';
    $bincard->transaction_number= $inventory->grn_number;
    $bincard->batch_number=$inventory->batch_number;
    $bincard->item_id =$inventory->item_id;
    $bincard->quantity=$quantity;
    $bincard->balance=$inventory->quantity;
    $bincard->lab_id = $inventory->lab_id;
    $bincard->section_id = $inventory->section_id;
    $bincard->created_at = now();
    $bincard->updated_at=NULL;
	$bincard->save();


}
catch(Exception $e){
	
}
}


//update received order 
public function updateOrderReceivingLabCard($item_id,$lab_id, $quantity){
$inventory=DB::table('inventories')->where([['item_id','=',$item_id],['lab_id','=',$lab_id]])->first();
	$timeZone = 'Africa/Blantyre';
   $currentDateTime = Carbon::now();
   $currentDateTime->setTimezone($timeZone);

try{

	 $configValue = config('stocksentry.order.order_received');



    $bincard=new BinCard();
    $bincard->inventory_id=$inventory->id;
    $bincard->date=$currentDateTime;

    $bincard->description=$configValue;
    $bincard->transaction_type ='order_received';
    $bincard->transaction_number= $inventory->grn_number;
    $bincard->batch_number=$inventory->batch_number;
    $bincard->item_id =$inventory->item_id;
    $bincard->quantity=$quantity;
    $bincard->balance=$inventory->quantity;
    $bincard->lab_id = $inventory->lab_id;
    $bincard->section_id = $inventory->section_id;
    $bincard->created_at = now();
    $bincard->updated_at=NULL;
	$bincard->save();
}
catch(Exception $e){

}
}
public function updateByInventoryIdOrderReceivingLabCard($item_id,$lab_id, $quantity){
//$inventory= DB::table('inventories')->where([['item_id','=',$item_id],['lab_id','=',$lab_id]])->first();	
$inventory=DB::table('inventories')->where([['id','=',$item_id],['lab_id','=',$lab_id]])->first();
//dd($inventory);
	$timeZone = 'Africa/Blantyre';
   $currentDateTime = Carbon::now();
   $currentDateTime->setTimezone($timeZone);
try{

	 $configValue = config('stocksentry.order.order_received');



    $bincard=new BinCard();
    $bincard->inventory_id=$item_id;
    $bincard->date= $currentDateTime;

    $bincard->description=$configValue;
    $bincard->transaction_type ='order_received';
    $bincard->transaction_number= $inventory->grn_number;
    $bincard->batch_number=$inventory->batch_number;
    $bincard->item_id =$inventory->item_id;
    $bincard->quantity=$quantity;
    $bincard->balance=$inventory->quantity;
    $bincard->lab_id = $inventory->lab_id;
    $bincard->section_id = $inventory->section_id;
    $bincard->created_at = now();
    $bincard->updated_at=NULL;
	$bincard->save();
}
catch(Exception $e){

}
}

public function updateDisposalBinCard($ids,$quantity,$reason){
   	$timeZone = 'Africa/Blantyre';
   $currentDateTime = Carbon::now();
   $currentDateTime->setTimezone($timeZone);
for($x=0;$x<count($ids);$x++){
	switch ($reason[$x]) {
		case 'expired':
			 $configValue = config('stocksentry.disposal.bin_expiry_description');
			break;
		
		case 'damaged':
		 $configValue = config('stocksentry.disposal.bin_description');
		break;

		case 'donated':
		$configValue = config('stocksentry.disposal.bin_donation_description');
		break;
	}
	    
	$inventory=Inventory::where('id',$ids[$x])->first();
    
	$bincard=new BinCard();
    $bincard->inventory_id=$ids[$x];
    $bincard->date= $currentDateTime;

    $bincard->description=$configValue;
    $bincard->transaction_type ='disposed';
    $bincard->transaction_number= $inventory->grn_number;
    $bincard->batch_number=$inventory->batch_number;
    $bincard->item_id =$inventory->item_id;
    $bincard->quantity=$quantity[$x];
    $bincard->balance=$inventory->quantity;
    $bincard->lab_id = $inventory->lab_id;
    $bincard->section_id = $inventory->section_id;
    $bincard->created_at = now();
    $bincard->updated_at=NULL;
	$bincard->save();

}
}

public function approveItemDisposedCard($disposedItems){
	foreach($disposedItems as $disposed){
	switch ($disposed->remarks) {
		case 'expired':
			 $configValue = config('stocksentry.disposal.bin_expiry_description');
			break;
		
		case 'damaged':
		 $configValue = config('stocksentry.disposal.bin_description');
		break;

		case 'donated':
		$configValue = config('stocksentry.disposal.bin_donation_description');
		break;
	}
	 	$timeZone = 'Africa/Blantyre';
   $currentDateTime = Carbon::now();
   $currentDateTime->setTimezone($timeZone);
	$inventory=Inventory::where('id', $disposed->item_id)->first();
    
	$bincard=new BinCard();
    $bincard->inventory_id=$disposed->item_id;
    $bincard->date= $currentDateTime;

    $bincard->description=$configValue;
    $bincard->transaction_type ='disposed';
    $bincard->transaction_number= $inventory->grn_number;
    $bincard->batch_number=$inventory->batch_number;
    $bincard->item_id =$inventory->item_id;
    $bincard->quantity=$disposed->dispose_quantity;
    $bincard->balance=$inventory->quantity;
    $bincard->lab_id = $inventory->lab_id;
    $bincard->section_id = $inventory->section_id;
    $bincard->created_at = now();
    $bincard->updated_at=NULL;
	$bincard->save();


}
}
}