<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Contract;
use App\Models\Laboratory;
use App\Models\User;
use App\Models\ContractHistory;
use App\Models\ContractUser;
use DB;
use PDF;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class ContractController extends Controller
{
    //

    public function add(){
        $data['suppliers']=Supplier::all();
        $data['subscriptions']=DB::table('subscription_categories')->get();

        $data['contracts']=DB::table('contracts as c')
                                    ->join('suppliers as s','s.id','=','c.supplier_id')
                                    ->get();
             $data['users']=User::where('laboratory_id',auth()->user()->laboratory_id)->get();
             $labs=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
             $data['lab_name']='Logged Into: '.$labs->lab_name;
        return view('contracts.show',$data);
    }
    public function load(Request $request){
        $date=date('Y-m-d');
        $columns= array(
    0 => 'id', 
    1=>'contract_number',
    2=>'contract_name',
    3=>'contract_desc',
    4=>'contract_start',
    5=>'contract_end',
    6=>'sub_type',

    7=>'supplier',
    8=>'status',
    9=>'action',
    10=>'frequency',
    11=>'contract_unit',
    12=>'delete'
    );
   
   $totalData = DB::table('contracts as c')
                                   
                                    ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');


            $terms =DB::table('contracts as c')
                                    ->select(
                                        'c.id as id',
                                        'c.contract_number',
                                        'c.contract_name',
                                        'c.contract_description',
                                        'c.contract_startdate',
                                        'c.contract_enddate',
                                        'c.contract_type',
                                        'c.frequency',
                                        'c.contract_unit',
                                        'c.supplier_id',
                                        'c.contractor_name'
                                       
                                )
                ->where(function ($query) use ($search){
                  return  $query->where('contract_number', 'LIKE', "%{$search}%")
                  ->orWhere('contract_name','LIKE',"%{$search}%");
                      
                     
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
            if($term->supplier_id!=NULL){
    $supplier=Supplier::where('id',$term->supplier_id)->select('supplier_name')->withTrashed()->first();
     $supplier_name=$supplier->supplier_name;
}
else if ($term->contractor_name!=NULL){
  $supplier_name=$term->contractor_name;
}
else{
$supplier_name="N/A";
}

$download=route('contract.download',['id'=>$term->id]);
$timeZone = 'Africa/Blantyre';
  
$expiryDate = Carbon::parse($term->contract_enddate);
$currentDate = Carbon::now($timeZone);
$expiryThreshold = $currentDate->copy()->addDays(90);

             $nestedData['id']=$x;
                $nestedData['contract_number']=$term->contract_number;
             
                 $nestedData['contract_name']= $term->contract_name;
                
                   
                 $nestedData['contract_start'] = date('d,M Y',strtotime($term->contract_startdate));
                 $nestedData['contract_end']  =date('d,M Y',strtotime($term->contract_enddate));
               $nestedData['contract_desc']= $term->contract_description;
                   
            $nestedData['contract_start'] = date('d,M Y',strtotime($term->contract_startdate));
            $nestedData['frequency']=$term->frequency;
             if ($term->contract_unit==1) {
               $nestedData['contract_unit']="Month";
                
             }
             else{
                $nestedData['contract_unit']="Year";
             }
                   
                
            $nestedData['contract_end']=date('d,M Y',strtotime($term->contract_enddate));
                 if($term->contract_type==1){
                     $nestedData['sub_type']='Supplier';
                 }
                 else{
                    $nestedData['sub_type']='Service';
                 }
                 
                  $nestedData['supplier']  = $supplier_name;
                 
                  

if ($currentDate->lt($expiryDate)) {
  $nestedData['status']  ="<span class='badge badge-success'>Good</span>";
   $nestedData['action']= "<a class='btn btn-info btn-sm' id='$term->id' onclick='viewContract(this.id)'><i class='fa fa-eye'></i> </a> ";
   $nestedData['delete']=" <a class='btn btn-primary btn-sm' id='$term->id' onclick='editContract(this.id)'><i class='fa fa-edit' aria-hidden='true'></i></a>";
} 
if ($expiryDate->lt($expiryThreshold)) {

     $nestedData['status']  ="<span class='badge badge-warning'>warning</span>";
      $nestedData['action'] = "<a class='btn btn-info btn-sm' id='$term->id' onclick='viewContract(this.id)'><i class='fa fa-eye'></i> </a> | <a class='btn btn-success btn-sm' id='$term->id' onclick='updateContract(this.id)'><i class='fa fa-refresh' aria-hidden='true'></i></a>  ";
      $nestedData['delete']=" <a class='btn btn-primary btn-sm' id='$term->id' onclick='editContract(this.id)'><i class='fa fa-edit' aria-hidden='true'></i></a> | <a class='btn btn-danger btn-sm' id='$term->id' onclick='deleteContract(this.id)'><i class='fa fa-trash'></i> </a>";
} 
        
if ($currentDate->gt($expiryDate)) {
     $nestedData['status']  ="<span class='badge badge-danger'>Expired</span>";
     $nestedData['action'] = "<a class='btn btn-info btn-sm' id='$term->id' onclick='viewContract(this.id)'><i class='fa fa-eye'></i></a> | <a class='btn btn-success btn-sm' id='$term->id' onclick='updateContract(this.id)'><i class='fa fa-refresh' aria-hidden='true'></i></a> ";
     $nestedData['delete']="<a class='btn btn-primary btn-sm' id='$term->id' onclick='editContract(this.id)'><i class='fa fa-edit' aria-hidden='true'></i></a> | <a class='btn btn-danger btn-sm' id='$term->id' onclick='deleteContract(this.id)'><i class='fa fa-trash'></i> </a>";
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
    public function showModal(){
          $data['suppliers']=Supplier::all();
        $data['subscriptions']=DB::table('subscription_categories')->get();
          $data['users']=User::where('laboratory_id',auth()->user()->laboratory_id)->get();
        return view('inventory.modal.contract_modal',$data);
    }

    public function saveContract(Request $request){

        $validatedData=$request->validate([
            'contract_name'=>'required',
            'contract_number'=>'required',
            'contract_startdate'=>'required',
            'contract_enddate'=>'required'
        ]);
        try{
            DB::beginTransaction();
      $contract=new Contract();
       $contract->contract_number=$request->contract_number;
       $contract->contract_name=$request->contract_name;
       $contract->contract_description=$request->contract_desc;
       $contract->contract_startdate=$request->contract_startdate;
       $contract->contract_enddate=$request->contract_enddate;
       $contract->frequency=$request->contract_frequency;
       $contract->contract_unit=$request->cont_unit;
       $contract->contract_type=$request->cont_type;
       if($request->supplier!=NULL){
       $contract->supplier_id=$request->supplier;
   }
   else{
     $contract->supplier_id=NULL;
   }
   if($request->contractor_name!=NULL){
    $contract->contractor_name=$request->contractor_name;
   }
   else{
    $contract->contractor_name=NULL;
   }
       $contract->created_at=now();
       $contract->updated_at=NULL;
       $contract->save();
 $id=$contract->id;
$history=new ContractHistory();
$history->contract_id=$id;
$history->start_date=$request->contract_startdate;
$history->end_date=$request->contract_enddate;
$history->updated_by=auth()->user()->id;
$history->created_at=now();
$history->updated_at=NULL;
$history->save();
if($request->supplier!=NULL){
Supplier::where('id',$request->supplier)->update([
'contract_expiry'=>$request->contract_enddate,
]);
}
 if (!empty($request->employee_involved) && count($request->employee_involved)>0) {
                    for ($x=0; $x<count($request->employee_involved);$x++) {
                    $userContract=new ContractUser();
                    $userContract->contract_id=$id;
                    $userContract->user_id=$request->employee_involved[$x];
                    $userContract->created_at=now();
                    $userContract->updated_at=NULL;
                    
                     $userContract->save();
                    }
                    
                }
DB::commit();
       return back()->with('success',"Contract created Successfully");
}
catch(Exception $e){
    DB::rollback();
 return back()->with('error',"Failed to create Contract");
}
    }

    public function saveSubscriptionType(Request $request)
    {
        parse_str($request->sub_form,$out);
  $filtered=$out;
 
      // dd($filtered['sub_name']);

       DB::table('subscription_categories')->insert([
    'name' => $filtered['sub_name'],
     'description' => $filtered['sub_description']
    ]);

       return response()->json([
        'message'=>"done",
        'error'=>false
       ]);
    }

    public function viewContract(Request $request){
        $data['contract']=DB::table('contracts as c')
                                   
                                   
                                    ->select(
                                        'c.id as id',
                                        'c.contract_number',
                                        'c.contract_name',
                                        'c.contract_description',
                                        'c.contract_startdate',
                                        'c.contract_enddate',
                                        'c.contract_type',
                                        'c.frequency',
                                        'c.contract_unit',
                                        'c.supplier_id'
                                )->where('c.id',$request->id)->first();
if($data['contract']->supplier_id!=NULL){
$supplier=Supplier::where('id',$data['contract']->supplier_id)->select('supplier_name')->withTrashed()->first();
$data['supplier']=$supplier->supplier_name;
}else{
$data['supplier']=NULL;
}
            $data['contract_details']=DB::table('contract_histories as h')
                                            ->join('users as u','u.id','=','h.updated_by')
                                            ->where('h.contract_id',$request->id)
                                            ->get();
            return view('inventory.modal.view_contract',$data);
    }

    public function updateContract(Request $request){
 $data['contract']=DB::table('contracts as c')
                                  
                                    ->select(
                                       'c.id as id',
                                        'c.contract_number',
                                        'c.contract_name',
                                        'c.contract_description',
                                        'c.contract_startdate',
                                        'c.contract_enddate',
                                        'c.contract_type',
                                        'c.frequency',
                                        'c.contract_unit',
                                        'c.supplier_id'
                                )->where('c.id',$request->id)->first();
    $data['id']=$request->id;
if($data['contract']->supplier_id!=NULL){
$supplier=Supplier::where('id',$data['contract']->supplier_id)->select('supplier_name')->withTrashed()->first();
$data['supplier']=$supplier->supplier_name;
}else{
$data['supplier']=NULL;
}
            return view('inventory.modal.update_contract',$data);

    }
    
        public function editContract(Request $request){
         $data['suppliers']=Supplier::all();
        $data['subscriptions']=DB::table('subscription_categories')->get();
          $data['users']=User::where('laboratory_id',auth()->user()->laboratory_id)->get();
 $data['contract']=DB::table('contracts as c')
                                  
                                    ->select(
                                       'c.id as id',
                                        'c.contract_number',
                                        'c.contract_name',
                                        'c.contract_description',
                                        'c.contract_startdate',
                                        'c.contract_enddate',
                                        'c.contract_type',
                                        'c.frequency',
                                        'c.contract_unit',
                                        'c.supplier_id',
                                        'contractor_name'
                                )->where('c.id',$request->id)->first();
    $data['id']=$request->id;
if($data['contract']->supplier_id!=NULL){
$supplier=Supplier::where('id',$data['contract']->supplier_id)->select('supplier_name')->withTrashed()->first();
$data['supplier']=$supplier->supplier_name;
}else{
$data['supplier']=NULL;
}
  $notified = array();

$receipts=ContractUser::where('contract_id',$request->id)->select('user_id')->get();
foreach($receipts as $user){
$notified[]=$user->user_id;
}

$data['receipients']= $notified;
            return view('inventory.modal.edit_contract',$data);

    }
    public function saveEditContract(Request $request){

    $id=$request->contract_edit_id;
    if($request->supplier_edit==0){
    $supplier_id=NULL;
    }
    else{
    $supplier_id=$request->supplier_edit;
    }
    if($request->contractor_name_edit==NULL && $request->service_name!=NULL){
    $contract=$request->service_name;
    
    } 
    if($request->contractor_name_edit==NULL && $request->service_name==NULL){
    $contract=NULL;
    }
    if($request->contractor_name_edit!=NULL && $request->service_name!=NULL){
     $contract=$request->contractor_name_edit;
    }
     $validatedData=$request->validate([
            'contract_name'=>'required',
            'contract_number_edit'=>'required',
            'contract_startdate_edit'=>'required',
            'contract_enddate_edit'=>'required'
        ]);
      Contract::where('id',$id)->update([
       'contract_name'=>$request->contract_name,
            'contract_number'=>$request->contract_number_edit,
            'contract_description'=>$request->contract_desc_edit,
            'frequency'=>$request->contract_frequency_edit,
            'contract_startdate'=>$request->contract_startdate_edit,
            'contract_enddate'=>$request->contract_enddate_edit,
            'contract_unit'=>$request->cont_unit_edit,
            'contract_type'=>$request->cont_type_edit,
            'contractor_name'=>$contract,
            'supplier_id'=>$supplier_id,
      ]);
   ContractUser::where('contract_id',$id)->delete();
   //update notification receipients
    if (!empty($request->employee_involved) && count($request->employee_involved)>0) {
                    for ($x=0; $x<count($request->employee_involved);$x++) {
                    $userContract=new ContractUser();
                    $userContract->contract_id=$id;
                    $userContract->user_id=$request->employee_involved[$x];
                    $userContract->created_at=now();
                    $userContract->updated_at=NULL;
                    
                     $userContract->save();
                    }
                    
                }
        return back()->with('success',"Contract edited Successfully");
   
    }

    public function keepupContract(Request $request){

        Contract::where('id',$request->contract_id)->update([
           'contract_name'=>$request->contract_name,
            'contract_number'=>$request->contract_number,
            'contract_description'=>$request->contract_desc,
            'frequency'=>$request->contract_frequency,
            'contract_startdate'=>$request->contract_enddate,
            'contract_enddate'=>$request->contract_nextdate,
            'contract_unit'=>$request->cont_unit
        ]);
        
        Supplier::where('id',$request->supplier_id)->update([
'contract_expiry'=>$request->contract_enddate,
]);
        $history= new ContractHistory();
        $history->contract_id= $request->contract_id;
        $history->start_date=$request->contract_enddate;
        $history->end_date=$request->contract_nextdate;
        $history->updated_by=auth()->user()->id;

        $history->save();

        return back()->with('success',"Updated Successfully");
    }
public function deleteContract(Request $request){

ContractHistory::where('contract_id',$request->id)->delete();
Contract::find($request->id)->delete();
 return response()->json([
                    'message'=>'Contract deleted successfully.',
                    'error'=>false
                ]);
}
public function filterContract(Request $request){
   
 $columns= array(
       0 => 'id', 
    1=>'contract_number',
    2=>'contract_name',
    3=>'contract_desc',
    4=>'contract_start',
    5=>'contract_end',
    6=>'sub_type',

    7=>'supplier',
    8=>'status',
    9=>'action',
    10=>'frequency',
    11=>'contract_unit',
    12=>'delete'
    );
   switch($request->type){
    case 'number':
   $totalData = DB::table('contracts as c')
                                    
                                    ->where('c.contract_number','LIKE',"%{$request->value}%")
                                    ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');


              $terms =DB::table('contracts as c')
                                    ->select(
                                        'c.id as id',
                                        'c.contract_number',
                                        'c.contract_name',
                                        'c.contract_description',
                                        'c.contract_startdate',
                                        'c.contract_enddate',
                                        'c.contract_type',
                                        'c.frequency',
                                        'c.contract_unit',
                                        'c.supplier_id',
                                        'c.contractor_name'
                                )->where('c.contract_number','LIKE',"%{$request->value}%")
                ->where(function ($query) use ($search){
                  return  $query->where('contract_number', 'LIKE', "%{$search}%")
                  ->orWhere('contract_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
        
            ->limit($limit)
            ->orderBy('id','desc')
            ->get(); 

            break;
    case 'name':

    $totalData = DB::table('contracts as c')
                          
                                    ->where('c.contract_name','LIKE',"%{$request->value}%")
                                    ->count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');


             $terms =DB::table('contracts as c')
                                    ->select(
                                        'c.id as id',
                                        'c.contract_number',
                                        'c.contract_name',
                                        'c.contract_description',
                                        'c.contract_startdate',
                                        'c.contract_enddate',
                                        'c.contract_type',
                                        'c.frequency',
                                        'c.contract_unit',
                                        'c.supplier_id',
                                        'c.contractor_name'
                                )->where('c.contract_name','LIKE',"%{$request->value}%")
                ->where(function ($query) use ($search){
                  return  $query->where('contract_number', 'LIKE', "%{$search}%")
                  ->orWhere('contract_name','LIKE',"%{$search}%");
                      
                     
            })
            ->offset($start)
        
            ->limit($limit)
            ->orderBy('id','desc')
            ->get(); 
            
            break; 

}
              $totalFiltered =  $totalRec ;

          $data = array();
          if (!empty($terms)) {
$x=1;
 
            foreach ($terms as $term) {

if($term->supplier_id!=NULL){
    $supplier=Supplier::where('id',$term->supplier_id)->select('supplier_name')->withTrashed()->first();
     $supplier_name=$supplier->supplier_name;
}
else if ($term->contractor_name!=NULL){
  $supplier_name=$term->contractor_name;
}
else{
$supplier_name="N/A";
}


$timeZone = 'Africa/Blantyre';
  
$expiryDate = Carbon::parse($term->contract_enddate);
$currentDate = Carbon::now($timeZone);
$expiryThreshold = $currentDate->copy()->addDays(30);

             $nestedData['id']=$x;
                $nestedData['contract_number']=$term->contract_number;
             
                 $nestedData['contract_name']= $term->contract_name;
                
                   
                 $nestedData['contract_start'] = date('d,M Y',strtotime($term->contract_startdate));
                 $nestedData['contract_end']  =date('d,M Y',strtotime($term->contract_enddate));
               $nestedData['contract_desc']= $term->contract_description;
                   
            $nestedData['contract_start'] = date('d,M Y',strtotime($term->contract_startdate));
            $nestedData['frequency']=$term->frequency;
             if ($term->contract_unit==1) {
               $nestedData['contract_unit']="Month";
                
             }
             else{
                $nestedData['contract_unit']="Year";
             }
                   
                
            $nestedData['contract_end']=date('d,M Y',strtotime($term->contract_enddate));
                 if($term->contract_type==1){
                     $nestedData['sub_type']='Supplier';
                 }
                 else{
                    $nestedData['sub_type']='Service';
                 }
                 
                  $nestedData['supplier']  = $supplier_name;
                 
                  

if ($currentDate->lt($expiryDate)) {
  $nestedData['status']  ="<span class='badge badge-success'>Good</span>";
   $nestedData['action']= "<a class='btn btn-info btn-sm' id='$term->id' onclick='viewContract(this.id)'><i class='fa fa-eye'></i> </a>";
   $nestedData['delete']="| <a class='btn btn-primary  btn-sm' id='$term->id' onclick='editContract(this.id)'><i class='fa fa-edit' aria-hidden='true'></i></a> ";
} 
if ($expiryDate->lt($expiryThreshold)) {

     $nestedData['status']  ="<span class='badge badge-warning'>warning</span>";
      $nestedData['action'] = "<a class='btn btn-info btn-sm' id='$term->id' onclick='viewContract(this.id)'><i class='fa fa-eye'></i> </a> | <a class='btn btn-success btn-sm' id='$term->id' onclick='updateContract(this.id)'><i class='fa fa-refresh' aria-hidden='true'></i></a> ";
      $nestedData['delete']="| <a class='btn btn-primary  btn-sm' id='$term->id' onclick='editContract(this.id)'><i class='fa fa-edit' aria-hidden='true'></i></a> | <a class='btn btn-danger btn-sm' id='$term->id' onclick='deleteContract(this.id)'><i class='fa fa-trash'></i> </a>";
} 
        
if ($currentDate->gt($expiryDate)) {
     $nestedData['status']  ="<span class='badge badge-danger'>Expired</span>";
     $nestedData['action'] = "<a class='btn btn-info btn-sm' id='$term->id' onclick='viewContract(this.id)'><i class='fa fa-eye'></i></a> | <a class='btn btn-success btn-sm' id='$term->id' onclick='updateContract(this.id)'><i class='fa fa-refresh' aria-hidden='true'></i></a>  ";
     $nestedData['delete']="| <a class='btn btn-primary  btn-sm' id='$term->id' onclick='editContract(this.id)'><i class='fa fa-edit' aria-hidden='true'></i></a> | <a class='btn btn-danger btn-sm' id='$term->id' onclick='deleteContract(this.id)'><i class='fa fa-trash'></i> </a>";
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
public function downloadContract(Request $request){
$data['contracts']=DB::table('contracts as c')
->leftjoin('suppliers as s','s.id','=','c.supplier_id')
                                    ->select(
                                        'c.id as id',
                                        'c.contract_number',
                                        'c.contract_name',
                                        'c.contract_description',
                                        'c.contract_startdate',
                                        'c.contract_enddate',
                                        'c.contract_type',
                                        'c.frequency',
                                        'c.contract_unit',
                                        'c.supplier_id',
                                        's.supplier_name',
                                        'c.contractor_name'
                                       )->get();
	 $pdf=PDF::loadView('pdf.contract',$data);		
return $pdf->download('contracts.pdf');
}
public function downloadContractExcel(Request $request){
$contracts=DB::table('contracts as c')
->leftjoin('suppliers as s','s.id','=','c.supplier_id')
                                    ->select(
                                        'c.id as id',
                                        'c.contract_number',
                                        'c.contract_name',
                                        'c.contract_description',
                                        'c.contract_startdate',
                                        'c.contract_enddate',
                                        'c.contract_type',
                                        'c.frequency',
                                        'c.contract_unit',
                                        'c.supplier_id',
                                        's.supplier_name',
                                        'c.contractor_name'
                                       )->get();


$spreadsheet = new Spreadsheet();

        
$spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(100, 'pt');
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
      $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
 $image = file_get_contents(url('/').'/assets/icon/logo_black.png');
$imageName = 'logo.png';
$temp_image=tempnam(sys_get_temp_dir(), $imageName);
file_put_contents($temp_image, $image);
$drawing->setName('Logo');
$drawing->setDescription('Logo');
$drawing->setPath($temp_image); 
$drawing->setHeight(70);
$drawing->setCoordinates('C2');
$drawing->setOffsetX(110);


$drawing->getShadow()->setDirection(45);
$drawing->setWorksheet($spreadsheet->getActiveSheet());

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->setCellValue('D7', 'CONTRACT LIST ');
$spreadsheet->getActiveSheet()->getStyle('D7')->getFont()->setBold(true);
     
$spreadsheet->getProperties()->setCreator('cathebert muyila')
    ->setLastModifiedBy('Cathebert muyila')
    ->setTitle('PhpSpreadsheet Table Test Document')
    ->setSubject('PhpSpreadsheet Table Test Document')
    ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
    ->setKeywords('office PhpSpreadsheet php')
    ->setCategory('Table');

// Create the worksheet


$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()
    ->setCellValue('A8', 'Name')
    ->setCellValue('B8', 'Number')
    ->setCellValue('C8', 'Description')
    ->setCellValue('D8', 'Start Date')
    ->setCellValue('E8', 'End Date')
    ->setCellValue('F8', 'Type')
    ->setCellValue('G8', 'Frequency')
    ->setCellValue('H8', 'Unit')
    ->setCellValue('I8', 'Supplier/Contractor ');
  

$num=10;
  foreach ($contracts as $contract){

    switch($contract->contract_type){
case 1:
$type="Supplier";
break;
case 2:
$type="Service";
break;
    }
    switch($contract->contract_unit){
        case 1:
        $unit="Month";
        break;
        case 2:
        $unit="Year";
        break;
    }
    $name=$contract->supplier_name??$contract->contract_name;
  $data=[

    [$contract->contract_name,
    $contract->contract_number,
    $contract->contract_description,
    $contract->contract_startdate,
    $contract->contract_enddate,
    $type,
    $contract->frequency,
    $unit,
    $name,
]
  

  ];
 
$spreadsheet->getActiveSheet()->fromArray($data, null, 'A'.$num);
$num++;
}

$step=$num+1;
//$spreadsheet->getActiveSheet()->fromArray($data, null, 'A2');

   
    

// Create Table

$table = new Table('A8:I'.$num, 'About_To_Expire_Data');

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
$writer->save(public_path('reports').'/contracts_list.xlsx');
$path=public_path('reports').'/contracts_list.xlsx';
$name='contracts_list.xlsx';
$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

];

return response()->download($path,$name, $headers);
}
   
  }