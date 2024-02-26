<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Laboratory;
use App\Models\LaboratorySection;
use App\Models\ReceivedItem;
use App\Models\Inventory;
use App\Models\ItemTemp;
use App\Models\Issue;
use App\Models\Requisition;
class SectionHeadController extends Controller
{
    //

    public function home(){
        $lab=Laboratory::where('id',auth()->user()->laboratory_id)->first();
        $data['laboratory']=$lab;
        $section=LaboratorySection::where('id',auth()->user()->section_id)->first();
        $data['section']=$section;
        $data['lab_name']=$lab->lab_name;
        $data['section_name']=$section->section_name;
        $data['item']=Inventory::where([['section_id','=',auth()->user()->section_id],
            ['expiry_date','>',date('Y-m-d')]])->count();
        $data['users']=User::where([['id','=',auth()->user()->laboratory_id],['section_id','=',auth()->user()->section_id]])->count();
        return view('sectionhead.index',$data);
    }
}
