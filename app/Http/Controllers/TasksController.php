<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TasksController extends Controller
{
    //post a task to the selected category
    public function postTask($category_id){
        return view('post_task');
    }

    //go to the task details
    public function taskDetails($task_id){
        
    }
}
