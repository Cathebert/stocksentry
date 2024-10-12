<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laboratory;
 use App\Models\LaboratorySection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Notifications\WelcomeEmailNotification;
use App\Notifications\PasswordResetNotification;
use DB;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserController extends Controller
{
    //
    public function addUser(){
        $data['lab']=Laboratory::select('id','lab_name')->get();
       
        $lab_nam=Laboratory::where('id',auth()->user()->laboratory_id)->select('id','lab_name','has_section')->first();
        $data['labs']=$lab_nam;
        $data['has_section']=$lab_nam->has_section;
        if($lab_nam->has_section=="yes"){

           $data['sections']=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->where('l.lab_id',auth()->user()->laboratory_id)->get();
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
        return view('user.add',$data);
    }

    public function createModerator(Request  $request){
  //dd($request);
        $data=$request->all();
$is_valid=$this->validator($data);
$hashed=$this->passwordGenerator(8,"0123456789abcdefghjkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ!@#$%^&*");
$default="12345678";
$password=Hash::make($default);
DB::beginTransaction();
if($is_valid->passes()){
   
try{
    $check_user=$request->username.'.'.$request->extension;
    //dd($check_user);
     switch($request->check){
    case 0:
        $authority=$request->user_type;
        break;

    case 1:
    $authority=$request->cold_type;

    break;

    case 2:
        $authority=$request->lab_type;

        break;
    }
$status=$this->checkIfUserExists($check_user);
if($status==false){
$user= new User();
  $user->name=$request->first_name;
   $user->username=$check_user;
   $user->last_name=$request->last_name;
    $user->laboratory_id=$request->lab_id;
    $user->section_id=$request->section_id;
    $user->occupation=$request->user_position;
    $user->type=$authority;
    $user->authority=$authority;
    $user->is_registered=1;
    $user->email=$request->email;
    $user->password=$password;
    $user->phone_number=$request->phone_number;
    $user->created_at=now();
    $user->updated_at=NULL;
    $user->save();


$user->notify(new WelcomeEmailNotification($default,$check_user));
       
DB::commit();
 return response()->json([
        'error'=> false,
'message'=>"User added Successfully",
    ]);
}
else{
    return response()->json([
        'error'=> true,
'message'=>"Username already taken. Please change",
    ]);

}
}
catch(Exception $e){
    DB::rollback();
return response()->json([
        'error'=> true,
'message'=> "Failed to add user",
    ]);

}
}
else{
    return response()->json([
        'error'=> true,
'message'=> $is_valid->errors(),
    ]);
}
    }
 protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'user_position' => ['required','string','max:255'],
           
        ]);
    }
    function passwordGenerator(
    $length,
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
) {
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    if ($max < 1) {
        throw new Exception('$keyspace must be at least two characters long');
    }
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}
public function getReceiver(Request $request){

    $user=User::where('laboratory_id',$request->id)->select('id','name','last_name','occupation')->get();
     $lab=Laboratory::where('id',$request->id)->select('has_section')->first();
    if($lab->has_section=="yes"){
        $sections=DB::table('lab_sections as l')
                    ->join('laboratory_sections as s','s.id','=','l.section_id')
                    ->select('s.id as id','s.section_name')
                    ->where('l.lab_id',$request->id)->get();

return response()->json([
            'sections'=>$sections,
            'status'=>0,
             'user'=>$user,
        ]);
    }
    else{
        return response()->json([
            'sections'=>"",
            'status'=>1,
            'user'=>$user,
        ]);
    }



    

}
public function checkEmailExist(Request $request){
    if(User::where('username',$request->email)->exists()){
        return response()->json([
            'available'=>true,
            'message'=>"Username  already taken",
        ]);
    }
    else{
        return response()->json([
            'available'=>false,
            'message'=>'not available'
        ]);
    }
}

