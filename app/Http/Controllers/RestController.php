<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;
use Illuminate\Support\Facades\Auth;

class RestController extends Controller
{
    public function start()
    {
        $user = Auth::user();
        $param = ['user' => $user,];
        return view('index',$param);
    }

    public function end()
    {
        $user = Auth::user();
        $param = ['user' => $user,];
        return view('index',$param);
    }
    //↑2つ今週
}
