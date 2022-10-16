<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        
        return view('index', $param);
    }

    public function start()
    {
        
        return view('index', $param);
    }

    public function end()
    {
        
        return view('index', $param);
    }

    public function attendances()
    {
        
        return view('attendances', $param);
    }
}