protected function checkIfUserExists($username){
   if(User::where('username',$username)->exists()){
        return true;
    } 
    else{
        return false;
    } 
}
 public function addLabUser(){
      $lab=Laboratory::select('id','lab_name')->get();
        $data['labs']=$lab;
        $labs=Laboratory::where('id',auth()->user()->laboratory_id)->select('id','has_section','lab_name','lab_code')->first();
        $data['lab']=$labs;
        $data['has_section']=$labs->has_section;
       
            $data['has_section']=$labs->has_section;
        if($labs->has_section=='yes'){
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
     $data['lab_name']='Logged Into: '.$labs->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$labs->lab_name;
}
        return view('provider.user.create',$data);
    }
    
     public function view()
    {
        // Retrieve the list of users and other necessary data
        $data['users'] = User::with('laboratory')->get();
    
        // Your existing code to set $has_section
        $data['labs'] = Laboratory::select('id', 'lab_name')->get();
       // $lab = Laboratory::where('id', auth()->user()->laboratory_id)->select('has_section')->first();
        $lab = Laboratory::where('id', auth()->user()->laboratory_id)->select('id', 'lab_name', 'has_section')->first();
        $data['lab_details'] = $lab;
      
 
$data['lab_name']='Logged Into: '.$lab->lab_name;

        // Pass the $has_section variable to the view
       return view('user.view_user_list',$data);
    }
    
public function showLabUsers(){
    $data['users'] = User::with('laboratory')->where('laboratory_id', auth()->user()->laboratory_id)->get();
    
        // Your existing code to set $has_section
        $data['lab_details'] = Laboratory::select('id', 'lab_name')->get();
       // $lab = Laboratory::where('id', auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
        $lab = Laboratory::where('id', auth()->user()->laboratory_id)->select('id', 'lab_name', 'has_section')->first();
        $data['labs'] = $lab;
      
    
      
       
   
  
$data['lab_name']='Logged Into: '.$lab->lab_name;

        // Pass the $has_section variable to the view
        return view('provider.user.view',$data);  
}
    public function destroy(Request $request)
    {
   
      User::where('id',$request->id)->delete();
     

         return response()->json([
            'success' => true ,
            'message' =>'User deleted successfully.'
        ],200);
    }
    public function resetPassword(Request $request){
       $default="12345678";
     $user=User::where('id',$request->id)->select('email')->first();
$password=Hash::make("12345678");
User::where('email',$user->email)->update([
    'password'=>$password
]);
$user=User::find($request->id);
$user->notify(new PasswordResetNotification($default,$user->name.' '.$user->last_name));
return response()->json([
    'message'=>'password has been reset to. '.$default,
    'error'=>false,
]);
    }
    
    public function labUserDelete(Request $request){
   
        User::find($request->id)->delete();

       return response()->json([
            'success' => true ,
            'message' =>'User deleted successfully.'
        ],200);
    }
 public function editUser(Request $request)
    {
             $data['labs'] = Laboratory::get();
     
$data['user']=User::with('laboratory')->where('id', $request->id)->first();
$data['id']=$request->id;
return view('user.modal.edit',$data);
    }
   
    
    public function update(Request $request)
    {
       //dd($request);
        
        switch($request->check){
        case 0:
        $authority=$request->user_type;
        break;

    case 1:
    $authority=$request->cold_type;

    break;

    case 2:
        $authority=$request->lab_type;

        break;
    }
    User::where('id',$request->id)->update([
        'name'=>$request->name,
       'last_name'=>$request->last_name,
       'laboratory_id'=>$request->lab_id,
       'occupation'=>$request->occupation,
       'type'=>$request->user_type,
      'authority'=>$authority,
     'email'=>$request->email,
    'phone_number'=>$request->phone_number,
    'updated_at'=>NULL,
    ]);


        return redirect()->route('user.view')->with('success', 'User updated successfully.');
    }

    public function labUserUpdate(Request $request, User $user)
    {
        //dd($request);

        $authority=$request->lab_type;

      
  
    User::where('id',$request->id)->update([
        'name'=>$request->name,
       'last_name'=>$request->last_name,
       'laboratory_id'=>$request->lab_id,
       'occupation'=>$request->occupation,
       'type'=>$authority,
      'authority'=>$authority,
     'email'=>$request->email,
    'phone_number'=>$request->phone_number,
    'updated_at'=>NULL,
    ]);

        return redirect()->back()->with('success', 'User updated successfully.');
    }
   
    public function profileView(){
 $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
     
        $data['users'] = Auth::user();
        return view('user.profile', $data);
    }
    
    //profile for lab user
      public function labProfileView(){
 $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
     
        $data['users'] = Auth::user();
        return view('provider.user.profile', $data);
    }
    public function labUserProfileView(){
 $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
     
        $data['users'] = Auth::user();
        return view('clerk.user.profile', $data);
    }
     public function profileUpdate(Request $request)
    {
        $user = User::find(auth()->user()->email);

   // dd($request);
        $validatedData = $request->validate([
            'name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'nullable|string|max:10',
            'image' => 'image|mimes:jpeg,png,jpg,gif', // Add validation for image upload
        ]);


    $profile=auth()->user()->profile_img;
if ($request->is_remove_image == "Yes" && $request->file('image') == "") {

            if ($user->profile_img != '') {
                $imageUnlink = public_path() ."/upload/profile/" . $profile;
                if (file_exists($imageUnlink)) {
                    unlink($imageUnlink);
                }
                User::where('email',auth()->user()->email)->update([
                     'profile_img'=> '',
                ]);
            }
        }
       if($request->hasFile('image')){
        if($profile!=NULL){
$imageUnlink = public_path() ."/upload/profile/" . $profile;
                if (file_exists($imageUnlink)) {
                    unlink($imageUnlink);
                }
               // $user->profile_img = '';
                 $data = $request->imagebase64;

            list($type, $data) = explode(';', $data);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            $image_name = time() . '.png';
            $path = public_path() . "/upload/profile/" . $image_name;
            file_put_contents($path, $data);
            //$user->profile_img = $image_name;
            User::where('email',auth()->user()->email)->update([
                     'profile_img'=> $image_name,
                ]);
                
        }

       
       }
       try{
User::where('email',auth()->user()->email)->update([
    'name'=>$request->name,
    'last_name'=>$request->last_name,
    'email'=>$request->email,
    'phone_number'=>$request->mobile,
   
    'updated_at'=>now()
]);
return redirect()->back()->with('success', 'Profile updated successfully.');
       }
       catch(Exception $e){
        return redirect()->back()->with('error', 'Profile failed updating.');
       }
   
        
    }
    
    
    
