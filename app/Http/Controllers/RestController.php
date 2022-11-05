<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;

use Illuminate\Support\Facades\Auth;
use DateTime;

class RestController extends Controller
{
    public function start()
    {
        $user = Auth::user();
        $date = new DateTime();
        $rest = new Rest();
        $dt = new DateTime();
        $dt->format('Y-m-d');
        $attendance = Attendance::where('user_id', $user->id)->where('date', $dt->format('Y-m-d'))->latest()->first(); //分岐？
        //finished_atの条件をつける
        $rest->attendance_id = $attendance->id; //error発生  ()のせい？
        //error attendance->id=null
        $rest->started_at = date_format($date , 'H:i:s');
        $rest->save();
        $work_start = FALSE;
        $work_end = FALSE;
        $rest_start = FALSE;
        $rest_end = TRUE;
        $param = ['user' => $user, 
            'work_start' => $work_start,
            'work_end' => $work_end,
            'rest_start' => $rest_start,
            'rest_end' => $rest_end,
        ];
        return view('index',$param);
    }

    public function end()
    {
        $user = Auth::user();
        $dt = new DateTime();
        $dt->format('Y-m-d');
        $attendance = Attendance::where('user_id', $user->id)->where('date', $dt->format('Y-m-d'))->latest()->first(); //分岐？
        //finished_atの条件をつける
        $date = new DateTime();
        $rest = Rest::where('attendance_id', $attendance->id)->latest()->first();
        //error undefined variable $attendance
        $rest->update([
            'finished_at' => date_format($date , 'H:i:s')
        ]);
        $work_start = FALSE;
        $work_end = TRUE;
        $rest_start = TRUE;
        $rest_end = FALSE;
        $param = ['user' => $user, 
            'work_start' => $work_start,
            'work_end' => $work_end,
            'rest_start' => $rest_start,
            'rest_end' => $rest_end,
        ];
        return view('index',$param);
    }
    //↑2つ今週 10/15
}
