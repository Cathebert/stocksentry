<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemMail;
use DB;
class SystemMailController extends Controller
{
    //
    public function show(){
        $data['mails']=SystemMail::get();

        return view('mail.show');
    }

public function load(Request $request){
    
   $columns = array(
            0=>'id',
            1=>'date',
            2=>'lab',
            3=>'subject',
            4=>'type',
            
        );

    $totalData = SystemMail::count();
    



            $totalRec = $totalData;
          // $totalData = DB::table('appointments')->count();

          $limit = $request->input('length');
          $start = $request->input('start');
          $order = $columns[$request->input('order.0.column')];
          $dir = $request->input('order.0.dir');

           $search = $request->input('search.value');

            $terms = DB::table('system_mails as s')
            ->join('laboratories as l','l.id','=','s.lab_id')
            ->where(function ($query) use ($search){
                  return  $query->where('s.type', 'LIKE', "%{$search}%");
                 
                      
                     
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


                $nestedData['id']=$x;
                $nestedData['date']=date('d, M Y',strtotime($term->date));
                $nestedData['lab']= $term->lab_name;
                $nestedData['subject']= $term->subject;
                $nestedData['type']= $term->type;
                
    
               
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
}