public function signature(){
 $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
     
        $data['users'] = Auth::user();
    return view('user.signature',$data);
}

//labmanager signature view
public function labSignature(){
 $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
     
        $data['users'] = Auth::user();
    return view('provider.user.signature',$data);
}
public function labUserSignature(){
 $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
     
        $data['users'] = Auth::user();
    return view('clerk.user.signature',$data);
}


public function updateSignature (Request $request){

     $id = auth()->user()->id;
   $user=User::find($id);
   $signature=auth()->user()->signature;
   if($request->sign_check==0 && $request->imagebase64=="data:,"){
       return redirect()->back()->with('error', 'At least choose one method.');

   }
   if($request->sign_check==1){
     if ($user->signature != '') {

                $imageUnlink = public_path() ."/upload/signatures/" . $signature;
                if (file_exists($imageUnlink)) {
                    unlink($imageUnlink);
                }
                $user->signature = '';
            }
            $data = $request->signature;

            list($type, $data) = explode(';', $data);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            $image_name = time() . '.png';
            $path = public_path() . "/upload/signatures/" . $image_name;
            file_put_contents($path, $data);
            //$user->signature = $image_name;

            try{
                User::where('email',auth()->user()->email)->update([
                    'signature'=>$image_name
                ]);
//$user->save();
$flag=TRUE;
            }
            catch(Exception $e){
$flag=FALSE;
            }
            if ($flag) {
               

 return redirect()->back()->with('success', 'Signature updated successfully.');
            } else {
               
 return redirect()->back()->with('error', 'Failed to update Signature.');
                

            }

   }

   if ($request->hasFile('image')) {
 if ($user->signature != '') {

                $imageUnlink = public_path() ."/upload/signatures/".$signature;
                if (file_exists($imageUnlink)) {
                    unlink($imageUnlink);
                }
                $user->signature = '';
            }
            $filename = time() . '.' . $request->image->extension();

        $request->image->move(public_path()/"/upload/signatures/", $filename);

        // save uploaded image filename here to your database

       
            $user->signature = $file_name;

            try{
$user->save();
$flag=TRUE;
            }
            catch(Exception $e){
$flag=FALSE;
            }
            if ($flag) {
               

                return redirect()->back()->with('success', 'Signature updated successfully.');
            } else {
               
 return redirect()->back()->with('error', 'Failed to update Signature.');
                

            }

   }
}
public function password(){
     $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
     
    return view('user.password.change-password',$data);
}
 
 
 //lab password
 
 public function labPassword(){
     $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
     
    return view('provider.user.password.change-password',$data);
}
public function labUserPassword(){
     $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
     
    return view('clerk.user.password.change-password',$data);
}
public function changePassword(Request $request)
{
    //dd($request);
     $this->validate($request, [
            'old' => 'required',
            'new' => 'required|string|min:8',
            'confirm' => 'required|same:new'
        ]);

   $id = auth()->user()->id;
        $user = User::find($id);

        $current_password = $request->old;
        $password = $request->new;

        if (Hash::check($current_password, $user->password)) {
           $user->password = Hash::make($password);
            $user->updated_at=now();
            try {
               $user->save();
                $flag = TRUE;
            } catch (Exception $e) {
                $flag = FALSE;
            }
            if ($flag) {
               

                return response()->json([
                    'message'=>'Password changed successfully.',
                    'error'=>false
                    ]);
            } else {
               
 return response()->json([
                    'message'=>'Unable to process request this time. Try again later.',
                    'error'=>true
                    ]);
                

            }

        }
         else {
            return response()->json([
                    'message'=>'Your current password do not match.',
                    'error'=>true
                    ]);
  
        }
}
public function sectionHeadProfileView(){
 $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
     
        $data['users'] = Auth::user();
        return view('sectionhead.user.profile', $data);
    }
    
     public function sectionHeadPassword(){
     $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
     
    return view('sectionhead.user.password.change-password',$data);
}
public function sectionHeadSignature(){
 $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
     
        $data['users'] = Auth::user();
    return view('sectionhead.user.signature',$data);
}

