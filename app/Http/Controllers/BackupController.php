<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BackupSchedule;
use App\Models\SystemBackup;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Laboratory;
use ZipArchive;

use DB;
use Carbon\Carbon;
class BackupController extends Controller
{
    public function showBackUp(){
          $lab=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();
    
   
 
$data['lab_name']='Logged Into: '.$lab->lab_name;

$data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])->get();
        return view('pdf.reports.scheduled.backup',$data);
}
public function scheduleBackUp(Request $request){
try{
DB::beginTransaction();
$back=new BackupSchedule();
$back->frequency=$request->frequency;
$back->scheduled_by=auth()->user()->id;
$back->receiver=$request->back_up_receiver;
$back->status="active";
$back->created_at=now();
$back->save();
DB::commit();

 return redirect()->back()->with('success',"Backup scheduled Successfully");  
}
catch(Exception $e){
DB::rollback();
 return redirect()->back()->with('error',"Failed to schedule backup");  
}
}
public function loadSheduledBackups(Request $request){
 $columns = array(
            0=>'id',
            1=>'frequency',
            2=>'receipient',
            3=>'status',
            4=>'scheduled_by',
            5=>'action',
            6=>'delete',
            
        );

    $totalData = BackupSchedule::count();
    



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('backup_schedules as s')
          
            ->select(
                's.id as id',
                's.frequency',
                's.scheduled_by',
                's.receiver', 
                's.status',
                's.created_at',
              
            )
            ->where(function ($query) use ($search){
                  return  $query->where('s.id', 'LIKE', "%{$search}%");
                 
                      
                     
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
$user=User::where('id',$term->	scheduled_by)->select('name','last_name')->first();
$receipients=User::where('id',$term->receiver)->get();

                $nestedData['id']=$x;
              
              

              
                
               
                switch ($term->frequency) {
                    case 1:
                       $nestedData['frequency']= "Daily"
                       ;
                      // $date = Carbon::parse($term->start_date); // Parse the date string into a Carbon instance

// Add 7 days to the date
//$next_run_date = $date->addDays(7)->format('d M Y');
                        break;
                    case 2:
                       $nestedData['frequency']= "Weekly";
                        

// Add 7 days to the date
//$next_run_date = $date->addMonth()->format('d M Y');
                        break;
                    case 3:
                       $nestedData['frequency']= "Monthly";
                        //$date = Carbon::parse($term->start_date); // Parse the date string into a Carbon instance

// Add 7 days to the date
//$next_run_date = $date->addQuarters(1)->format('d M Y');
                        break;
                    case 4:
                       $nestedData['frequency']= "Yearly";
                       // $date = Carbon::parse($term->start_date); // Parse the date string into a Carbon instance

// Add 7 days to the date
//$next_run_date = $date->copy()->addYear()->format('d M Y');
                        break;
                    
                }
                
               
                   $nestedData['scheduled_by']= $user->name.' '.$user->last_name;
                   
                   if(!empty($receipients) && count($receipients)>0){
                     $nestedData['receipient']= '<p>Receiver(:</p>';

                    foreach($receipients as $r){
                         $nestedData['receipient'].= '<span class="badge badge-info">'.$r->name.' '.$r->last_name.'</span>';

                    }
                   }
                   else{
                     $nestedData['receipient']="";
                   }
               
              
                
               if($term->status=='active'){
                 $nestedData['status']='<span class="badge badge-success">'.$term->status.'</span>';
                 $nestedData['action']="<a class='btn btn-success btn-sm'  name='deactivate' id='$term->id' onclick='DeactivateBackup(this.id,this.name)' ><i class='fa fa-pause'></i> Deactivate</a>";
                 
                }
                else{
                   $nestedData['status']= '<span class="badge badge-danger">'.$term->status.'</span>'; 
                    $nestedData['action']="<a class='btn btn-warning btn-sm' type='button'  id='$term->id' name='activate' onclick='DeactivateBackup(this.id,this.name)'><i class='fa fa-check'></i> Activate</a>";
                  
                }
       $nestedData['delete']="<a class='btn btn-danger btn-sm'   id='$term->id' onclick='DeleteBackup(this.id)'><i class='fa fa-trash'></i></a>";
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
   BackupSchedule::where('id',$request->id)->update([
        'status'=>'inactive'
    ]);
    return response()->json([
        'message'=>"Backup Schedule Deactivated Successfully",
        'error'=>false
    ]);
    }
    else{
    BackupSchedule::where('id',$request->id)->update([
        'status'=>'active',
       
    ]);
    return response()->json([
        'message'=>"Backup Schedule Activated Successfully",
        'error'=>false
    ]);
    
    }
}

 
   public function delete(Request $request){
     BackupSchedule::find($request->id)->delete();
    return response()->json([
        'message'=>"Backup Schedule Deleted Successfully",
        'error'=>false
    ]);
}

 public function viewBackUps(){
//$data['users']=User::where([['laboratory_id','=',auth()->user()->laboratory_id],['authority','=',1]])->get();
        return view('inventory.modal.view_backups');
}


public function loadBackups(Request $request){
 $columns = array(
            0=>'id',
            1=>'name',
            2=>'type',
            3=>'backup_by',
            4=>'scheduled_by',
            5=>'created_at',
            5=>'action',
          
            
        );

    $totalData = SystemBackup::count();
    



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('system_backups as s')
          
            ->select(
                's.id as id',
                's.name',
                's.back_type',
                's.backup_by', 
                's.scheduled_by',
                's.created_at',
              
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
            
            if($term->scheduled_by!=NULL){
$user=User::where('id',$term->scheduled_by)->select('name','last_name')->first();
$scheduler=$user->name.' '.$user->last_name;
}
else{
$scheduler="";
}

                $nestedData['id']=$x;
                
                  $nestedData['name']=$term->name;
                   $nestedData['type']=$term->back_type;
                
                   
                  
                     $nestedData['backup_by']=$term->backup_by;
                
                  $nestedData['scheduled_by']= $scheduler;
              
                  $nestedData['created_at']= date('d,M Y',strtotime($term->created_at));
            
                 $nestedData['action']="<a class='btn btn-success btn-sm'   id='$term->id' onclick='RestoreBackup(this.id)' ><i class='fa fa-refresh'></i> Restore</a> | ";
                 
                  $nestedData['action'].=" <a class='btn btn-primary btn-sm'   id='$term->id' onclick='downloadBackup(this.id)' ><i class='fa fa-download'></i> Download</a> | ";
          $nestedData['action'].=" <a class='btn btn-danger btn-sm'   id='$term->id' onclick='deleteBackup(this.id)' ><i class='fa fa-trash'></i> Delete</a> ";
               
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
public function generateBackup(Request $request){

if (! Storage::exists('backup')) {
            Storage::makeDirectory('backup');
        }
        $rand=mt_rand(0, 999999);
 
        $filename = "backup-" . Carbon::now()->format('Y-m-d').'_' .$rand. "gz";
    
        $command = "mysqldump --user=" . env('DB_USERNAME') ." --password=" . env('DB_PASSWORD')
                . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') 
                . "   > " . storage_path() . "/app/backup/" . $filename;
 
        $returnVar = NULL;
        $output  = NULL;
 
        exec($command, $output, $returnVar);
        $full_path=storage_path() . "/app/backup/" . $filename;
        $name="backup-" . Carbon::now()->format('Y-m-d').'_'.$rand ;
        $fileSize=1024;
       $this->updateBackupInformation($name,$filename,$fileSize);
        
        
        
    return response()->json([
    "path"=>$name,
    "url"=>route('sy_backups.download',['name'=> $filename])
]);



}
   protected function updateBackupInformation($file_name,$internal_name,$fileSize){
    
    $backup=new SystemBackup();
    $backup->name=$file_name;
    $backup->internal_name=$internal_name;
    $backup->file_size=$fileSize;
    $backup->back_type='manual';
    $backup->scheduled_by=NULL;
    $backup->backup_by=auth()->user()->name.' '.auth()->user()->last_name;
    $backup->created_at	=now();
    $backup->updated_at=NULL;
    $backup->save();
    


    
    }
    
    public function downloadBackupFile($name){
    
    $path=storage_path() . "/app/backup/" .$name;
$name="backup-" . Carbon::now()->format('Y-m-d') . ".gz";
$headers = [
  'Content-type' => 'application/vnd.ms-excel', 
  'Content-Disposition' => sprintf('attachment; filename="%s"', $name),
  'Content-Length' => strlen($path)

]; 
return response()->download($path,$name, $headers);

    
    
    
    }
    
 public function createDownload(Request $request){
  $id=$request->id;
  $back=SystemBackup::where('id',$id)->select('internal_name')->first();
  
  $filename=$back->internal_name;
  
    return response()->json([
   
    "url"=>route('sy_backups.download',['name'=> $filename])
]);

  
  
  }
  public function deleteBackup(Request $request){
  $back=SystemBackup::where('id',$request->id)->select('internal_name')->first();
  $name= $back->internal_name;
  $path=storage_path() . "/app/backup/" .$name;
   if(file_exists( $path)){
            unlink( $path);
        }
  
  SystemBackup::find($request->id)->delete();
    return response()->json([
        'message'=>"Backup  Deleted Successfully",
        'error'=>false
    ]);
  
  
  
  }
  
  public function clearAllBackup(Request $request){
  
  SystemBackup::truncate();
   $path=storage_path() . "/app/backup";
 
  $this->removeDirectory($path);
     return response()->json([
        'message'=>"Backups cleared",
        'error'=>false
    ]);
  }
  protected function removeDirectory($path) {
    $files = glob($path . '/*');
    foreach ($files as $file) {
        is_dir($file) ? removeDirectory($file) : unlink($file);
    }
   // rmdir($path);
    return;
}

public function restoreBackup(Request $request){
 try{
       if ($request->hasFile('fileToUpload')) {
        $file_name = $request->file('fileToUpload')->getClientOriginalName();
     $type=$request->file('fileToUpload')->getClientOriginalExtension();
     if($type=='gz'){
      $request->file('fileToUpload')->move(public_path(),   $file_name);
      
     $filename=public_path().DIRECTORY_SEPARATOR.$file_name;
     
 /* $is_extracted=$this->getExtractedFile($filename);
  if($is_extracted){
  
  }*/
        $command = "mysql --user=" . env('DB_USERNAME') ." --password=" . env('DB_PASSWORD')
                . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') 
                . "<" .$filename;
 
        $returnVar = NULL;
        $output  = NULL;
 
        exec($command, $output, $returnVar);
        //unlink($filename);
         return response()->json([
      'message'=>"Database  restored Successfully",
        'error'=>false
        ]);
     }
     else{
     return response()->json([
      'message'=>"This is not a backup file",
        'error'=>true
     
     ]);
     }
     }
     else{
     $back=SystemBackup::where('id',$request->id)->select('internal_name')->first();
     $filname=$back->internal_name;
     $path=storage_path() . "/app/backup/".$filname;
     
     if(file_exists( $path)){
              $command = "mysql --user=" . env('DB_USERNAME') ." --password=" . env('DB_PASSWORD')
                . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') 
                . "< " . $path;
 
        $returnVar = NULL;
        $output  = NULL;
 
        exec($command, $output, $returnVar);  

         return response()->json([
      'message'=>"Database  restored Successfully",
        'error'=>false
        ]);
        }
        else{
        
         return response()->json([
      'message'=>"It seems file has been moved",
        'error'=>false
        ]);
        }
     }
     
     }
     catch(Exception $e){
     return response()->json([
      'message'=>$e->getMessage(),
        'error'=>true
     
     ]);
     }


}
protected function getExtractedFile($zippedFile){

try{
  $zip = new ZipArchive();
  $res = $zip->open($zippedFile);
if($res==TRUE ){
 $zip->extractTo(public_path()); 
 $zip->close();
 return true;
}
else{
return false;
}
}
catch(Exception $e){
return false;

}

}
}