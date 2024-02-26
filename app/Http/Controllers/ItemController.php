<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Laboratory;
use App\Models\LaboratorySection;
use App\Models\ReceivedItem;
use App\Models\Inventory;
use App\Models\ItemTemp;
use App\Models\Issue;
use App\Models\User;
use App\Models\BinCard;
use App\Models\ReceivedItemCheckList;
use App\Services\BinCardService;
use App\Jobs\UploadCSVFileItem;
use Validator;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PDF;
use App\Models\Setting;
use Illuminate\Support\Facades\File;
class ItemController extends Controller
{
    //
 public function createNew(){
        $data['suppliers']=Supplier::select('id','supplier_name')->get();
        $data['laboratory']=Laboratory::select('id','lab_name')->get();
         $data['sections']=DB::table('laboratory_sections')->select('id','section_name')->get();
$uln=Item::select('uln')->latest('id')->first();

  $settings=Setting::find(1);

         if($uln->uln==NULL){
          $data['uln']=$settings->uln_prefix.'0001';
         }
         else{
          $number=str_pad($uln->uln+1, 4, '0', STR_PAD_LEFT);
        
          $data['uln']=$settings->uln_prefix.''.$number;
         }
            $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('inventory.create',$data);
    }
 public function showItems(){
        $data['suppliers']=Supplier::select('id','supplier_name')->get();
        $data['laboratory']=Laboratory::select('id','lab_name')->get();
         $data['sections']=DB::table('laboratory_sections')->select('id','section_name')->get();
            $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
       
if(auth()->user()->authority==1){
 return view('items.show',$data);
}
if(auth()->user()->authority==2){
  return view('items.mod_items',$data);
}
       
    }

      public function showItemsOnTable(Request $request){
      
  $columns = array(
              0 =>'id',
              1=>'code',
              2=> 'image',
              3=>'brand',
              4=>'name',
              5=>'warehouse_size',
              6=>'cat_number',
              7=>'hazardous',
              8=>'storage_temp',
              9=>'unit_issue',
              10=>'stock_level',
              11=>'section',
              12=>'options',
              
          );
  $date=date('Y-m-d');
            $totalData = DB::table('items as i') 
  ->leftjoin('laboratories as l','l.id','=','i.laboratory_id')
  //->join('laboratory_sections as ls','ls.id','=','i.laboratory_sections_id')

          // ->where('created_at','=',$date)
            ->count();

                  $totalRec = $totalData;
            // $totalData = DB::table('appointments')->count();

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $search = $request->input('search.value');
              $terms = DB::table('items AS a')
            ->leftjoin('laboratories as l','l.id','=','a.laboratory_id')
  //->join('laboratory_sections as ls','ls.id','=','a.laboratory_sections_id')
 
  ->select('a.id as id','a.laboratory_id','a.uln','a.code','a.brand','a.catalog_number','a.item_image','a.item_name','a.is_hazardous','a.store_temp','a.unit_issue','a.minimum_level','a.maximum_level','l.lab_name')
            // ->where('created_at','=',$date)
                  ->where(function ($query) use ($search){
                    return  $query->where('a.item_name', 'LIKE', "%{$search}%")
                    ->orWhere('a.code', 'LIKE', "%{$search}%")
                      ->orWhere('a.brand', 'LIKE', "%{$search}%");        
                      
              })
              ->offset($start)
              ->limit($limit)
              ->orderBy($order, $dir)
              ->get();

            $totalFiltered =  $totalRec ;
  //  0 => 'id',
          

            $data = array();
            if (!empty($terms)) {
  $x=1;
              foreach ($terms as $term) {
  $c=url('/'). "/public/upload/items/".$term->item_image ;
  $default=url('/')."/assets/icon/not_available.jpg";

  

                  $nestedData['id']="<input type='checkbox' id='$term->id'  name='check' onclick='selectCheckedItem(this.id)'/>";
                      $nestedData['code']=$term->code;
                  if(empty($term->item_image)){
                        $nestedData['image'] = "<img src='$default' class='img-thumbnail' alt='...' width='50px' height='50px'>";  
                  }
                  else{
                  $nestedData['image'] = "<img src='$c' class='img-thumbnail' alt='...' width='50px' height='50px'>";
                  }
                  $nestedData['brand']= $term->brand??"";
                  $nestedData['name']= $term->item_name??"";
                   $nestedData['warehouse_size']=$term->warehouse_size??"";
                $nestedData['cat_number']=$term->catalog_number??"";
           $nestedData['hazardous']=$term->	is_hazardous;
             $nestedData['storage_temp']=$term->store_temp;
                      $nestedData['unit_issue']=$term->unit_issue;
                        $nestedData['stock_level']= "Min: ". $term->minimum_level." Max: ".$term->maximum_level;
    $nestedData['section']= $term->lab_name??"";
                
                        $nestedData['options']="<a href='#' id='$term->id' onclick='EditItem(this.id)' style='color:#3B71CA' > <i class='fa fa-edit title='Edit Item'></i></a>";
                          $nestedData['options'].= "&nbsp  | &nbsp <a href='#' id='$term->id' onclick='deleteItem(this.id)' style='color:red'> <i class='fa fa-trash  title='Delete'></i></a>";
                
    
                  
                
                    $x++;
                  $data[] = $nestedData;
            }
        }

        $json_data = array(
          "draw" => intval($request->input('draw')),
          "recordsTotal" => intval($totalData),
          "recordsFiltered" => intval($totalFiltered),
          "data" => $data
      );

        echo json_encode($json_data);
            
        
      }


      /**
       * deactivate Item
       */
      public function deactivateItem(Request $request){

       if(!empty($request['deactivated']) && count($request['deactivated'])>0){
        for( $i=0; $i <count($request['deactivated']); $i++){

  $affectedRows=Item::where('id',$request['deactivated'][$i])
->update([
'status'=>'deactivated',
]);
  }
return response()->json([
  'message'=> "Deactivation was a success ".$affectedRows,
  'error' => false,
]);
      
       }
      }
      /**
       * 
       * Show items entered that day only
       */

