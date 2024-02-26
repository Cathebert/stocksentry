<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Laboratory;
use App\Models\LaboratorySection;
use App\Models\Inventory;
use App\Models\Issue;
use App\Models\IssueDetails;
use App\Models\User;
use App\Models\ConsumptionDetail;
use App\Models\ItemOrder;
use App\Services\UpdateInventoryService;
use App\Services\AcceptIssueService;
use Validator;
use DB;

use PDF;
use App\Models\Setting;

class LabInventoryController extends Controller
{
    //

    public function showLabBinCard(){
         $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id]])->select('id','name','last_name')->get();
        $data['area']=LaboratorySection::select('id','section_name')->get();
        $data['items']= DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
          ->select('t.id as id','s.item_name')
             ->where('t.lab_id','=',auth()->user()->laboratory_id);
         
      $data['items']= DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
          ->select('t.id as id','s.item_name')
          ->groupBy('s.id')
             ->where('t.lab_id','=',auth()->user()->laboratory_id)
          ->paginate(15);
   $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
            $data['has_section']=$lab->has_section;
        if($lab->has_section=='yes'){
           $data['lab_sections']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->count();
            $data['sections']=DB::table('lab_sections as l')
                              ->join('laboratory_sections as s','s.id','=','l.section_id')
                              ->select('s.id','s.section_name')
                              ->where('l.lab_id',auth()->user()->laboratory_id)->get();

        }
            
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        return view('provider.inventory.inventory_tabs.lab_bincard', $data);
    }

 public function showLabStockTake(){
      $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
            $data['has_section']=$lab->has_section;
        if($lab->has_section=='yes'){
           $data['lab_sections']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->count();
            $data['sections']=DB::table('lab_sections as l')
                              ->join('laboratory_sections as s','s.id','=','l.section_id')
                              ->select('s.id','s.section_name')
                              ->where('l.lab_id',auth()->user()->laboratory_id)->get();

        }

   $data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id]])->select('id','name','last_name')->get();
        $data['area']=LaboratorySection::select('id','section_name')->get();
        $data['count']=Inventory::where('lab_id',auth()->user()->laboratory_id)->count(); 
        
            
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        return view('provider.inventory.inventory_tabs.lab_stocktake',$data);
    }

    public function showLabConsumption(){
          $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
            $data['has_section']=$lab->has_section;
        if($lab->has_section=='yes'){
           $data['lab_sections']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->count();
            $data['sections']=DB::table('lab_sections as l')
                              ->join('laboratory_sections as s','s.id','=','l.section_id')
                              ->select('s.id','s.section_name')
                              ->where('l.lab_id',auth()->user()->laboratory_id)->get();

        }
         $data['items']= DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
          ->select('t.id as id','s.item_name')
             ->where('t.lab_id','=',auth()->user()->laboratory_id);
             
                 
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
             
        return view('provider.inventory.inventory_tabs.lab_consumption',$data);
    }

    public function showLabStockForecasting(){
           $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
            $data['has_section']=$lab->has_section;
        if($lab->has_section=='yes'){
           $data['lab_sections']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->count();
            $data['sections']=DB::table('lab_sections as l')
                              ->join('laboratory_sections as s','s.id','=','l.section_id')
                              ->select('s.id','s.section_name')
                              ->where('l.lab_id',auth()->user()->laboratory_id)->get();

        }
      $order=ItemOrder::select('id')->latest('id')->first();

  $settings=Setting::find(1);
  if($order==NULL){
          $data['order']=$settings->order_prefix.'0001';
         }
         else{
          $number=str_pad($order->id+1, 4, '0', STR_PAD_LEFT);
        
          $data['order']=$settings->order_prefix.''.$number;
         }
         
         
         
             
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        return view('provider.inventory.inventory_tabs.lab_forecasting',$data);
    }
    public function getSelectedStockLocation(Request $request){

      $columns = array(
            0 =>'id',
            1=>'code',
            2=>'batch_number',
            3=>'brand',
            4=>'name',
            5=>'unit',
            6=>'consumed',
           
        ); 
      switch($request->id){
        case 0:
  $totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.quantity','t.batch_number','t.cost','s.item_name','t.expiry_date')
          ->where('t.lab_id',auth()->user()->laboratory_id)
           // ->where('t.expiry_date', '>', date('Y-m-d') )
          ->count();

           $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.item_id','t.quantity','t.batch_number','t.cost','s.unit_issue','s.item_name','t.expiry_date')
         ->where('t.lab_id',auth()->user()->laboratory_id)
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.brand','LIKE',"%{$search}%")
                   ->orWhere('t.batch_number','LIKE',"%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%") ;
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.expiry_date','asc')
            ->get();

          $totalFiltered =  $totalRec ;
        break;

    case 1:
$totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.quantity','t.batch_number','t.cost','s.item_name','t.expiry_date')
          ->where([['t.lab_id','=',auth()->user()->laboratory_id],['t.storage_location','=',$request->id]])
           // ->where('t.expiry_date', '>', date('Y-m-d') )
          ->count();

           $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.item_id','t.quantity','t.batch_number','t.cost','s.unit_issue','s.item_name','t.expiry_date')
         ->where([['t.lab_id','='.auth()->user()->laboratory_id],['t.storage_location','=',$request->id]])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.brand','LIKE',"%{$search}%")
                   ->orWhere('t.batch_number','LIKE',"%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%") ;
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.expiry_date','asc')
            ->get();

          $totalFiltered =  $totalRec ;
      break;

      case 2:
$totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.quantity','t.batch_number','t.cost','s.item_name','t.expiry_date')
          ->where('t.lab_id',auth()->user()->laboratory_id)
          ->where('t.storage_location',$request->id)
           // ->where('t.expiry_date', '>', date('Y-m-d') )
          ->count();

           $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.item_id','t.quantity','t.batch_number','t.cost','s.unit_issue','s.item_name','t.expiry_date')
        ->where('t.lab_id',auth()->user()->laboratory_id)
          ->where('t.storage_location',$request->id)
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.brand','LIKE',"%{$search}%")
                   ->orWhere('t.batch_number','LIKE',"%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%") ;
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.expiry_date','asc')
            ->get();

          $totalFiltered =  $totalRec ;

      break;



      case 3:
$totalData = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.quantity','t.batch_number','t.cost','s.item_name','t.expiry_date')
          ->where([['t.lab_id','=',auth()->user()->laboratory_id],['t.storage_location','=',$request->id]])
           // ->where('t.expiry_date', '>', date('Y-m-d') )
          ->count();

           $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = DB::table('inventories as t') 
              ->join('items AS s', 's.id', '=', 't.item_id')
              ->select('t.id as id','s.code','s.brand','s.item_description','t.item_id','t.quantity','t.batch_number','t.cost','s.unit_issue','s.item_name','t.expiry_date')
         ->where([['t.lab_id','='.auth()->user()->laboratory_id],['t.section_id','=',auth()->user()->section_id],['t.storage_location','=',$request->id]])
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('s.code', 'LIKE', "%{$search}%")
                  ->orWhere('s.brand','LIKE',"%{$search}%")
                   ->orWhere('t.batch_number','LIKE',"%{$search}%")
                  ->orWhere('s.item_name','LIKE',"%{$search}%") ;
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('t.expiry_date','asc')
            ->get();

          $totalFiltered =  $totalRec ;

      break;


      }
 

         
    
          $data = array();
          if (!empty($terms)) {
$x=1;
  

            foreach ($terms as $term) {


 $nestedData['item_id']=$term->id;

                $nestedData['id']="<input type='checkbox' id='se_$term->id' name='selected_check' onclick='AddIdToList(this.id)'/>";
                  $nestedData['batch_number']=$term->batch_number;
                    $nestedData['brand']= $term->brand;
                $nestedData['code']=$term->code;
             
                 $nestedData['name']= $term->item_name;
                  $nestedData['unit']= $term->unit_issue;
               
                 $nestedData['consumed'] = "<input type='number'  size='5' id='s_$term->id' min='0' class='form-control' placeholder='Enter Here' name='$term->id' onchange='getPhysicalCount(this.id,this.name)'/>";
                 
                   
     
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
    public function showLabAdjustment(){
          $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
            $data['has_section']=$lab->has_section;
        if($lab->has_section=='yes'){
           $data['lab_sections']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->count();
            $data['sections']=DB::table('lab_sections as l')
                              ->join('laboratory_sections as s','s.id','=','l.section_id')
                              ->select('s.id','s.section_name')
                              ->where('l.lab_id',auth()->user()->laboratory_id)->get();

        }
        
           
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
         return view('provider.inventory.inventory_tabs.lab_adjustment',$data);

    }

    public function showLabDisposal(){
         $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
            $data['has_section']=$lab->has_section;
        if($lab->has_section=='yes'){
           $data['lab_sections']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->count();
            $data['sections']=DB::table('lab_sections as l')
                              ->join('laboratory_sections as s','s.id','=','l.section_id')
                              ->select('s.id','s.section_name')
                              ->where('l.lab_id',auth()->user()->laboratory_id)->get();

        }
        
            
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
  return view('provider.inventory.inventory_tabs.lab_disposal',$data);

    }


    public function itemList(Request $request){
         $data['suppliers']=Supplier::select('id','supplier_name')->get();
        $data['laboratory']=Laboratory::select('id','lab_name')->get();
         $data['sections']=DB::table('laboratory_sections')->select('id','section_name')->get();
 $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
            $data['has_section']=$lab->has_section;
        if($lab->has_section=='yes'){
           $data['lab_sections']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->count();
            $data['sections']=DB::table('lab_sections as l')
                              ->join('laboratory_sections as s','s.id','=','l.section_id')
                              ->select('s.id','s.section_name')
                              ->where('l.lab_id',auth()->user()->laboratory_id)->get();

        }
             
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}

if(auth()->user()->authority==2){
  return view('items.mod_items',$data);
}
    }
    public function loadItemList(Request $request){
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
            // ->join('laboratories as l','l.id','=','i.laboratory_id')
  //->join('laboratory_sections as ls','ls.id','=','a.laboratory_sections_id')
  //->select('a.id as Id','a.code','a.brand','a.item_image','a.item_name','a.unit_issue','a.minimum_level','a.maximum_level','ls.section_name')
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

  $section=LaboratorySection::where('id',$term->laboratory_sections_id)->first();

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
           $nestedData['hazardous']=$term-> is_hazardous;
             $nestedData['storage_temp']=$term->store_temp;
                      $nestedData['unit_issue']=$term->unit_issue;
                        $nestedData['stock_level']= "Minimum: ". $term->minimum_level;
    $nestedData['section']= $section->section_name??"";
                
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

public function labcreateNewItem(){
        $data['suppliers']=Supplier::select('id','supplier_name')->get();
        $data['laboratory']=Laboratory::select('id','lab_name')->get();
         $data['sections']=DB::table('laboratory_sections')->select('id','section_name')->get();
              $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
        if($lab->has_section=='yes'){
           $data['lab_sections']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->count();
        }
        
$uln=Item::select('uln')->latest()->take(1)->first();

  $settings=Setting::find(1);

         if($uln->uln==NULL){
          $data['uln']=$settings->uln_prefix.'0001';
         }
         else{
          $number=str_pad($uln->uln+1, 4, '0', STR_PAD_LEFT);
        
          $data['uln']=$settings->uln_prefix.''.$number;
         }
         
            
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
    return view('provider.items.lab_create',$data);
    }

     public function receiveInventory(Request $request){
          $data['suppliers']=Supplier::select('id','supplier_name')->get();
           $data['laboratories']=Laboratory::select('id','lab_name')->where('id',auth()->user()->laboratory_id)->get();
          // $data['sections']=LaboratorySection::select('id','section_name')->get();
            $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
            $data['has_section']=$lab->has_section;
        if($lab->has_section=='yes'){
           $data['lab_sections']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->count();
            $data['sections']=DB::table('lab_sections as l')
                              ->join('laboratory_sections as s','s.id','=','l.section_id')
                              ->select('s.id','s.section_name')
                              ->where('l.lab_id',auth()->user()->laboratory_id)->get();

        }
        
            
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        return view('provider.receive.receive_tabs.new_receipt',$data);
    }
      public function allReceivedInventory(Request $request){
          $data['suppliers']=Supplier::select('id','supplier_name')->get();
           $data['laboratories']=Laboratory::select('id','lab_name')->where('id',auth()->user()->laboratory_id)->get();
           $data['sections']=LaboratorySection::select('id','section_name')->get();

                $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
        if($lab->has_section=='yes'){
           $data['lab_sections']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->count();
        }
        
            
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        
        return view('provider.receive.receive_tabs.lab_received_receipts',$data);
    }
}
