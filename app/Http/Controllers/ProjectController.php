<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    //get the latest Project
    public function getLatestProjects(){
        $data['projects']=Project::where('status','active')->get();
        return view('projects',$data);

    }
    public function create(){
        return view('provider.layout.project.show');

    }
}
