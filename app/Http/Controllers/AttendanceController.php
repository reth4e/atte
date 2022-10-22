<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;

use Illuminate\Support\Facades\Auth;
use DateTime;

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
        $date = new DateTime();
        $attendance = new Attendance;
        //user_idは自動で代入？
        $attendance->user_id = $user->id;
        $attendance->date = date_format($date , 'Y-m-d');
        $attendance->started_at = date_format($date , 'H:i:s');
        //token削除は必要？
        $attendance->save();
        $param = ['user' => $user,];
        return view('index',$param);
    }

    public function end()
    {
        $user = Auth::user();
        $date = new DateTime();
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();
        // $attendance->finished_at = date_format($date , 'H:i:s');
        // Attendance::where('user_id', $user->id)->latest()->first()->update($attendance);
        //↑のやり方ではarray関連のエラーが起きた
        $attendance->update([
            'finished_at' => date_format($date , 'H:i:s')
        ]);
        //model Attendance.phpのfillableにfinished_atを入れなければならない
        $param = ['user' => $user,];
        return view('index',$param);
    }
//↑３つ今週やる
    public function attendances()
    {
        
        return view('attendances');
    }
}
