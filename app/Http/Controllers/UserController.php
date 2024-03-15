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
use DB;
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
       dd($request);
        $data=$request->all();
$is_valid=$this->validator($data);
$hashed=$this->passwordGenerator(8,"0123456789abcdefghjkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ!@#$%^&*");
$default="12345678";
$password=Hash::make("12345678");
DB::beginTransaction();
if($is_valid->passes()){
   
try{
    $check_user=$request->username.''.$request->extension;
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
    $user->type=$request->user_type;
    $user->authority=$authority;
    $user->is_registered=1;
    $user->email=$request->email;
    $user->password=$password;
    $user->phone_number=$request->phone_number;
    $user->created_at=now();
    $user->updated_at=NULL;
    $user->save();

$user->notify(new WelcomeEmailNotification($default,$check_user));
//$user->notify(new WelcomeEmailNotification($hashed));
//return $user;
       
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
        $labs=Laboratory::where('id',auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
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
        $data['has_section'] = $lab->has_section;
    
        if ($lab->has_section == 'yes') {
            $data['lab_sections'] = DB::table('lab_sections as l')
                ->join('laboratory_sections as s', 's.id', '=', 'l.section_id')
                ->where('l.lab_id', auth()->user()->laboratory_id)->count();
            $data['sections'] = DB::table('lab_sections as l')
                ->join('laboratory_sections as s', 's.id', '=', 'l.section_id')
                ->select('s.id', 's.section_name')
                ->where('l.lab_id', auth()->user()->laboratory_id)->get();
        }
    $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        // Pass the $has_section variable to the view
        return view('user.view',$data);
    }
    
public function showLabUsers(){
    $data['users'] = User::with('laboratory')->where('laboratory_id', auth()->user()->laboratory_id)->get();
    
        // Your existing code to set $has_section
        $data['lab_details'] = Laboratory::select('id', 'lab_name')->get();
       // $lab = Laboratory::where('id', auth()->user()->laboratory_id)->select('has_section','lab_name')->first();
        $lab = Laboratory::where('id', auth()->user()->laboratory_id)->select('id', 'lab_name', 'has_section')->first();
        $data['labs'] = $lab;
        $data['has_section'] = $lab->has_section;
    
        if ($lab->has_section == 'yes') {
            $data['lab_sections'] = DB::table('lab_sections as l')
                ->join('laboratory_sections as s', 's.id', '=', 'l.section_id')
                ->where('l.lab_id', auth()->user()->laboratory_id)->count();
            $data['sections'] = DB::table('lab_sections as l')
                ->join('laboratory_sections as s', 's.id', '=', 'l.section_id')
                ->select('s.id', 's.section_name')
                ->where('l.lab_id', auth()->user()->laboratory_id)->get();
        }
       
   
   $section=LaboratorySection::where('id',auth()->user()->section_id)->select('section_name')->first();
   if($section){
     $data['lab_name']='Logged Into: '.$lab->lab_name.' / '.$section->section_name;  
   }
   else
   {
$data['lab_name']='Logged Into: '.$lab->lab_name;
}
        // Pass the $has_section variable to the view
        return view('provider.user.view',$data);  
}
    public function destroy(User $user)
    {
        
        $user->delete();

        return redirect()->route('user.view')->with('success', 'User deleted successfully.');
    }
    
    public function labUserDelete(User $user){
        $user->delete();

        return redirect()->route('lab-user.view')->with('success', 'User deleted successfully.');  
    }

    public function update(Request $request, User $user)
    {
        
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'required|string|max:10',
            'occupation' => 'required|string',
            'type' => 'nullable|in:1,2,3,4',
            'laboratory_id' => 'nullable_if:user_type,1,2|exists:laboratories,id',
           // 'section_id' => 'nullable_if:user_type,4|exists:laboratory_sections,id',
            // ... add more validation rules as needed ...
        ]);

        // Update the user using the validated data
        $user->update($validatedData);

        return redirect()->route('user.view')->with('success', 'User updated successfully.');
    }

    public function labUpdateUser(Request $request, User $user)
    {
        
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'required|string|max:10',
            'occupation' => 'required|string',
            'type' => 'nullable|in:1,2,3,4',
            'laboratory_id' => 'nullable_if:user_type,1,2|exists:laboratories,id',
          //  'section_id' => 'nullable_if:user_type,4|exists:laboratory_sections,id',
            // ... add more validation rules as needed ...
        ]);

        // Update the user using the validated data
       User::where('email',auth()->user()->email)->update([
            'name'=>$request->name,
            'last_name'=>$request->last_name,
            'email'=>$request->email,
            'phone_number'=>$request->phone_number,
            'occupation'=>$request->occupation,
            'authority'=>$request->user_type,
            'laboratory_id'=>$request->lab_id,

        ]);

        return redirect()->route('lab_user.view')->with('success', 'User updated successfully.');
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
                User::where('email',auth()->user()->email)->update([
                     'profile_img'=> $image_name,
                ]);
        }

         $data = $request->imagebase64;

            list($type, $data) = explode(';', $data);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            $image_name = time() . '.png';
            $path = public_path() . "/upload/profile/" . $image_name;
            file_put_contents($path, $data);
            //$user->profile_img = $image_name;
       }
       try{
User::where('email',auth()->user()->email)->update([
    'name'=>$request->name,
    'last_name'=>$request->last_name,
    'email'=>$request->email,
    'phone_number'=>$request->mobile,
    'profile_img'=> $image_name,
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
}


}