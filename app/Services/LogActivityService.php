<?php
 namespace App\Services;
use Carbon\Carbon;
use DB;
use Request;
use App\Models\LogActivity as ModelLogActivity ;
 class LogActivityService{


public static function saveToLog($subject,$description,$level){
	//dd(Request::fullUrl());

$log = [];
    	$log['subject'] = $subject;
		$log['description']=$description;
		$log['performed_by']= auth()->user()->first_name.''.auth()->user()->last_name;
		$log['level']=$level;
    	$log['url'] = Request::fullUrl();
    	$log['method'] = Request::method();
    	$log['ip'] = Request::ip();
    	$log['agent'] = Request::header('user-agent');
    	$log['user_id'] = auth()->check() ? auth()->user()->id : 1;
    	ModelLogActivity::create($log);

    }

      public static function logActivityLists()
    {
    	return  ModelLogActivity::latest()->get();
    }
 }
