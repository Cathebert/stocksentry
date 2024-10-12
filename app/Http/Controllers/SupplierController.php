<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Laboratory;
use App\Models\LaboratorySection;
use DB;
use PDF;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Services\LogActivityService;
class SupplierController extends Controller
{
    //
    public function addSupplier(){
             $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        return view('supplier.add',$data);
    }

    public function createSupplier(Request $request){
     
        try{
            DB::beginTransaction();
        $supplier=new Supplier();
        $supplier->supplier_name=$request->supplier_name;
        $supplier->contact_person=$request->contact_person;
        $supplier->address=$request->address;
        $supplier->email=$request->email;
        $supplier->phone_number =   $request->phone_number;
        $supplier->contract_expiry  =   $request->contract_expiry;
        $supplier->created_at   =   now();
        $supplier->updated_at   =   null;
        $supplier->save();
DB::commit();
  LogActivityService::saveToLog('Supplier Created','Member with name  '.auth()->user()->name.' '.auth()->user()->last_name.' created '.$request->supplier_name,'low');
return response()->json([
    "error"=>false,
    'message'=>"Supplier added Successfully"

]);
        }
        catch(Exception $e){
        DB::rollback();
        return response()->json([
            'error' =>  true,
            'message'   =>  "Failed to add Supplier"

        ]);
    }
        
    }
     public function addLabSupplier(){
         $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section')->first();
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
             $labs=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$labs->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$labs->lab_name;
}
        return view('provider.supplier.create',$data);
    }
    
     public function view(){
        $data['suppliers']=Supplier::all();
             $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
       return view('supplier.supplier_list',$data);
    }
    
    public function loadAllSuppliers(Request $request){
    
  $columns = array(
            0=>'id',
            1=>'name',
            4=>'email',
            5=>'phone_number',
            6=>'expiry',
            7=>'action',
            8=>'address'
           
        );

    $totalData =Supplier::count();
    



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = Supplier::where(function ($query) use ($search){
                  return  $query->where('supplier_name', 'LIKE', "%{$search}%");
                 
                      
                     
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



                $nestedData['id']=$x;
                $nestedData['name']=$term->supplier_name;
                 $nestedData['address']=$term->address;
                $nestedData['email']= $term->email;
                $nestedData['phone_number']= $term->phone_number;
                if($term->contract_expiry!=NULL){
                $nestedData['expiry']=date('d,M Y',strtotime($term->contract_expiry));
        }
        else{
             $nestedData['expiry']="Not Set";
        }
                
                $nestedData['action']= " <a class='btn btn-info btn-sm' id='$term->id' onclick='editSupplier(this.id)'><i class='fa fa-edit'></i>Edit</a> | <a class='btn btn-warning btn-sm' id='$term->id' onclick='deleteSupplier(this.id)'><i class='fa fa-eye-slash'></i>Hide</a>";
    
               
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
    
    public function editSupplier(Request $request)
    {
             $data['labs'] = Laboratory::get();
     
$data['supplier']=Supplier::where('id', $request->id)->first();
$data['id']=$request->id;
return view('supplier.modal.edit',$data);
    }
public function labViewSupplier(){
    $data['suppliers']=Supplier::all();
         $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}



    return view('provider.supplier.supplier_list',$data);
}
 public function update(Request $request)
    {
        
        
        // Validate the request data
        $validatedData = $request->validate([
            'supplier_name' => 'required|string',
           // 'contact_person' => 'required|string',
            
            'email' => 'required|email',
            
            // ... add more validation rules as needed ...
        ]);
        
        // Update the supplier 
        Supplier::where('id',$request->id)->update([
            'supplier_name'=>$request->supplier_name,

            'address'=>$request->address,
            'email'=>$request->email,
            'phone_number'=>$request->phone_number,
            'contract_expiry'=>$request->expiry,
        ]);
        //$supplier->update($validatedData);

        return redirect()->back()->with('success', 'Supplier updated successfully.');
        
    }

     public function destroy(Request $request)
    {
    $supplier=Supplier::find($request->id)->supplier_name;
    LogActivityService::saveToLog('Supplier Deleted','Member with name  '.auth()->user()->name.' '.auth()->user()->last_name.' removed '.$supplier.' from the system','low');
        Supplier::find($request->id)->delete();

        return response()->json([
            'message'=>'Supplier deleted successfully.',
            'error'=>false
        ]);
    }

public function downloadSupplier($type){
$data['suppliers']=Cache::rememberForever('supplier', function () {
    return Supplier::get();
});

switch($type){
case 'pdf':


$name="suppliers.pdf";
    $path=public_path('suppliers').'/'.$name;
        
    $pdf=PDF::loadView('pdf.suppliers',$data);
     return $pdf->download($name);
$headers = [
  'Content-type' => 'application/pdf', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

]; 

return response()->download($path,$name, $headers);


break;

case "excel":
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
$drawing->setCoordinates('A1');
$drawing->setOffsetX(110);


$drawing->getShadow()->setDirection(45);
$drawing->setWorksheet($spreadsheet->getActiveSheet());

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->setCellValue('D1', 'SUPPLIERS LIST');
$spreadsheet->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
     
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
    ->setCellValue('B8', 'Email')
    ->setCellValue('C8', 'Phone')
    ->setCellValue('D8', 'Address');
   
  

$num=9;


  for ($x=0; $x<count($data['suppliers']); $x++){

  $dat=[

    [
    $data['suppliers'][$x]->supplier_name,
    $data['suppliers'][$x]->email, 
    $data['suppliers'][$x]->phone_number,
    $data['suppliers'][$x]->Address,
 
   
]
  

  ];
 $spreadsheet->getActiveSheet()->fromArray($dat, null, 'A'.$num);
$num++;
}

$step=$num+1;
//$spreadsheet->getActiveSheet()->fromArray($data, null, 'A2');


// Create Table

$table = new Table('A8:D'.$num, 'Expired_Data');

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
$writer->save(public_path('suppliers').'/suppliers.xlsx');
$path=public_path('suppliers').'/suppliers.xlsx';
$name='suppliers.xlsx';
$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

];
return response()->download($path,$name, $headers);





break;

}
}

}