public function InitializeUser(Request $request){
  
    $this->validate($request, [
            'old' => 'required',
            'new' => 'required|string|min:8',
            'confirm' => 'required|same:new'
        ]);

   $id = auth()->user()->id;
        $user = User::find($id);

        $current_password = $request->old;
        $password = $request->new;

        if (Hash::check($current_password, $user->password)) {
           $password = Hash::make($password);
           
            try {
                User::where('email',auth()->user()->email)->update([
                    'password'=>$password,
                    'updated_at'=>now()
                ]);
               //$user->save();
                $flag = TRUE;
            } catch (Exception $e) {
                $flag = FALSE;
            }

            $id = auth()->user()->id;
   $user=User::find($id);
   $signature=auth()->user()->signature;
   if($request->sign_check==0 && $request->imagebase64=="data:,"){
       return response()->json([
        'message'=> 'At least choose one method',
        'error'=>true
    ]);

   }
   if($request->sign_check==1){
     if ($user->signature != '') {

                $imageUnlink = public_path() ."/upload/signatures/" . $signature;
                if (file_exists($imageUnlink)) {
                    unlink($imageUnlink);
                }
                User::where('email',auth()->user()->email)->update([
                    'signature' => ''
                ]);
                
            }
            $data = $request->signature;

            list($type, $data) = explode(';', $data);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            $image_name = time() . '.png';
            $path = public_path() . "/upload/signatures/" . $image_name;
            file_put_contents($path, $data);
            //$user->signature = $image_name;

            try{
                User::where('email',auth()->user()->email)->update([
                    'signature'=>$image_name
                ]);
//$user->save();
$signed=TRUE;
            }
            catch(Exception $e){
$signed=FALSE;
            }
            if ($flag && $signed) {
               User::where('email',auth()->user()->email)->update([
                'is_registered'=>0
               ]);

 return response()->json([
    'message'=> 'Details updated successfully.',
    'error'=>false
]);
            } else {
               
 return response()->json([
    'message'=>'Failed to update details.',
    'error'=>true
]);
                

            }

   }

   if ($request->hasFile('image')) {
 if ($user->signature != '') {

                $imageUnlink = public_path() ."/upload/signatures/".$signature;
                if (file_exists($imageUnlink)) {
                    unlink($imageUnlink);
                }
                 User::where('email',auth()->user()->email)->update([
                'signature'=>'',
            ]);
            }
            $filename = time() . '.' . $request->image->extension();

        $request->image->move(public_path()/"/upload/signatures/", $filename);

        // save uploaded image filename here to your database

       
           // $user->signature = $file_name;

           

            try{
 User::where('email',auth()->user()->email)->update([
                'signature'=>$file_name,
            ]);
$signed=TRUE;
            }
            catch(Exception $e){
$signed=FALSE;
            }
            if ($flag && $signed) {
               
               User::where('email',auth()->user()->email)->update([
                'is_registered'=>0
               ]);

                return response()->json([
                    'message'=>'Details updated successfully.',
                    'error'=>false
                ]);
            } else {
               
 return response()->json([
    'message'=>'Failed to update Details.',
    'error'=>true
]);
                

            }

   }
}

else{
 return response()->json([
    'message'=>'Your current Password does not match registered password.',
    'error'=>true
]);
      

}
}

