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
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();
        $rest = Rest::where('attendance_id', $attendance->id)->latest()->first();
        if($attendance->started_at && $attendance->finished_at){
            $work_start = TRUE;
            $rest_start = FALSE;
            $rest_end = FALSE;
        }elseif($attendance->started_at && !$attendance->finished_at){
            $work_start = FALSE;
            if($rest->started_at && $rest->finished_at){
                $rest_start = TRUE;
                $rest_end = FALSE;
            }elseif($rest->started_at && !$rest->finished_at){
                $rest_start = FALSE;
                $rest_end = TRUE;
            }
        }

        $param = ['user' => $user,
            'work_start' => $work_start,
            'rest_start' => $rest_start,
            'rest_end' => $rest_end
        ];
        return view('index',$param);
    }
//indexはボタン状態の保持に使う ↑を主に今週やる10/24
    public function start()
    {
        $user = Auth::user();
        $date = new DateTime();
        $attendance = new Attendance;
        //user_idは自動で代入？
        // $attendance->user_id = $user->id;
        // $attendance->date = date_format($date , 'Y-m-d');
        // $attendance->started_at = date_format($date , 'H:i:s');
        // //token削除は必要？
        // $attendance->save();
        $attendance->create([
            'user_id' => $user->id,
            'date' => date_format($date , 'Y-m-d'),
            'started_at' => date_format($date , 'H:i:s')
        ]);
        $param = ['user' => $user,];
        return view('index',$param);
    }

    public function end()
    {
        $user = Auth::user();
        //毎回Auth::user()入れる？
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
//↑３つ今週やる 10/16
    public function attendances()
    {
        
        return view('attendances');
    }
}
