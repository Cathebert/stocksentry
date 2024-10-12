<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laboratory;
use App\Models\LaboratorySection;
use App\Models\LabSection;
use Illuminate\Support\Facades\Validator;
use DB;
class LaboratoryController extends Controller
{
    //
    public function addLaboratory(){
        $data['laboratories']=Laboratory::get();
    $data['lab_sections']=LaboratorySection::get();
         $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        return view('laboratory.add',$data);

    }

public function createLaboratory(Request $request)
{

try{
DB::beginTransaction();
    $lab=new Laboratory();
    $lab->lab_name=$request->lab_name;
    $lab->lab_code=$request->lab_code;
    $lab->lab_location=$request->lab_location;
    $lab->lab_email=$request->lab_email;
    $lab->lab_phone=$request->lab_phone;
    $lab->lab_address=$request->lab_address;
    $lab->has_section=$request->has_section;
    $lab->created_at=now();

    $lab->updated_at=NULL;
    $lab->save();
    $id=$lab->id;
    if($request->has_section=="yes"){
if(!empty($request->section_id) && count($request->section_id)){
    for($x=0;$x<count($request->section_id);$x++){
    $labsec=new LabSection();
    $labsec->lab_id=$id;
    $labsec->section_id=$request->section_id[$x];
     $labsec->created_at=now();
     $labsec->updated_at=NULL;
     $labsec->save();
 }
 }
}
    
    DB::commit();
    return response()->json([
'message'=>"Laboratory Created Successfully",
 'status'=>"ok",
    ]);
}
catch(Exception $e){
    return response()->json([
'message'=>"Failed to create",
 'status'=>"bad",
    ]); 
}
}

public function showMoreLabData($id, $slug){
    $data['laboratory']=Laboratory::where('id',$id)->first();
    $data['lab_sections']=LaboratorySection::get();
   return view('laboratory.lab',$data);
}
 public function labList()
{
         $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
return view('laboratory.lab_list',$data);
}
public function loadLabList(Request $request){
   
     $columns = array(
            0 =>'id',
            1=>'name',
            2=>'location',
            3=>'email',
            4=>'phone',
            5=>'address',
            5=>'action',
            
        ); 
   $totalData = Laboratory::count();



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');
            $terms = Laboratory::where(function ($query) use ($search){
                  return  $query->where('lab_name', 'LIKE', "%{$search}%")
                  ->orWhere('lab_location','LIKE',"%{$search}%")
                   ->orWhere('lab_email','LIKE',"%{$search}%");
                  
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('id','asc')
            ->get();

          $totalFiltered =  $totalRec ;
//  0 => 'id',
    
          $data = array();
          if (!empty($terms)) {
$x=1;
  

            foreach ($terms as $term) {



                $nestedData['id']=$x;
                      $nestedData['name']= $term->lab_name;
                    $nestedData['location']= $term->lab_location;
                $nestedData['email']=$term->lab_email;
             
                  $nestedData['phone']= $term->lab_phone;
                    $nestedData['address']= $term->lab_address;
                 $nestedData['action'] = "<button type='button' id='$term->id' onclick='EditLab(this.id)' class='btn btn-primary'><i class='fa fa-edit'></i> Edit </button> | 
                   <button type='button' id='$term->id' onclick='DeleteLab(this.id)' 
                   class='btn btn-warning'><i class='fa fa-eye-slash'></i> Hide </button ";
                 
                    
              
     
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
public function updateLabDetails(Request $request){
   try{
    DB::beginTransaction();
    Laboratory::where('id',$request->lab_id)->update([
        'lab_name'=> $request->lab_name,
        'lab_location'=>$request->lab_location,
        'lab_email'=>$request->lab_email,
        'lab_phone'=>$request->lab_phone,
        'has_section'=>$request->has_section,
        'lab_address'=>$request->lab_address,
    ]);
    if($request->has_section=="no"){
         $this->deletePreviousSections($request->lab_id);
    }
    if($request->has_section=="yes"){
       if(!empty($request->section_id) && count($request->section_id)){
        $this->deletePreviousSections($request->lab_id);
    for($x=0;$x<count($request->section_id);$x++){
    $labsec=new LabSection();
    $labsec->lab_id=$request->lab_id;
    $labsec->section_id=$request->section_id[$x];
    $labsec->created_at=now();
    $labsec->updated_at=NULL;
    $labsec->save();
 }
 } 
    }
    DB::commit();
  return redirect()->route('lab.list')->with('success',' Laboratory Details updated Successfully');
}
catch(Exception $e){
    DB::rollback();
return redirect()->route('lab.list')->with('error',' Failed to update laboratory Details');   
}
}
public function editLabDetails(Request $request){
    $data['laboratory']=Laboratory::where('id',$request->id)->first();
    $data['lab_sections']=LaboratorySection::get();
    return view('laboratory.edit',$data);
}
private function deletePreviousSections($lab_id){
  LabSection::where('lab_id',$lab_id)->delete();


}

public function deleteLab(Request $request){
    try{
    DB::beginTransaction();
    Laboratory::find($request->id)->delete();
 
DB::commit();
    return response()->json([
        'message'=>'Laboratory hidden Successfully',
        'error' => false
    ]);
}
catch(Exception $e){
  DB::rollback();  
}
   
}
public function getSections(Request $request){
    
    $lab=Laboratory::where('id',$request->id)->select('lab_code')->first();
    $name=strtolower($lab->lab_code);
return response()->json([
            'name'=>$name,
            'status'=>0
        ]);
    }
   

}