<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Laboratory;
use App\Models\LaboratorySection;
use DB;

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
        return view('supplier.view',$data);
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
    public function update(Request $request, Supplier $supplier)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'supplier_name' => 'required|string',
            'contact_person' => 'required|string',
            'address' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'nullable|string',
            'contract_expiry' => 'nullable|date',
            // ... add more validation rules as needed ...
        ]);
        
        // Update the supplier using the validated data
        $supplier->update($validatedData);

        return redirect()->back()->with('success', 'Supplier updated successfully.');
        
    }

    public function destroy(Supplier $supplier ,$id)
    {
   
        $supplier->find($id)->delete();

        return redirect()->back()->with('success', 'Supplier deleted successfully.');
    }


}