            public function showTodaysItemsOnTable(Request $request){
    
  $columns = array(
            0 =>'id',
              1=>'code',
              2=> 'image',
              3=>'brand',
              4=>'name',
              5=>'warehouse_size',
              6=>'cat_number',
              7=>'hazardous',
              8=>'storage_temp',
              9=>'unit_issue',
              10=>'stock_level',
              11=>'section',
              12=>'options',
           
              
          );
  $date=date('Y-m-d');

            $totalData = DB::table('items as i') 
  //->join('laboratories as l','l.id','=','i.laboratory_id')
  ->join('laboratory_sections as ls','ls.id','=','i.laboratory_sections_id')

          ->where('i.created_at','=',$date)
            ->count();

                  $totalRec = $totalData;
            // $totalData = DB::table('appointments')->count();

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $search = $request->input('search.value');
              $terms = DB::table('items AS a')
  ->join('laboratories as l','l.id','=','a.laboratory_id')
  //->join('laboratory_sections as ls','ls.id','=','a.laboratory_sections_id')
 
  ->select('a.id as id','a.laboratory_id','a.uln','a.code','a.brand','a.catalog_number','a.item_image','a.item_name','a.is_hazardous','a.store_temp','a.unit_issue','a.minimum_level','a.maximum_level','l.lab_name')
             ->where('a.created_at','=',$date)
                  ->where(function ($query) use ($search){
                    return  $query->where('a.item_name', 'LIKE', "%{$search}%")
                    ->orWhere('a.code', 'LIKE', "%{$search}%")
                      ->orWhere('a.brand', 'LIKE', "%{$search}%");        
                      
              })
              ->offset($start)
              ->limit($limit)
              ->orderBy($order, $dir)
              ->get();

            $totalFiltered =  $totalRec ;
  //  0 => 'id',
          
 $default=url('/')."/assets/icon/not_available.jpg";
            $data = array();
            if (!empty($terms)) {
  $x=1;
              foreach ($terms as $term) {
                  
                   $c=url('/'). "/public/upload/items/".$term->item_image ;
                  
             
 
 



                  $nestedData['id']=$x;
                      $nestedData['code']=$term->code;
                  if(empty($term->item_image)){
                        $nestedData['image'] = "<img src='$default' class='img-thumbnail' alt='...' width='50px' height='50px'>";  
                  }
                  else{
                  $nestedData['image'] = "<img src='$c' class='img-thumbnail' alt='...' width='50px' height='50px'>";
                  }
                  $nestedData['brand']= $term->brand??"";
                  $nestedData['name']= $term->item_name??"";
                    $nestedData['warehouse_size']=$term->warehouse_size??"";
                $nestedData['cat_number']=$term->catalog_number??"";
           $nestedData['hazardous']=$term->is_hazardous??"";
             $nestedData['storage_temp']=$term->store_temp;
                      $nestedData['unit_issue']=$term->unit_issue;
                        $nestedData['stock_level']= "Min: ". $term->minimum_level." Max: ".$term->maximum_level;
                    
    $nestedData['section']= $term->lab_name??"";
            
            
                        $nestedData['options']="<a href='#' id='$term->Id' onclick='EditItem(this.id)' style='color:#3B71CA' > <i class='fa fa-edit title='Edit Item'></i></a>";
                          $nestedData['options'].= "&nbsp  | &nbsp <a href='#' id='$term->Id' onclick='deleteItem(this.id)' style='color:red'> <i class='fa fa-trash  title='Delete'></i></a>";
                
    
                  
                
                    $x++;
                  $data[] = $nestedData;
            }
        }

        $json_data = array(
          "draw" => intval($request->input('draw')),
          "recordsTotal" => intval($totalData),
          "recordsFiltered" => intval($totalFiltered),
          "data" => $data
      );

        echo json_encode($json_data);
            
        
      }
    public function editItem(Request $request){
        $data['item']=DB::table('items')->where('id',$request->id)->first();
           $data['laboratories']=DB::table('laboratories')->select('id','lab_name')->get();
           
           
         return view('inventory.modal.edit',$data);

    }


    public function deleteItem(Request $request){
      try{
  $name=Item::where('id',$request->id)->select('item_image')->first();  
  if($name->item_image!=NULL){
 $main_url = public_path('/')."/upload/items/".$name->item_image;
    if(File::exists($main_url)){
        unlink($main_url);
    }
  }
     $name=Item::where('id',$request->id)->delete(); 
      $message="Item Successfully Deleted"; 
    return response()->json([
            'success' => true ,
            'message' =>$message
        ],200);

}
catch(Exception $e){
    $message="Snap! Failed to  Delete Item"; 
    return response()->json([
            'success' => false ,
            'message' =>$message
        ],200);  
}
    }

    public function updateItem(Request $request){
       
        $image_name="";
       if($request->imagebase64!="data:,"){
 $old_image=Item::where('id',$request->id)->select('item_image')->first();
 if($old_image->item_image!=NULL){
    $imageUnlink = public_path() . "/upload/items/" . $old_image->item_image;
                    if (file_exists($imageUnlink)) {
                        unlink($imageUnlink);
                    }

                }

                $data = $request->imagebase64;

                list($type, $data) = explode(';', $data);
              list(, $data)      = explode(',', $data);

                $data = base64_decode($data);
                $image_name= time().'.png';
                $path = public_path() . "/upload/items/" . $image_name;
                file_put_contents($path, $data);

                Item::where('id',$request->id)
      ->update([
'item_image'=> $image_name,
      ]);
            }
      Item::where('id',$request->id)
      ->update([
        'code'   =>  $request->code, 
 'laboratory_id'  =>  $request->lab_id,
 'brand'   =>  $request->brand,
'item_name'   =>  $request->generic_name,
'item_description'=>  $request->item_description, 
'item_category'=>$request->item_category,
'warehouse_size' =>   $request->warehouse_size,
'catalog_number' =>   $request->cat_number,
'place_of_purchase'=>$request->place_of_purchase,
'is_hazardous' => $request->is_hazardous,
'store_temp'  =>  $request->store_temp,
'unit_issue'   =>   $request->unit_issue,
'minimum_level'    =>  $request->min,
'maximum_level'    =>   $request->max,
'updated_at'   => now(),
      ]);
     
       return response()->json([
            'message'=>"Item Updated Successfully",
            'status'=>"ok",
        ]);
    
    }

    public function receiveInventory(){
           $data['suppliers']=Supplier::select('id','supplier_name')->get();
           $data['laboratories']=Laboratory::where('id',auth()->user()->laboratory_id)->select('id','lab_name')->get();
           $data['sections']=LaboratorySection::select('id','section_name')->get();
              $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}

        return view('inventory.receive_tabs.new_receipt',$data);
    }
    public function AllreceivedInventory(){
           $data['suppliers']=Supplier::select('id','supplier_name')->get();
           $data['laboratories']=Laboratory::select('id','lab_name')->get();
           $data['sections']=LaboratorySection::select('id','section_name')->get();
              $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        return view('inventory.receive_tabs.all_receipts',$data);
    }
    public function saveReceivedInventory(Request $request){
       return response()->json([
            'message'=>"Items will be Saved",
            'status'=>"ok",
        ]);
    }
