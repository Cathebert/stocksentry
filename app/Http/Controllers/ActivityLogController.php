<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LogActivityService;
use App\Models\LogActivity;
class ActivityLogController extends Controller
{
    //

public function logActivity()
    {
        $logs = LogActivityService::logActivityLists();
        return view('log.logActivity',compact('logs'));
    }
    
public function deleteLog(Request $request){
    //dd($request);
$log=LogActivity::find($request->id);
$log->delete();
return response()->json([
    'message'=>"Deleted"
]);
}
}