public function loadUsers(Request $request){
    $columns = array(
            0=>'id',
            1=>'username',
            2=>'name',
            3=>'last_name',
            4=>'email',
            5=>'phone',
            6=>'lab',
            7=>'location',
            8=>'options'
        );

    $totalData = User::with('laboratory')->count();
    


            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('users as u')
            ->join('laboratories as l','l.id','=','u.laboratory_id')
         ->select('u.id as id','u.username','u.name','u.last_name','u.email','u.phone_number','l.lab_name','l.lab_location')
           ->whereNull('u.deleted_at')
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('u.username', 'LIKE', "%{$search}%")
                  ->orWhere('u.name','LIKE',"%{$search}%")
                   ->orWhere('u.last_name','LIKE',"%{$search}%");
                 
                      
                     
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
                $nestedData['username']=$term->username;
                $nestedData['name']= $term->name;
                $nestedData['last_name']= $term->last_name;
                $nestedData['email']= $term->email;
                $nestedData['phone']= $term->phone_number??"Unavailable";
                $nestedData['lab']= $term->lab_name??"Unavailable";
                $nestedData['location']= $term->lab_location??"Unavailable";
                $nestedData['options']= " <a class='btn btn-info btn-sm' id='$term->id' onclick='editUser(this.id)'><i class='fa fa-edit'></i>Edit</a> | <a class='btn btn-warning btn-sm' id='$term->id' onclick='resetPassword(this.id)'><i class='fa fa-lock'></i>Reset</a> | <a class='btn btn-danger btn-sm' id='$term->id' onclick='deleteUser(this.id)'><i class='fa fa-trash'></i>Delete</a>";
    
               
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
public function loadLabUsers(Request $request){
$columns = array(
            0=>'id',
            1=>'username',
            2=>'name',
            3=>'last_name',
            4=>'email',
            5=>'phone',
            6=>'lab',
            7=>'location',
            8=>'options'
        );

    $totalData = User::with('laboratory')->where('laboratory_id',auth()->user()->laboratory_id)->count();
    



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

           $terms = DB::table('users as u')
            ->join('laboratories as l','l.id','=','u.laboratory_id')
         ->select('u.id as id','u.username','u.name','u.last_name','u.email','u.phone_number','l.lab_name','l.lab_location')
           ->whereNull('u.deleted_at')
          ->where('laboratory_id',auth()->user()->laboratory_id)
         
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('name', 'LIKE', "%{$search}%");
                 
                      
                     
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
                $nestedData['username']=$term->username;
                $nestedData['name']= $term->name;
                $nestedData['last_name']= $term->last_name;
                $nestedData['email']= $term->email;
                $nestedData['phone']= $term->phone_number??"Unavailable";
                $nestedData['lab']= $term->lab_name;
                $nestedData['location']=$term->lab_location;
                $nestedData['options']= " <a class='btn btn-info btn-sm' id='$term->id' onclick='editUser(this.id)'><i class='fa fa-edit'></i>Edit</a> | <a class='btn btn-warning btn-sm' id='$term->id' onclick='resetPassword(this.id)'><i class='fa fa-lock'></i>Reset</a> | <a class='btn btn-danger btn-sm' id='$term->id' onclick='deleteUser(this.id)'><i class='fa fa-trash'></i>Delete</a>";
    
               
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
 public function labEditUser(Request $request)
    {
             $data['labs'] = Laboratory::where('id',auth()->user()->laboratory_id)->get();
     
$data['user']=User::with('laboratory')->where('id', $request->id)->first();
$data['id']=$request->id;
return view('provider.user.modal.edit',$data);
    }
    public function labUserResetPassword(Request $request){
     $user=User::where('id',$request->id)->select('email')->first();
       $default="12345678";
$password=Hash::make("12345678");
User::where('id',$request->id)->update([
    'password'=>$password
]);
return response()->json([
    'message'=>'password has been reset to. '.$default,
    'error'=>false,
]);

}
public function downloadUser(Request $request){
parse_str($request->expiry_form,$out);
 $expired= $out;
 $lab=$expired['lab'];
if($lab==-1){
$data['lab_name']="All Users";
$data['users']=DB::table('users as u')
            ->join('laboratories as l','l.id','=','u.laboratory_id')
         ->select('u.id as id','u.username','u.name','u.last_name','u.email','u.phone_number','l.lab_name','l.lab_location')
          ->get();
}
else{
$lab=Laboratory::where('id',$lab)->select('lab_name')->first();
$data['lab_name']=$lab->lab_name;
$data['users']=DB::table('users as u')
            ->join('laboratories as l','l.id','=','u.laboratory_id')
         ->select('u.id as id','u.username','u.name','u.last_name','u.email','u.phone_number','l.lab_name','l.lab_location')
          ->where('laboratory_id',$lab)->get();
}

switch($request->type){

case "pdf":
 $name="users.pdf";
    $path=public_path('users').'/'.$name;
        
    $pdf=PDF::loadView('pdf.users',$data);
        $pdf->save($path); 
$url=route('user.get_user_pdf',['name'=>$name]);

return response()->json([
    'path'=>$name,
    'url'=>$url,

]);


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
$drawing->setCoordinates('C2');
$drawing->setOffsetX(110);


$drawing->getShadow()->setDirection(45);
$drawing->setWorksheet($spreadsheet->getActiveSheet());

$spreadsheet->setActiveSheetIndex(0);
$spreadsheet->getActiveSheet()->setCellValue('D7', 'Users ');
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
    ->setCellValue('A8', 'User Name')
    ->setCellValue('B8', 'First Name')
    ->setCellValue('C8', 'Last Name')
    ->setCellValue('D8', 'Email')
    ->setCellValue('E8', 'Phone')
    ->setCellValue('F8', 'Lab');
  

$num=9;
$total=0;
$overall_total=0;

  for ($x=0; $x<count($data['users']); $x++){

  $dat=[

    [
    $data['users'][$x]->username,
    $data['users'][$x]->name, 
    $data['users'][$x]->last_name,
    $data['users'][$x]->email,
    $data['users'][$x]->phone_number,
    $data['users'][$x]->lab_name,
   
]
  

  ];
 $spreadsheet->getActiveSheet()->fromArray($dat, null, 'A'.$num);
$num++;
}

$step=$num+1;
//$spreadsheet->getActiveSheet()->fromArray($data, null, 'A2');


// Create Table

$table = new Table('A8:F'.$num, 'Expired_Data');

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
$writer->save(public_path('users').'/users.xlsx');
$path=public_path('users').'/users.xlsx';
$name='users.xlsx';
$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

];
$url=route('user.get_user_excel',['name'=>$name]); 
return response()->json([
    'path'=>$name,
    'url'=>$url,
]);



break;
}

}
public function getUserPDFDownload($name){

$path=public_path('users').'/'.$name;
$name=now().'_users.pdf';
$headers = [
  'Content-type' => 'application/pdf', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

]; 
return response()->download($path,$name, $headers);
}

public function getUserExcelFile($name){
   
    $path=public_path('users').'/'.$name;
$name=now().'_'.$name;;
$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

]; 
return response()->download($path,$name, $headers);
}
public function filterUsers(Request $request){

 $lab=$request->id;
 
    $columns = array(
            0=>'id',
            1=>'username',
            2=>'name',
            3=>'last_name',
            4=>'email',
            5=>'phone',
            6=>'lab',
            7=>'location',
            8=>'options'
        );
if($lab==-1){
    $totalData = User::with('laboratory')->count();
    


            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('users as u')
            ->join('laboratories as l','l.id','=','u.laboratory_id')
         ->select('u.id as id','u.username','u.name','u.last_name','u.email','u.phone_number','l.lab_name','l.lab_location')
           ->whereNull('u.deleted_at')
          //->where('t.expiry_date', '>', date('Y-m-d') )
          
                ->where(function ($query) use ($search){
                  return  $query->where('u.username', 'LIKE', "%{$search}%")
                  ->orWhere('u.name','LIKE',"%{$search}%")
                   ->orWhere('u.last_name','LIKE',"%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('id','desc')
            ->get();
            $totalFiltered =  $totalRec ;
}
else{
  $totalData = User::with('laboratory')->where('laboratory_id',$lab)->count();
    


            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('users as u')
            ->join('laboratories as l','l.id','=','u.laboratory_id')
         ->select('u.id as id','u.username','u.name','u.last_name','u.email','u.phone_number','l.lab_name','l.lab_location')
           ->whereNull('u.deleted_at')
          ->where('u.laboratory_id', $lab)
                ->where(function ($query) use ($search){
                  return  $query->where('u.username', 'LIKE', "%{$search}%")
                  ->orWhere('u.name','LIKE',"%{$search}%")
                   ->orWhere('u.last_name','LIKE',"%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('id','desc')
            ->get();

$totalFiltered =  $totalRec ;

}
          




        $data = array();
          if (!empty($terms)) {
$x=1;
 

            foreach ($terms as $term) {



                $nestedData['id']=$x;
                $nestedData['username']=$term->username;
                $nestedData['name']= $term->name;
                $nestedData['last_name']= $term->last_name;
                $nestedData['email']= $term->email;
                $nestedData['phone']= $term->phone_number??"Unavailable";
                $nestedData['lab']= $term->lab_name??"Unavailable";
                $nestedData['location']= $term->lab_location??"Unavailable";
                $nestedData['options']= " <a class='btn btn-info btn-sm' id='$term->id' onclick='editUser(this.id)'><i class='fa fa-edit'></i>Edit</a> | <a class='btn btn-warning btn-sm' id='$term->id' onclick='resetPassword(this.id)'><i class='fa fa-lock'></i>Reset</a> | <a class='btn btn-danger btn-sm' id='$term->id' onclick='deleteUser(this.id)'><i class='fa fa-trash'></i>Delete</a>";
    
               
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
public function showDeletedUsers(){
 return view('inventory.modal.deleted_users');
}
public function loadDeletedUsers(Request $request){
 $columns = array(
            0=>'id',
            1=>'username',
            2=>'name',
            3=>'last_name',
            4=>'email',
            5=>'phone',
            6=>'lab',
            7=>'location',
            8=>'options'
        );

    $totalData = DB::table('users as u')
            ->join('laboratories as l','l.id','=','u.laboratory_id')
      
           ->whereNotNull('u.deleted_at')->count();
    


            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('users as u')
            ->join('laboratories as l','l.id','=','u.laboratory_id')
         ->select('u.id as id','u.username','u.name','u.last_name','u.email','u.phone_number','l.lab_name','l.lab_location')
           ->whereNotNull('u.deleted_at')
          //->where('t.expiry_date', '>', date('Y-m-d') )
                ->where(function ($query) use ($search){
                  return  $query->where('u.username', 'LIKE', "%{$search}%")
                  ->orWhere('u.name','LIKE',"%{$search}%")
                   ->orWhere('u.last_name','LIKE',"%{$search}%");
                 
                      
                     
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
                $nestedData['username']=$term->username;
                $nestedData['name']= $term->name;
                $nestedData['last_name']= $term->last_name;
                $nestedData['email']= $term->email;
                $nestedData['phone']= $term->phone_number??"Unavailable";
                $nestedData['lab']= $term->lab_name??"Unavailable";
                $nestedData['location']= $term->lab_location??"Unavailable";
                $nestedData['options']= "<a class='btn btn-success btn-sm' id='$term->id' onclick='restoreUser(this.id)'> <i class='fas fa-trash-restore'></i>Restore</a>";
    
               
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
 public function restoreUser(Request $request){

     User::where('id',$request->id)->restore(); 
      $message="User Successfully restored"; 
    return response()->json([
            'success' => true ,
            'message' =>$message
        ],200);



    }
}