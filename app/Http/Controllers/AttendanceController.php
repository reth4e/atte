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
        $user = Auth::user();
        $param = ['user' => $user,];
        return view('index',$param);
    }
//indexはボタン状態の保持に使う
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
//↑３つ今週やる
    public function attendances()
    {
        
        return view('attendances');
    }
}
