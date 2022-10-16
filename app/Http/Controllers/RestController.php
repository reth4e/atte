<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;

class RestController extends Controller
{
    public function start()
    {
        
        return view('index', $param);
    }

    public function end()
    {
        
        return view('index', $param);
    }
}