public function addInventory(Request $request){
$input=$request->all();
$validateInput=$this->validateItemInput($input);
$image_name="";
       if($request->imagebase64!="data:,"){
				$data = $request->imagebase64;

				list($type, $data) = explode(';', $data);
			  list(, $data)      = explode(',', $data);

				$data = base64_decode($data);
				$image_name= time().'.png';
                $path = public_path() . "/upload/items/" . $image_name;
				file_put_contents($path, $data);
			

			  }
         if($validateInput->passes()){
          try{
            $uln_number=Item::select('uln')->latest()->take(1)->first();
        
            if($uln_number->uln==NULL){
         $uln='0001';
         }
         else{
          $uln= str_pad($uln_number->uln+1, 4, '0', STR_PAD_LEFT);
         
         }
             //dd($uln);
        $item=new Item();
  $item->laboratory_id  =   $request->laboratory;   
  $item->uln=$uln;   
$item->laboratory_sections_id   =   $request->lab_section;
$item->code         =   $request->code;
$item->brand        =   $request->brand_name;
$item->item_name    =   $request->generic_name;
$item->item_description=  $request->item_description;  
$item->item_category=$request->item_category;
$item->warehouse_size=  $request->warehouse_size;
$item->catalog_number=$request->cat_number;
$item->place_of_purchase=$request->place_of_purchase;
$item->is_hazardous = $request->is_hazardous;
$item->store_temp = $request->store_temp;
$item->unit_issue   =   $request->unit_issue;
$item->minimum_level    =   $request->min;
$item->maximum_level    =   $request->max;
$item->item_image    = $image_name;
$created_at    =   now();
$updated_at    = null;
$item->save();

     DB::commit();
       $settings=Setting::find(1);
            $uln_number=Item::select('uln')->latest()->take(1)->first();
 $number=str_pad($uln_number->uln+1, 4, '0', STR_PAD_LEFT);
        
          $uln=$settings->uln_prefix.''.$number;
        return   response()->json([
            'message'=>"Item  added Successfully",
            'status'=>"ok",
            'uln'=>$uln,
        ]);
    }
    catch(Exception $e){
      DB::rollback();
    }
         }

  
  
  else{
  return back()->with('errors', $validateInput->errors());
  }
}
protected function validateItemInput (array $data){
       return Validator::make($data,[
        'generic_name'=>'required',
        'warehouse_size'=>'required',
       
    ]);
}
    public function Stats(){
        return response()->json([
            'good'=>300,
            'warning'=>5,
            'expired'=>40,
        ]);
    }
    public function searchItem(Request $request){
        $data = Item::select("item_name as value", "id")
                    ->where('item_name', 'LIKE', '%'. $request->get('search'). '%')
                    ->get();
                    
    
        return response()->json($data);
    }

    public function ItemDetailsUpdate(Request $request){
   
        $data['item']=Item::where('id',$request->id)->first();
        return view('inventory.modal.receive_modal',$data);
    }
    public function addTemporary(Request $request){
     // dd($request);
 try{
    DB::beginTransaction();
    
         if (!empty($request->additional)) {
                    foreach ($request->additional as $key => $val) {
                     
       $temp= new ItemTemp();
       $temp->item_id= $request->id;
       $temp->user_email=auth()->user()->email;
         $temp->user_id=auth()->user()->id;
       $temp->is_saved='no';
       $temp->storage_location=NULL;
       $temp->item_pp=$request->item_pp;
       $temp->has_expiry=$request->has_expiry;
     
       $temp->suitable_for_use=$request->suitable_for_use;

       $temp->correct_temp=$request->correct_temp;
  
          if($request->has_expiry=="yes"){
            $temp->any_expired=$request->any_expired;
       $temp->item_quantity=$request->input('additional.' . $key. '.item_quantity');

       $temp->item_cost=$request->input('additional.' . $key. '.item_cost');
                    $temp->batch_number = $request->input('additional.' . $key. '.item_batch');
                     $temp->expiry_date = $request->input('additional.' . $key . '.item_expiry');
                     $temp->created_at=now();
                     $temp->updated_at=NULL;
                 }
                 else
                 {
                    $temp->item_quantity=$request->input('additional.' . $key. '.item_quantity');

       $temp->item_cost=$request->input('additional.' . $key. '.item_cost');
                    $temp->batch_number = $request->input('additional.' . $key. '.item_batch');
                 }
                     $temp->save();
                 
                    }
                }
                else{
                  return response()->json([
                  'message'=>"Please enter quantity, and cost",
                  'error'=>true,
                ]);   
                }
                DB::commit();
                return response()->json([
                  'message'=>"success",
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
    public function loadReceivedTable(Request $request){
      
        $columns = array(
            0 =>'id',
            1=>'description',
            2=> 'unit',
            3=>'quantity',
            4=>'batch',
            5=>'expiry',
            6=>'cost', 
            7=>'total',
            8=>'action' 
        ); 
$date=date('Y-m-d');
          $totalData = DB::table('item_temps as t') 
              ->join('items AS i', 'i.id', '=', 't.item_id')
              ->select('t.id as id','i.item_description','i.unit_issue','t.item_quantity','t.batch_number','t.expiry_date','t.item_cost')
          ->where('t.user_id','=',auth()->user()->id)
          ->count();

                $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('item_temps as t') 
              ->join('items AS i', 'i.id', '=', 't.item_id')
              ->select('t.id as id','i.code','i.item_name','i.item_description','i.unit_issue','t.item_quantity','t.batch_number','t.expiry_date','t.item_cost')
          ->where([['t.user_id','=',auth()->user()->id],['t.is_saved','=','no']])
          
                ->where(function ($query) use ($search){
                  return  $query->where('i.item_name', 'LIKE', "%{$search}%");
                      
                     
            })
            //->offset($start)
            //->limit($limit)
            ->orderBy($order, $dir)
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
$t=0;
$total=0;
            foreach ($terms as $term) {



                $nestedData['id']=$term->code;
                $nestedData['description']=$term->item_name;
             
                 $nestedData['unit']= $term->unit_issue;
                $nestedData['quantity']= $term->item_quantity;
                $nestedData['batch']=$term->batch_number;
                 $nestedData['expiry']=  $term->expiry_date;
                $nestedData['cost']= $term->item_cost;
                 $nestedData['total']=$term->item_quantity*$term->item_cost;
                $nestedData['action']="<a href='#' id='$term->id' onclick='DeleteItem(this.id)' style='color:#FF0000' > <i class='fa fa-trash title='remove'></i></a>";
                     
              $t= $term->item_quantity*$term->item_cost;
   $total=$total+$t;
                
               
                   $x++;
                $data[] = $nestedData;
           }
      }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data,
        "total"=>$total,
        "count"=>  $totalData,
    );

      echo json_encode($json_data);
          
        
    }
  public function  getTotalCost(Request $request){
   $data=ItemTemp::where([['user_email','=',auth()->user()->id],['is_saved','=','no']])->select('item_quantity','item_cost')->get();
   $t=0;
$total=0;
   foreach($data as $term){
         $t= $term->item_quantity*$term->item_cost;
   $total=$total+$t;
                

   }
   return response()->json([
    "total"=>$total
   ]);
  }
  public function saveReceivedItems(Request $request){
    
  
  
  try{
    DB::beginTransaction();
$temp=ItemTemp::where([['user_id','=',auth()->user()->id],['is_saved','=','no']])->get();
 // dd($temp);
$received=new ReceivedItem();
$received->lab_id = $request->lab_id;
$received->section_id = auth()->user()->section_id;
$received->supplier_id=$request->supplier_id;
$received->received_description=config('stocksentry.received_from_supplier');
$received->grn_number=$request->grn_number;
$received->po_reference=$request->po_ref;
$received->receiving_date=date('Y-m-d', strtotime($request->receiving_date));
$received->received_id=auth()->user()->id;
$received->received_by=auth()->user()->name." ".auth()->user()->last_name;

$received->created_at=now();
$received->updated_at=NULL;
$received->save();
$id=$received->id;

foreach($temp as $temp){
$inventory=new Inventory();
$inventory->lab_id=$request->lab_id;
$inventory->section_id=$request->section_id;
$inventory->recieved_id= $id;
$inventory->storage_location=$temp->storage_location;
$inventory->grn_number=$request->grn_number;
$inventory->item_id=$temp->item_id;
$inventory->batch_number=$temp->batch_number;
$inventory->quantity=$temp->item_quantity;
$inventory->expiry_date=$temp->expiry_date;
$inventory->cost=$temp->item_cost;
$inventory->pp_no= $temp->item_pp;
$inventory->created_at=now();
$inventory->updated_at=NULL;
$inventory->save();

$item_id=$inventory->id;
$bincard=new BinCardService();
$bincard->updateReceiveCard($item_id,$temp->item_quantity);

$itemchecklist=new ReceivedItemCheckList();
$itemchecklist->inventory_id=$item_id;
$itemchecklist->item_id=$temp->item_id;
$itemchecklist->any_expired=$temp->any_expired;
$itemchecklist->correct_temp=$temp->correct_temp;
$itemchecklist->suitable_for_use=$temp->suitable_for_use;
$itemchecklist->save();
}

//ItemTemp::where('user_email',auth()->user()->email)->delete();
ItemTemp::where([['user_id','=',auth()->user()->id],['is_saved','=','no']])->update([
'is_saved'=>'yes',
]);

DB::commit();
 
  if ($request->session()->has('print_'.auth()->user()->id)) {
     $request->session()->forget('print_'.auth()->user()->id);
//$this->generateGRNReceipt($request->grn_number);
}
$request->session()->put('print_'.auth()->user()->id, $request->grn_number);
     return response()->json([
      'message'=>config('stocksentry.received.received_complete'),
      'grn_number'=>$request->grn_number,
      'error'=>false,
    ]);
   }

catch(Exception $e){
DB::rollback();
return response()->json([
      'message'=> config('stocksentry.received.received_failed'),
      'error'=>true,
    ]);
}
   
  }
  /*private function updateBinCard($item){
    //dd($item);
    $inventory=Inventory::where('id',$item)->first();
    //dd($inventory);
    $bincard=new BinCard();
    $bincard->inventory_id=$item;
    $bincard->date=now();
    $bincard->description=config('stocksentry.received_from_supplier');
    $bincard->transaction_type ='supplier';
    $bincard->transaction_number= $inventory->grn_number;
    $bincard->item_id =$inventory->item_id;
    $bincard->quantity=$inventory->quantity;
    $bincard->lab_id = $inventory->lab_id;
    $bincard->section_id = $inventory->section_id;
    $bincard->created_at = now();
    $bincard->updated_at=NULL$
bincard->save();

  }*/
  public function printTest(Request $request){
 $value = $request->id;
$data['value']=$value;


$infor=ReceivedItem::where('grn_number',$value)->select('lab_id','section_id','receiving_date','received_by','supplier_id','receiver_id','po_reference')->first();

$data['supplier']=Supplier::where('id',$infor->supplier_id)->select('supplier_name','address','email','phone_number')->first();
$lab=Laboratory::where('id',$infor->lab_id)->select('lab_name')->first();
if($infor->section_id!=0){
   $section=LaboratorySection::where('id',$infor->section_id)->select('section_name')->first();
   $sec=$section->section_name??'';
   $data['lab']=$lab->lab_name.' | '.$sec;
}

$data['lab']=$lab->lab_name;
$data['po_ref']=$infor->po_reference;
$data['date_received']=date('d, M Y',strtotime($infor->receiving_date));
$data['print_data'] = DB::table('items as t') 
              ->join('inventories AS i', 'i.item_id', '=', 't.id')
              ->select('i.id as id','i.lab_id','t.item_name','t.unit_issue','i.quantity','i.batch_number','i.cost')
              ->where([['i.grn_number','=',$value],['i.lab_id','=',auth()->user()->laboratory_id]])
              ->get();


if($infor->receiver_id!=NULL){
$user=User::where('id',$infor->receiver_id)->select('signature')->first();
$data['signature']=$user->signature;
}
else{
   $data['signature']='';
}

          
    return view('inventory.modal.print_modal',$data);
  }
public function downloadDocument(Request $request){

  switch($request->type){
    case 'receive':

       $value = $request->id;
$data['value']=$value;
$infor=ReceivedItem::where('grn_number',$value)->select('receiving_date','received_by','supplier_id','receiver_id')->first();
$data['receiving_date']=date('d, M Y',strtotime($infor->receiving_date));
$data['received_by']=$infor->received_by;
$user=User::select('signature')->where(id,$infor->receiver_id)->first();
$data['signature']=$user->signature??'';
$supplier=Supplier::where('id',$infor->supplier_id)->select('supplier_name')->first();
$data['supplier']=$supplier->supplier_name;
$data['print_data']= DB::table('items as t') 
              ->join('inventories AS i', 'i.item_id', '=', 't.id')
              ->select('i.id as id','t.item_name','t.unit_issue','i.quantity','i.batch_number','t.code','i.cost')
              ->where('i.grn_number',$value)
              // ->groupBy('t.item_name')
              ->get();
           
if($request->action=="download"){
 $pdf=PDF::loadView('pdf.download',$data);
                  return $pdf->download('GRN_'.$value.'.pdf'); 
}
else{
 $pdf=PDF::loadView('pdf.download',$data);
                  return $pdf->stream();
}
      break;


  case 'issue':
$value = $request->id;

  $info=Issue::where('siv_number',$value)->first();
        $issued_by = User::select('name','last_name','email','signature')->where('id',$info->issued_by)->first();
      $data['issued_by'] = $issued_by->name.' '.$issued_by->last_name; 
      $data['email'] = $issued_by->email;
      $data['siv_number']=$info->siv_number;
      $data['issue_date']=date('d, M Y',strtotime($info->issuing_date));
      
      //aapprove
     if($info->approve_status=='approved'){
         $approver=User::select('name','last_name','email','signature')->where('id',$info->approved_by)->first();
         $data['approved_by']=$approver->name.' '.$approver->last_name;
         $data['approver_sign']=$approver->signature??NULL;
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
    $data['signature']=  $issued_by->signature??NULL;
          
      //
 $data['value']=$value;
  $data['info']=DB::table('items as itm')
      ->join('inventories as inv','inv.item_id','=','itm.id')
      ->join('issue_details as d','d.item_id','=','inv.id')
      ->join('issues as iss','iss.id','=','d.issue_id')
      ->select('itm.item_name','itm.unit_issue','inv.cost','d.quantity','itm.code', 'inv.batch_number','iss.siv_number','inv.item_id')
     ->where('iss.siv_number','=',$value)
      ->distinct()
      ->get();

      if($request->action=="download"){
 $pdf=PDF::loadView('pdf.issue',$data);
                  return $pdf->download('SIV_'.$value.'.pdf'); 
}
else{
 $pdf=PDF::loadView('pdf.issue', $data);
                  return $pdf->stream();
}

    break;

    case 'requisition':
      $requisition=DB::table('requisitions as r')
                  ->join('users as u','u.id','=','r.requested_by')
                   ->join('laboratories as l','l.id','=','r.lab_id')
                   ->select('r.status','u.name','u.last_name','u.signature','r.sr_number','r.section_id','l.lab_name','r.requested_date','r.approved_by')
                  ->where('r.sr_number',$request->id)
                  ->first();
$data['issued_by']=$requisition->name.' '.$requisition->last_name;
$data['status']= $requisition->status;
$data['sr']=$requisition->sr_number;
$data['id']=$request->id;
$data['signature']=$requisition->signature;
$approver=User::where('id',$requisition->approved_by)->select('name','last_name','signature')->first();
if($approver){
$data['approved_by']=$approver->name.' '.$approver->last_name;
$data['approver_sign']=$approver->signature??'';
}
else{
  $data['approved_by']='';
$data['approver_sign']='';  
}

if($requisition->section_id!=NULL){
    $section=LaboratorySection::where('id',$requisition->section_id)->select('section_name')->first();
    $data['lab']=$requisition->lab_name.'|'.$section->section_name;
}
else{
  $data['lab']=$requisition->lab_name;   
}
$data['requested_date']=date('d, M Y',strtotime($requisition->requested_date));
 $data['requests']=DB::table('requisitions as itm')
     //
      ->join('requisition_details  as d','itm.id','=','d.requisition_id')
       ->join('inventories as inv','inv.id','=','d.item_id')
       ->join('items as iss','iss.id','=','inv.item_id')
      ->select('itm.sr_number','iss.item_name','iss.unit_issue','iss.code','d.quantity_requested','inv.cost','inv.batch_number')
     ->where('itm.sr_number','=',$request->id)
      ->groupBy('d.item_id')
     
      ->get();
      
if($request->action=="download"){
 $pdf=PDF::loadView('pdf.requisition',$data);
                  return $pdf->download('SR_'.$request->id.'.pdf'); 
}
else{
 $pdf=PDF::loadView('pdf.requisition', $data);
                  return $pdf->stream();
}
      break;
  }
         

}
public function loadReceivedItems(Request $request){



          $columns = array(
            0 =>'id',
            1=>'receiving_date',
            2=> 'Supplier',
            3=>'Received_by',
            4=>'action' ,
        ); 
   $totalData = DB::table('received_items as t') 
              ->join('suppliers AS s', 's.id', '=', 't.supplier_id')
              ->select('t.id as id','t.grn_number','t.received_by','s.supplier_name','t.receiving_date')
          ->where('t.lab_id','=',auth()->user()->laboratory_id)
          ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('received_items as t') 
                ->join('suppliers AS s', 's.id', '=', 't.supplier_id')
               ->select('t.id as id','t.grn_number','t.received_by','s.supplier_name','t.receiving_date')
         ->where('t.lab_id','=',auth()->user()->laboratory_id)
          
                ->where(function ($query) use ($search){
                  return  $query->where('t.grn_number', 'LIKE', "%{$search}%")
                  ->orWhere('s.supplier_name','LIKE',"%{$search}%");
                      
                     
            })
            //->offset($start)
            //->limit($limit)
            ->orderBy($order, $dir)
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
 

            foreach ($terms as $term) {

$print=route('received.generateprint',['id'=>$term->grn_number]);
$download=route('received.generatepdf',['id'=>$term->grn_number]);

                $nestedData['id']=$term->grn_number;
                $nestedData['receiving_date']=$term->receiving_date;
             
                 $nestedData['Supplier']= $term->supplier_name;
                $nestedData['Received_by']= $term->received_by;
               
                $nestedData['action']= "<a href='#' id='$term->grn_number' onclick=' showItemDetails(this.id)' ><i class='fa fa-eye'></i><u>View</u></a>";
                 $nestedData['action'].='<ul class="navbar-nav"><li class="nav-item dropdown" >
         <a class="nav-link dropdown-toggle " href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-file"></i>   Export To
        </a>
         <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href='.$download.' name="download" id='.$term->grn_number.' onclick=generatePDF(this.id,this.name)><i class="fa fa-file-pdf" aria-hidden="true"></i> PDF</a>
        
          <div class="dropdown-divider"></div>
          <a class="dropdown-item"  href='.$print.' id='.$term->grn_number.' ><i class="fa fa-print" aria-hidden="true"></i> Print</a>
        </div>
      </li></ul>';
               
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
public function generatePrint(Request $request, $id){

  $value = $id;
$data['grn_number']=$value;
$data['value']=$value;

$infor=ReceivedItem::where('grn_number',$value)->select('lab_id','section_id','receiving_date','received_by','supplier_id','receiver_id','po_reference')->first();
//dd($infor);
if($infor->receiver_id!=NULL){
$data['signature']=User::find($infor->receiver_id)->signature;
}
else{
   $data['signature']='';
}
$data['supplier']=Supplier::where('id',$infor->supplier_id)->select('supplier_name','address','email','phone_number')->first();
$lab=Laboratory::where('id',$infor->lab_id)->select('lab_name')->first();
if($infor->section_id!=0){
   $section=LaboratorySection::where('id',$infor->section_id)->select('section_name')->first();
   $sec=$section->section_name??'';
   $data['lab']=$lab->lab_name.' | '.$sec;
}

$data['lab']=$lab->lab_name;
$data['po_ref']=$infor->po_reference;
$data['received_date']=date('d, M Y',strtotime($infor->receiving_date));


   $data['received_by']=$infor->received_by??"N/A";


$data['info']= DB::table('items as t') 
              ->join('inventories AS i', 'i.item_id', '=', 't.id')
              ->select('i.id as id','i.lab_id','t.item_name','t.unit_issue','i.quantity','i.batch_number','i.cost')
              ->where([['i.grn_number','=',$value],['i.lab_id','=',auth()->user()->laboratory_id]])
               ->groupBy('t.item_name')
              ->get(); 
         
 $pdf=PDF::loadView('pdf.goods_receiving_note',$data); 
  return $pdf->stream();



}
public function generatePDF(Request $request){
  $value = $request->id;

$data['grn_number']=$value;
$data['value']=$value;

$infor=ReceivedItem::where('grn_number',$value)->select('lab_id','section_id','receiving_date','received_by','supplier_id','receiver_id','po_reference')->first();
//dd($infor);
if($infor->receiver_id!=NULL){
$data['signature']=User::find($infor->receiver_id)->signature;
}
else{
   $data['signature']='';
}
$data['supplier']=Supplier::where('id',$infor->supplier_id)->select('supplier_name','address','email','phone_number')->first();
$lab=Laboratory::where('id',$infor->lab_id)->select('lab_name')->first();
if($infor->section_id!=0){
   $section=LaboratorySection::where('id',$infor->section_id)->select('section_name')->first();
   $sec=$section->section_name??'';
   $data['lab']=$lab->lab_name.' | '.$sec;
}

$data['lab']=$lab->lab_name;
$data['po_ref']=$infor->po_reference;
$data['received_date']=date('d, M Y',strtotime($infor->receiving_date));


   $data['received_by']=$infor->received_by??"N/A";


$data['info']= DB::table('items as t') 
              ->join('inventories AS i', 'i.item_id', '=', 't.id')
              ->select('i.id as id','i.lab_id','t.item_name','t.unit_issue','i.quantity','i.batch_number','i.cost')
              ->where([['i.grn_number','=',$value],['i.lab_id','=',auth()->user()->laboratory_id]])
               ->groupBy('t.item_name')
              ->get(); 
            
 $pdf=PDF::loadView('pdf.goods_receiving_note',$data);
                return $pdf->download('GRN_'.$value.'.pdf'); 





}
public function receivedDetails(Request $request){

   $value = $request->id;
$data['value']=$value;

$infor=ReceivedItem::where('grn_number',$value)->select('lab_id','section_id','receiving_date','received_by','receiver_id','supplier_id','po_reference')->first();
//dd($infor);
$data['supplier']=Supplier::where('id',$infor->supplier_id)->select('supplier_name','address','email','phone_number')->first();
$lab=Laboratory::where('id',$infor->lab_id)->select('lab_name')->first();
if($infor->section_id!=0){
   $section=LaboratorySection::where('id',$infor->section_id)->select('section_name')->first();
   $sec=$section->section_name??'';
   $data['lab']=$lab->lab_name.' | '.$sec;
}

$data['lab']=$lab->lab_name;
$data['po_ref']=$infor->po_reference;
$data['date_received']=date('d, M Y',strtotime($infor->receiving_date));
$data['print_data']= DB::table('items as t') 
              ->join('inventories AS i', 'i.item_id', '=', 't.id')
              ->select('i.id as id','i.lab_id','t.item_name','t.unit_issue','i.quantity','i.batch_number','i.cost')
              ->where([['i.grn_number','=',$value],['i.lab_id','=',auth()->user()->laboratory_id]])
              ->get();

//$received=ReceivedItem::where('grn_number',$value)->select('receiver_id')->first();
if($infor->receiver_id!=NULL){
$user=User::where('id',$infor->receiver_id)->select('signature')->first();
$data['signature']=$user->signature;
}
else{
   $data['signature']='';
}


    return view('inventory.modal.all_received_modal',$data);
}
public function getReceivedFiltered(Request $request){
      parse_str($request->all_receipts,$out);
  $filtered=$out;
 
    $columns = array(
            0 =>'id',
            1=>'receiving_date',
            2=> 'Supplier',
            3=>'Received_by',
            4=>'action' ,
        ); 

  if($filtered['start_date']&& $filtered['end_date']){
      $totalData = DB::table('received_items as t') 
              ->join('suppliers AS s', 's.id', '=', 't.supplier_id')
              ->select('t.id as id','t.grn_number','t.received_by','s.supplier_name','t.receiving_date')
          ->where([['t.lab_id','=',auth()->user()->laboratory_id],['s.id','=',$filtered['supplier']]])
             ->whereBetween(DB::raw('DATE(t.receiving_date)'), array($filtered['start_date'],  $filtered['end_date']))
          ->count();


            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
           
          $totalFiltered =  $totalRec ;
//  0 => 'id',
    

           $terms = DB::table('received_items as t') 
                ->join('suppliers AS s', 's.id', '=', 't.supplier_id')
               ->select('t.id as id','t.grn_number','t.received_by','s.supplier_name','t.receiving_date')
         ->where([['t.lab_id','=',auth()->user()->laboratory_id],['s.id','=',$filtered['supplier']]])
             ->whereBetween(DB::raw('DATE(t.receiving_date)'), array($filtered['start_date'],  $filtered['end_date']))
                ->where(function ($query) use ($search){
                  return  $query->where('t.grn_number', 'LIKE', "%{$search}%")
                  ->orWhere('s.supplier_name','LIKE',"%{$search}%");
                      
                     
            })
            //->offset($start)
            //->limit($limit)
            ->orderBy($order, $dir)
            ->get();

  }



  else{
    $totalData = DB::table('received_items as t') 
              ->join('suppliers AS s', 's.id', '=', 't.supplier_id')
              ->select('t.id as id','t.grn_number','t.received_by','s.supplier_name','t.receiving_date')
          ->where([['t.lab_id','=',auth()->user()->laboratory_id],['s.id','=',$filtered['supplier']]])
          
          ->count();


            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
           
          $totalFiltered =  $totalRec ;
//  0 => 'id',
    

           $terms = DB::table('received_items as t') 
                ->join('suppliers AS s', 's.id', '=', 't.supplier_id')
               ->select('t.id as id','t.grn_number','t.received_by','s.supplier_name','t.receiving_date')
         ->where([['t.lab_id','=',auth()->user()->laboratory_id],['s.id','=',$filtered['supplier']]])
          
                ->where(function ($query) use ($search){
                  return  $query->where('t.grn_number', 'LIKE', "%{$search}%")
                  ->orWhere('s.supplier_name','LIKE',"%{$search}%");
                      
                     
            })
            //->offset($start)
            //->limit($limit)
            ->orderBy($order, $dir)
            ->get();
  }
        


          $data = array();
          if (!empty($terms)) {
$x=1;
 

            foreach ($terms as $term) {
$print=route('received.generateprint',['id'=>$term->grn_number]);
$download=route('received.generatepdf',['id'=>$term->grn_number]);



                $nestedData['id']=$term->grn_number;
                $nestedData['receiving_date']=$term->receiving_date;
             
                 $nestedData['Supplier']= $term->supplier_name;
                $nestedData['Received_by']= $term->received_by;
               
                $nestedData['action']= "<a href='#' id='$term->grn_number' onclick='showItemDetails(this.id)' ><i class='fa fa-eye'></i><u>View</u></a>";
               $nestedData['action'].='<ul class="navbar-nav"><li class="nav-item dropdown" >
         <a class="nav-link dropdown-toggle " href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-file"></i>   Export To
        </a>
         <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href='.$download.' name="download" id='.$term->grn_number.' onclick=generatePDF(this.id,this.name)><i class="fa fa-file-pdf" aria-hidden="true"></i> PDF</a>
        
          <div class="dropdown-divider"></div>
          <a class="dropdown-item"  href='.$print.' id='.$term->grn_number.' ><i class="fa fa-print" aria-hidden="true"></i> Print</a>
        </div>
      </li></ul>';
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
public function uploadCVSItemList(Request $request){
   if ($request->hasFile('fileToUpload')) {
        $file_name = $request->file('fileToUpload')->getClientOriginalName();

     //  $earn_proof = $request->file('fileToUpload')->storeAs("public/upload/cvs/", $file_name);
        $request->file('fileToUpload')-> move(public_path().'/upload/cvs',$file_name);
       $file_path=public_path().'/upload/cvs/'.$file_name;
UploadCSVFileItem::dispatch($file_path);
    }
      return response()->json(['result' => true, 'message'=> "File uploaded will notify you of the progress " .$file_name], 200);
}

public function searchFilterItem(Request $request){
    //dd($request);
  $columns = array(
              0 =>'id',
              1=>'code',
              2=> 'image',
              3=>'brand',
              4=>'name',
              5=>'warehouse_size',
              6=>'cat_number',
              7=>'hazardous',
              8=>'storage_temp',
              9=>'unit_issue',
              10=>'stock_level',
              11=>'section',
              12=>'options',
              
          );
  $date=date('Y-m-d');
    
            switch ($request->type) {
              case 'code':
                          $totalData = DB::table('items as i') 
  
          ->where('i.code','LIKE',"%{$request->value}%")
            ->count();

                  $totalRec = $totalData;
            // $totalData = DB::table('appointments')->count();

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $search = $request->input('search.value');
           
                  
                  
                $terms = DB::table('items AS a')
           ->join('laboratories as l','l.id','=','a.laboratory_id')
  //->join('laboratory_sections as ls','ls.id','=','a.laboratory_sections_id')
 
  ->select('a.id as id','a.laboratory_id','a.uln','a.code','a.brand','a.catalog_number','a.item_image','a.item_name','a.is_hazardous','a.store_temp','a.unit_issue','a.minimum_level','a.maximum_level','l.lab_name')
             ->where('a.code','LIKE',"%{$request->value}%")
                  ->where(function ($query) use ($search){
                    return  $query->where('a.item_name', 'LIKE', "%{$search}%")
                    ->orWhere('a.code', 'LIKE', "%{$search}%")
                      ->orWhere('a.brand', 'LIKE', "%{$search}%");        
                      
              })
              ->offset($start)
              ->limit($limit)
              ->orderBy($order, $dir)
              ->get();
                break;
              
              case "name":
                       $totalData = DB::table('items as i') 
  
          ->where('i.code','LIKE',"%{$request->value}%")
            ->count();

                  $totalRec = $totalData;
            // $totalData = DB::table('appointments')->count();

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $search = $request->input('search.value');
                  
                $terms = DB::table('items AS a')
          ->join('laboratories as l','l.id','=','a.laboratory_id')
  //->join('laboratory_sections as ls','ls.id','=','a.laboratory_sections_id')
 
  ->select('a.id as id','a.laboratory_id','a.uln','a.code','a.brand','a.catalog_number','a.item_image','a.item_name','a.is_hazardous','a.store_temp','a.unit_issue','a.minimum_level','a.maximum_level','l.lab_name')
  //->select('a.id as Id','a.code','a.brand','a.item_image','a.item_name','a.unit_issue','a.minimum_level','a.maximum_level','ls.section_name')
             ->where('a.item_name','LIKE',"%{$request->value}%")
                  ->where(function ($query) use ($search){
                    return  $query->where('a.item_name', 'LIKE', "%{$search}%")
                    ->orWhere('a.code', 'LIKE', "%{$search}%")
                      ->orWhere('a.brand', 'LIKE', "%{$search}%");        
                      
              })
              ->offset($start)
              ->limit($limit)
              ->orderBy($order, $dir)
              ->get();
                break;

                case 'lab':
                         $totalData = DB::table('items as i') 
  
           ->where('i.laboratory_id','=',$request->value)
            ->count();

                  $totalRec = $totalData;
            // $totalData = DB::table('appointments')->count();

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $search = $request->input('search.value');
            
                   $terms = DB::table('items AS a')
         ->join('laboratories as l','l.id','=','a.laboratory_id')
  //->join('laboratory_sections as ls','ls.id','=','a.laboratory_sections_id')
 
  ->select('a.id as id','a.laboratory_id','a.uln','a.code','a.brand','a.catalog_number','a.item_image','a.item_name','a.is_hazardous','a.store_temp','a.unit_issue','a.minimum_level','a.maximum_level','l.lab_name')
             ->where('a.laboratory_id','=',$request->value)
                  ->where(function ($query) use ($search){
                    return  $query->where('a.item_name', 'LIKE', "%{$search}%")
                    ->orWhere('a.code', 'LIKE', "%{$search}%")
                      ->orWhere('a.brand', 'LIKE', "%{$search}%");        
                      
              })
              ->offset($start)
              ->limit($limit)
              ->orderBy($order, $dir)
              ->get();
                  break;
                  
                  case 'section':
                         $totalData = DB::table('items as i') 
  
            ->where('i.laboratory_sections_id','=',$request->value)
            ->count();

                  $totalRec = $totalData;
            // $totalData = DB::table('appointments')->count();

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $search = $request->input('search.value');
                       $terms = DB::table('items AS a')
            // ->join('laboratories as l','l.id','=','i.laboratory_id')
  //->join('laboratory_sections as ls','ls.id','=','a.laboratory_sections_id')
  //->select('a.id as Id','a.code','a.brand','a.item_image','a.item_name','a.unit_issue','a.minimum_level','a.maximum_level','ls.section_name')
             ->where('a.laboratory_sections_id','=',$request->value)
                  ->where(function ($query) use ($search){
                    return  $query->where('a.item_name', 'LIKE', "%{$search}%")
                    ->orWhere('a.code', 'LIKE', "%{$search}%")
                      ->orWhere('a.brand', 'LIKE', "%{$search}%");        
                      
              })
              ->offset($start)
              ->limit($limit)
              ->orderBy($order, $dir)
              ->get();      
                      
                      
                      break;

             case "category" :
                 
                     $totalData = DB::table('items as i') 
  
        ->where('i.unit_issue','LIKE',"%{$request->value}%")
            ->count();

                  $totalRec = $totalData;
            // $totalData = DB::table('appointments')->count();

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $search = $request->input('search.value');
               $terms = DB::table('items AS a')
            ->join('laboratories as l','l.id','=','a.laboratory_id')
  //->join('laboratory_sections as ls','ls.id','=','a.laboratory_sections_id')
  //->select('a.id as Id','a.code','a.brand','a.item_image','a.item_name','a.unit_issue','a.minimum_level','a.maximum_level','ls.section_name')
             ->where('a.unit_issue','LIKE',"%{$request->value}%")
                  ->where(function ($query) use ($search){
                    return  $query->where('a.item_name', 'LIKE', "%{$search}%")
                    ->orWhere('a.code', 'LIKE', "%{$search}%")
                      ->orWhere('a.brand', 'LIKE', "%{$search}%");        
                      
              })
              ->offset($start)
              ->limit($limit)
              ->orderBy($order, $dir)
              ->get();    
            }
             

            $totalFiltered =  $totalRec ;
  //  0 => 'id',
          

            $data = array();
            if (!empty($terms)) {
  $x=1;
              foreach ($terms as $term) {
  $c=url('/'). "/public/upload/items/".$term->item_image ;
  $default=url('/')."/assets/icon/not_available.jpg";


                  $nestedData['id']="<input type='checkbox' id='$term->id'  name='check' onclick='selectCheckedItem(this.id)'/>";
                      $nestedData['code']=$term->code;
                  if(empty($term->item_image)){
                        $nestedData['image'] = "<img src='$default' class='img-thumbnail' alt='...' width='50px' height='50px'>";  
                  }
                  else{
                  $nestedData['image'] = "<img src='$c' class='img-thumbnail' alt='...' width='50px' height='50px'>";
                  }
                  $nestedData['brand']= $term->brand??"";
                  $nestedData['name']= $term->item_name??"";
                   $nestedData['warehouse_size']=$term->warehouse_size??"";
                $nestedData['cat_number']=$term->catalog_number??"";
           $nestedData['hazardous']=$term->	is_hazardous;
             $nestedData['storage_temp']=$term->store_temp;
                      $nestedData['unit_issue']=$term->unit_issue;
                        $nestedData['stock_level']= "Minimum: ". $term->minimum_level.' Maximum: '.$term->maximum_level;
    $nestedData['section']= $term->lab_name??'';
                
                        $nestedData['options']="<a href='#' id='$term->id' onclick='EditItem(this.id)' style='color:#3B71CA' > <i class='fa fa-edit title='Edit Item'></i></a>";
                          $nestedData['options'].= "&nbsp  | &nbsp <a href='#' id='$term->id' onclick='deleteItem(this.id)' style='color:red'> <i class='fa fa-trash  title='Delete'></i></a>";
                
    
                  
                
                    $x++;
                  $data[] = $nestedData;
            }
        }

        $json_data = array(
          "draw" => intval($request->input('draw')),
          "recordsTotal" => intval($totalData),
          "recordsFiltered" => intval($totalFiltered),
          "data" => $data
      );

        echo json_encode($json_data);
            
  
}
public function exportItemList(Request $request){
        $name = $request->item_name;
        $code = $request->item_code;
        $labName = $request->item_lab;
        $labSection = $request->item_section;
        $category=$request->item_category;
        

     $query = Item::query();
        if (!empty($name)) {
            $query->where('item_name', 'LIKE', '%' . $name . '%');
        }

        if (!empty($code)) {
            $query->where('code', '=', $code);
        }

        if (!empty($labName)) {
            $query->where('laboratory_id', '=',  $labName );
        }

        if (!empty($labSection)) {
            $query->where('laboratory_sections_id', '=', $labSection);
        }
if(empty($name) && empty($code)&&empty($labName) && empty($labSection) && empty($category)){
    $items=Item::get();
}

else
{
 $items = $query->get();
}
//dd( $consolidated);
if(empty($items) && $items->count()==0){
    return response()->json([
        'message'=>"Items not Found",
        'error'=>false,
        ]);
}
     $spreadsheet = new Spreadsheet();

     $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(100, 'pt');
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$spreadsheet->getProperties()->setCreator('cathebert muyila')
    ->setLastModifiedBy('cathebert muyila')
    ->setTitle('PhpSpreadsheet Table Test Document')
    ->setSubject('PhpSpreadsheet Table Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Table');

// Create the worksheet

  

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->setCellValue('E2','ITEMS LIST');
$spreadsheet->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()
    ->setCellValue('A3', 'ULN NUMBER')
    ->setCellValue('B3', 'CODE')
    ->setCellValue('C3', 'Brand')
    ->setCellValue('D3', 'Item Name')
     ->setCellValue('E3', 'Warehouse Size')
    ->setCellValue('F3', 'Catalog Number')
    ->setCellValue('G3', 'Description')
    ->setCellValue('H3', 'Minimum Level')
    ->setCellValue('I3', 'Maximum Level')
    ->setCellValue('J3', 'Laboratory')
     ->setCellValue('K3', 'Section')
     ->setCellValue('L3', 'Unit Issue')
     ->setCellValue('M3', 'Hazardous')
    ->setCellValue('N3', 'Storage Temp');

$num=4;
  
    foreach($items as $item){
$lab=Laboratory::where('id',$item->laboratory_id)->select('lab_name')->first();
$section=LaboratorySection::where('id',$item->laboratory_sections_id)->select('section_name')->first();
if($lab){
    $lab_name=$lab->lab_name;
}
else{
    $lab_name="";
}
if($section){
    $section_name=$section->section_name;
}
else{
    $section_name='';
}

 
 $data=[

    [

    $item->uln,
    $item->code, 
    $item->brand,
    $item->item_name,
    $item->warehouse_size,
    $item->catalog_number,
   
    $item->item_description,
    $item->minimum_level,
    
    $item->maximum_level,
    $lab_name,
    $section_name,
    $item->unit_issue,
    $item->is_hazardous,
    $item->store_temp
  ]
  ];
 $spreadsheet->getActiveSheet()->fromArray($data, null, 'A'.$num);
$num++;
}


$step=$num+1;
//$spreadsheet->getActiveSheet()->fromArray($data, null, 'A2');
/*$spreadsheet->getActiveSheet()
    ->setCellValue('A'.$step, 'Total');
  $spreadsheet->getActiveSheet()->getStyle('A'.$step)->getFont()->setBold(true);
    $spreadsheet->getActiveSheet()
    ->setCellValue('H'.$step, $overall_total);
    $spreadsheet->getActiveSheet()->getStyle('H'.$step)->getNumberFormat()
    ->setFormatCode('#,##0.00');
    $spreadsheet->getActiveSheet()->getStyle('H'.$step)->getFont()->setBold(true);*/

// Create Table

$table = new Table('A3:N'.$num, 'Exported');

// Create Columns

// Create Table Style

$tableStyle = new TableStyle();
$tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM2);
$tableStyle->setShowRowStripes(true);
$tableStyle->setShowColumnStripes(true);
$tableStyle->setShowFirstColumn(true);
$tableStyle->setShowLastColumn(true);
$table->setStyle($tableStyle);

// Add Table to Worksheet

$spreadsheet->getActiveSheet()->addTable($table);



// Save

$writer = new Xlsx($spreadsheet);
$name="items_list.xlsx";
$writer->save(public_path('reports').'/'.$name);
 $path=public_path('reports').'/'.$name;
 $url=route('download_item_file',['name'=>$name]) ; 


return response()->json([
   
    'path'=>$url,
    ]);

}


public function downloadItems($name){
 $path=public_path('reports').'/'.$name;
$name='items_list.xlsx';

$headers = [
  'Content-type' => 'application/pdf', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

]; 


$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

]; 
//Update Requsition status
//Requisition::where('is_marked','yes')->update([
  //'is_marked'=>'done',
//]);

return response()->download($path,$name, $headers);


}
private function generateGRNReceipt($grn){
    
$infor=ReceivedItem::where('grn_number',$grn)->select('lab_id','section_id','receiving_date','received_by','supplier_id','receiver_id','po_reference')->first();
//dd($infor);
if($infor->receiver_id!=NULL){
$data['signature']=User::find($infor->receiver_id)->signature;
}
else{
   $data['signature']='';
}
$data['supplier']=Supplier::where('id',$infor->supplier_id)->select('supplier_name','address','email','phone_number')->first();
$lab=Laboratory::where('id',$infor->lab_id)->select('lab_name')->first();
if($infor->section_id!=0){
   $section=LaboratorySection::where('id',$infor->section_id)->select('section_name')->first();
   $sec=$section->section_name??'';
   $data['lab']=$lab->lab_name.' | '.$sec;
}

$data['lab']=$lab->lab_name;
$data['po_ref']=$infor->po_reference;
$data['received_date']=date('d, M Y',strtotime($infor->receiving_date));


   $data['received_by']=$infor->received_by??"N/A";

 $data['info']= DB::table('items as t') 
              ->join('inventories AS i', 'i.item_id', '=', 't.id')
              ->select('i.id as id','i.lab_id','t.item_name','t.unit_issue','i.quantity','i.batch_number','i.cost')
              ->where([['i.grn_number','=',$grn],['i.lab_id','=',auth()->user()->laboratory_id]])
               ->groupBy('t.item_name')
              ->get(); 
              if($request->action=="download"){
 $pdf=PDF::loadView('pdf.goods_receiving_note',$data);
                return $pdf->download('GRN_'.$grn.'.pdf'); 
}   
}
public function deleteItemDetails(Request $request){
  
ItemTemp::where('id',$request->id)->delete();

return response()->json([
    'message'=>'Removed',
    'error'=>false
]);
  
}
public function deleteAllEntries(){
 ItemTemp::where([['user_id','=',auth()->user()->id],['is_saved','=','no']])->delete();

return response()->json([
    'message'=>'Removed All entries',
    'error'=>false
]);
   
}
}
