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
            9=>'action'
            
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
                 $nestedData['type']=$term->type;
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
                   if(!empty($receipients) && count($receipients)>0){
                     $nestedData['receipient']= '<p>Receipients:</p>';

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
                }
                else{
                   $nestedData['status']= '<span class="badge badge-danger">'.$term->status.'</span>'; 
                }
    $nestedData['action']="<a class='btn btn-danger btn-sm'   id='$term->id' onclick='Deactivate(this.id)' ><i class='fa fa-hand-paper-o'></i>Stop</a>";
               
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
    ScheduleReport::where('id',$request->id)->update([
        'status'=>'inactive'
    ]);
    return response()->json([
        'message'=>"Schedule Deactivated Successfully",
        'error'=>false
    ]);
}
public function save(Request $request){

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
        $name="Issue Report";
        $type="Issue";
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
}
