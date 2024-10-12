<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\User;
use App\Models\UserSetting;
 
class UserSettingController extends Controller
{
    //

public function show(){
       $lab=auth()->user()->laboratory_id;
       $data['users']=User::where('laboratory_id',$lab)->get();
        return view('settings.settings',$data);
    }
    
    public function labSetting(){
    $lab=auth()->user()->laboratory_id;
       $data['users']=User::where('laboratory_id',$lab)->get();
        return view('provider.settings.settings',$data); 
}
public function coldSetting(){
    $lab=auth()->user()->laboratory_id;
       $data['users']=User::where('laboratory_id',$lab)->get();
        return view('cold.settings.settings',$data); 
}

public function approve(Request $request){
    $lab=auth()->user()->laboratory_id;
    try{
        DB::beginTransaction();
        if(UserSetting::where('lab_id',$lab)->exists()){
UserSetting::where('lab_id',$lab)->delete();
        }
       
if(!empty($request->issue_email_receivers) && count($request->issue_email_receivers)>0){
foreach ($request->issue_email_receivers as $key => $val){    
  $setting=new UserSetting();
  $setting->lab_id=$lab;
$setting->user_id= $request->input('issue_email_receivers.'.$key );
  $setting->created_at=now();
  $setting->updated_at=NULL;
  $setting->save();
}
DB::commit();
return redirect()->back()->with('success','Saved') ;
}
else{
return redirect()->back()->with('error','please select emails') ;
}

  


 }
 catch(Exception $e){
 DB::rollback();
 return redirect()->back()->with('error','Failed') ;
}
}
public function load(Request $request){
  $columns = array(
            0=>'id',
            1=>'name',
            2=>'email',
            3=>'action'
        );

    $totalData = DB::table('user_settings as u')
                        ->join('users as a','a.id','=','u.user_id')
                        ->count();
    



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('user_settings as u')
                        
                        ->join('users as o','o.id','u.user_id')
                        ->select('u.id as id','o.name','o.email')
                        ->where('u.lab_id',auth()->user()->laboratory_id)
         
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
                $nestedData['name']= $term->name;
                $nestedData['email']= $term->email;
                $nestedData['action']=" <a class='btn btn-danger btn-sm' id='$term->id' onclick='removeUser(this.id)'><i class='fa fa-trash'></i>Remove</a>";
    
               
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
public function remove(Request $request){
 UserSetting::find($request->id)->delete();
 return response()->json([
    'message'=>"User removed Successfully",
    'error'=>false
 ]);   
}
}