<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Notification;
use App\Notifications\ContactUsNotification;
use Illuminate\Notifications\Notifiable;
class HelpController extends Controller
{
    //
public function help(){
        return view('help.help');
    }
public function labHelp(){
        return view('help.lab_help');
    }
    
 public function userHelp(){
        return view('help.user_help');
    }
public function messageLog(){
$logs = DB::table('contact_us')->orderBy('created_at','desc')->get();
        return view('log.user_message_log',compact('logs'));
   } 
public function updateErrorLog(Request $request){
    DB::table('contact_us')->where('id',$request->id)->update([
        'is_resolved'=>'yes',
        'updated_at'=>now()
    ]);

    return response()->json([
    'message'=>"Marked as done"
]);
}
   public function contactUs(Request $request){
    $sender_email=auth()->user()->email;
    $message=$request->message;
    $has_file=$request->hasFile('attachment');
   

    $receipients=array("cdmuyila@gmail.com", "kapitoco@gmail.com");
    $path='';
    //dd(url()->previous());
    try{
    DB::beginTransaction();
   $message_id= DB::table('contact_us')->insertGetId(
    [
        'sender_email' => $sender_email, 
        'message' => $message,
        'has_file'=>$has_file,
        'is_resolved'=>'no',
        'uri'=>url()->previous(),
        'created_at'=>now(),
        'updated_at'=>NULL
       
    ]
);
   
   if($request->hasFile('attachment')){

                $name=$request->file('attachment')->getClientOriginalName();
                $replaced=str_replace(' ', '_', $name);
                $lower=strtolower($replaced);
                $renamed=$lower.random_int(0,1000) .'.'.$request->file('attachment')->extension();
               $request->file('attachment')->move(public_path().'/contact_us/', $renamed); 
              
    $path=public_path().'/contact_us/'.$renamed;
$contact_file=DB::table('contact_files')->insertGetId(
    [
       
        'message_id' => $message_id,
        'file_name'=>$renamed,
        'created_at'=>now(),
        'updated_at'=>now()
       
    ]
);
          
   }
  
   DB::commit();
foreach ($receipients as $email) {
  Notification::route('mail', $email)
->notify(new ContactUsNotification($message,$path));
}
  



    return redirect()->back()->with('success','Message Sent');
}
catch(Exception $e){
    DB::rollback();
    return redirect()->back()->with('error','failed');
}
   }
    
     public function coldHelp(){
        return view('help.cold_help');
    }
}