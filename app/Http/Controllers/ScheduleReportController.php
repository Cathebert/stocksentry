<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScheduleReport;
use App\Models\User;
use App\Models\EmailReceipient;
use DB;
use Carbon\Carbon;
class ScheduleReportController extends Controller
{
    //

    public function show(){
$data['users']=User::where('laboratory_id',auth()->user()->laboratory_id)->get();
        return view('pdf.reports.scheduled.show',$data);
    }
public function showBackUp(){
$data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])->get();
        return view('pdf.reports.scheduled.backup',$data);
}
    public function load(Request $request){
          
   $columns = array(
            0=>'id',
            1=>'name',
            2=>'frequency',
            3=>'date',
            4=>'type',
            5=>'receipient',
            6=>'file',
            7=>'status',
            8=>'scheduled_by',
            9=>'action',
            10=>'delete',
            
        );

    $totalData = ScheduleReport::count();
    



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('schedule_reports as s')
            ->join('laboratories as l','l.id','=','s.lab_id')
            ->select(
                's.id as id',
                's.user_id',
                's.name',
                's.frequency',
                's.type',
                's.start_date',
                's.next_run_date',
                's.attach_as',
                's.status',
                's.created_at',
                'l.lab_name'
            )
            ->where(function ($query) use ($search){
                  return  $query->where('s.name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('s.id','desc')
            ->get();

          $totalFiltered =  $totalRec ;




        $data = array();
          if (!empty($terms)) {
$x=1;


            foreach ($terms as $term) {
$user=User::where('id',$term->user_id)->select('name','last_name')->first();
$receipients=DB::table('email_receipients as u')
->join('users as e','e.id','=','u.user_id')
->where('report_id',$term->id)->get();

                $nestedData['id']=$x;
                $nestedData['name']=$term->name;
                switch($term->type){
                        case 1:
       
         $type="Consumption";
        break;
        case 2:
       
         $type="Stock Level";
            break;
        case 3:
    
         $type="Requisition";
        break;

        case 4:
        
         $type="Disposal";
        break;
        case 5:
      
        $type="Expired";
        break;
    }

              
                
               
                switch ($term->frequency) {
                    case 1:
                       $nestedData['frequency']= "Weekly"
                       ;
                       $date = Carbon::parse($term->start_date); // Parse the date string into a Carbon instance

// Add 7 days to the date
$next_run_date = $date->addDays(7)->format('d M Y');
                        break;
                    case 2:
                       $nestedData['frequency']= "Monthly";
                        $date = Carbon::parse($term->start_date); // Parse the date string into a Carbon instance

// Add 7 days to the date
$next_run_date = $date->addMonth()->format('d M Y');
                        break;
                    case 3:
                       $nestedData['frequency']= "Quarterly";
                        $date = Carbon::parse($term->start_date); // Parse the date string into a Carbon instance

// Add 7 days to the date
$next_run_date = $date->addQuarters(1)->format('d M Y');
                        break;
                    case 4:
                       $nestedData['frequency']= "Yearly";
                        $date = Carbon::parse($term->start_date); // Parse the date string into a Carbon instance

// Add 7 days to the date
$next_run_date = $date->copy()->addYear()->format('d M Y');
                        break;
                    
                }
                
                 $nestedData['date']=$next_run_date;
                   $nestedData['scheduled_by']= $user->name.' '.$user->last_name;
                      $nestedData['type']=$type;
                   if(!empty($receipients) && count($receipients)>0){
                     $nestedData['receipient']= '<p>Receiver(s):</p>';

                    foreach($receipients as $r){
                         $nestedData['receipient'].= '<span class="badge badge-info">'.$r->name.' '.$r->last_name.'</span>';

                    }
                   }
                   else{
                     $nestedData['receipient']="";
                   }
               
                switch ($term->attach_as) {
                    case 1:
                   $nestedData['file']= 'PDF';
                        break;
                    
                    case 2:
                        $nestedData['file']= 'Excel';
                        break;
                }
                
               if($term->status=='active'){
                 $nestedData['status']='<span class="badge badge-success">'.$term->status.'</span>';
                 $nestedData['action']="<a class='btn btn-success btn-sm'  name='deactivate' id='$term->id' onclick='Deactivate(this.id,this.name)' ><i class='fa fa-pause'></i> Deactivate</a>";
                 
                }
                else{
                   $nestedData['status']= '<span class="badge badge-danger">'.$term->status.'</span>'; 
                    $nestedData['action']="<a class='btn btn-warning btn-sm' type='button'  id='$term->id' name='activate' onclick='Deactivate(this.id,this.name)'><i class='fa fa-check'></i> Activate</a>";
                  
                }
       $nestedData['delete']="<a class='btn btn-danger btn-sm'   id='$term->id' onclick='DeleteSchedule(this.id)'><i class='fa fa-trash'></i></a>";
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
    public function deactivate(Request $request){
   
    if($request->name=='deactivate'){
    ScheduleReport::where('id',$request->id)->update([
        'status'=>'inactive'
    ]);
    return response()->json([
        'message'=>"Schedule Deactivated Successfully",
        'error'=>false
    ]);
    }
    else{
    ScheduleReport::where('id',$request->id)->update([
        'status'=>'active',
        'start_date'=>now()
    ]);
    return response()->json([
        'message'=>"Schedule Activated Successfully",
        'error'=>false
    ]);
    
    }
}

 
   public function delete(Request $request){
    ScheduleReport::find($request->id)->delete();
    return response()->json([
        'message'=>"Schedule Deleted Successfully",
        'error'=>false
    ]);
}



public function save(Request $request){
//dd($request);
    switch ($request->report_type) {
        case 1:
        $name="Consumption Report";
         $type="Consumption";
        break;
        case 2:
        $name="Stock Level Report";
         $type="Stock Level";
            break;
        case 3:
        $name="Requisition Report";
         $type="Requisition";
        break;

        case 4:
        $name="Stock Disposal Report";
         $type="Disposal";
        break;
       
         case 5:
        $name="Expired Report";
        $type="Expiry";
        break;
    }
    try{
        DB::beginTransaction();
   $schedule= new ScheduleReport();
    $schedule->user_id=auth()->user()->id;
   $schedule->lab_id=auth()->user()->laboratory_id;
   $schedule->name=$name;
   $schedule->type=$request->report_type;
   $schedule->start_date=$request->start_date;
   $schedule->frequency=$request->frequency;
   $schedule->attach_as=$request->attach_as;
    $schedule->email_list=json_encode($request->employee_involved);
   $schedule->status="active";
   $schedule->save();
$id=$schedule->id;
      if(!empty($request->employee_involved)){
         foreach ($request->employee_involved as $key => $val) {

$recipient=new EmailReceipient();
$recipient->report_id=$id;
$recipient->user_id= $request->input('employee_involved.' . $key );
$recipient->save();
DB::commit();
}
}
 return redirect()->back()->with('success',"Report scheduled Successfully");  
}
catch(Exception $e){
    DB::rollback();
    return redirect()->back()->with('error',"Report scheduling Failed");  
}

}

 public function labshow(){
$data['users']=User::where('laboratory_id',auth()->user()->laboratory_id)->get();
        return view('provider.scheduled.show',$data);
    }

    public function labload(Request $request){
       $lab=auth()->user()->laboratory_id;   
   $columns = array(
            0=>'id',
            1=>'name',
            2=>'frequency',
            3=>'date',
            4=>'type',
            5=>'receipient',
            6=>'file',
            7=>'status',
            8=>'scheduled_by',
            9=>'action',
            10=>'delete',
            
        );

    $totalData = ScheduleReport::where('lab_id',$lab)->count();
    



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('schedule_reports as s')
            ->join('laboratories as l','l.id','=','s.lab_id')
            ->where('lab_id',$lab)
            ->select(
                's.id as id',
                's.user_id',
                's.name',
                's.frequency',
                's.type',
                's.start_date',
                's.next_run_date',
                's.attach_as',
                's.status',
                's.created_at',
                'l.lab_name'
            )
            ->where(function ($query) use ($search){
                  return  $query->where('s.name', 'LIKE', "%{$search}%");
                 
                      
                     
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy('s.id','desc')
            ->get();

          $totalFiltered =  $totalRec ;




        $data = array();
          if (!empty($terms)) {
$x=1;


            foreach ($terms as $term) {
$user=User::where('id',$term->user_id)->select('name','last_name')->first();
$receipients=DB::table('email_receipients as u')
->join('users as e','e.id','=','u.user_id')
->where('report_id',$term->id)->get();

                $nestedData['id']=$x;
                $nestedData['name']=$term->name;
                switch($term->type){
                        case 1:
       
         $type="Consumption";
        break;
        case 2:
       
         $type="Stock Level";
            break;
        case 3:
    
         $type="Requisition";
        break;

        case 4:
        
         $type="Disposal";
        break;
        case 5:
      
        $type="Expiry";
        break;
    }

              
                
               
                switch ($term->frequency) {
                    case 1:
                       $nestedData['frequency']= "Weekly"
                       ;
                       $date = Carbon::parse($term->start_date); // Parse the date string into a Carbon instance

// Add 7 days to the date
$next_run_date = $date->addDays(7)->format('d M Y');
                        break;
                    case 2:
                       $nestedData['frequency']= "Monthly";
                        $date = Carbon::parse($term->start_date); // Parse the date string into a Carbon instance

// Add 7 days to the date
$next_run_date = $date->addMonth()->format('d M Y');
                        break;
                    case 3:
                       $nestedData['frequency']= "Quarterly";
                        $date = Carbon::parse($term->start_date); // Parse the date string into a Carbon instance

// Add 7 days to the date
$next_run_date = $date->addQuarters(1)->format('d M Y');
                        break;
                    case 4:
                       $nestedData['frequency']= "Yearly";
                        $date = Carbon::parse($term->start_date); // Parse the date string into a Carbon instance

// Add 7 days to the date
$next_run_date = $date->copy()->addYear()->format('d M Y');
                        break;
                    
                }
                
                 $nestedData['date']=$next_run_date;
                   $nestedData['scheduled_by']= $user->name.' '.$user->last_name;
                      $nestedData['type']=$type;
                   if(!empty($receipients) && count($receipients)>0){
                     $nestedData['receipient']= '<p>Receiver(s):</p>';

                    foreach($receipients as $r){
                         $nestedData['receipient'].= '<span class="badge badge-info">'.$r->name.' '.$r->last_name.'</span>';

                    }
                   }
                   else{
                     $nestedData['receipient']="";
                   }
               
                switch ($term->attach_as) {
                    case 1:
                   $nestedData['file']= 'PDF';
                        break;
                    
                    case 2:
                        $nestedData['file']= 'Excel';
                        break;
                }
                
                if($term->status=='active'){
                 $nestedData['status']='<span class="badge badge-success">'.$term->status.'</span>';
                 $nestedData['action']="<a class='btn btn-success btn-sm'  name='deactivate' id='$term->id' onclick='Deactivate(this.id,this.name)' ><i class='fa fa-pause'></i> Deactivate</a>";
                 
                }
                else{
                   $nestedData['status']= '<span class="badge badge-danger">'.$term->status.'</span>'; 
                    $nestedData['action']="<a class='btn btn-warning btn-sm' type='button'  id='$term->id' name='activate' onclick='Deactivate(this.id,this.name)'><i class='fa fa-check'></i> Activate</a>";
                  
                }
       $nestedData['delete']="<a class='btn btn-danger btn-sm'   id='$term->id' onclick='DeleteSchedule(this.id)'><i class='fa fa-trash'></i></a>";
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
    public function Coldshow(){
$data['users']=User::where('laboratory_id',auth()->user()->laboratory_id)->get();
        return view('cold.reports.schedule.show',$data);
    }
}