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
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();
        $rest->attendance_id = $attendance->id; //error発生  ()のせい？
        //error attendance->id=null
        $rest->started_at = date_format($date , 'H:i:s');
        $rest->save();
        $param = ['user' => $user,];
        return view('index',$param);
    }

    public function end()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();
        $date = new DateTime();
        $rest = Rest::where('attendance_id', $attendance->id)->latest()->first();
        //error undefined variable $attendance
        $rest->update([
            'finished_at' => date_format($date , 'H:i:s')
        ]);
        $param = ['user' => $user,];
        return view('index',$param);
    }
    //↑2つ今週
}
