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
        $dt = new DateTime();
        $attendance = Attendance::where('user_id', $user->id)->where('date', $dt->format('Y-m-d'))->latest()->first(); //$attendanceの取得に失敗、$dt->formatにし忘れ
        $work_start = FALSE;
        $work_end = FALSE;
        if($attendance){//すでに本日勤務開始している
            $rest = Rest::where('attendance_id', $attendance->id)->latest()->first();
            
        //started_atはヌルにならないので条件変更
            if(!$attendance->finished_at){//勤務中
                $work_end = TRUE;
                //error attempt to read property on NULL
                
                if($rest){//すでに休憩ボタンを押した
                    if(!$rest->finished_at){//休憩中
                        $work_end = FALSE;
                        $rest_start = FALSE;
                        $rest_end = TRUE;
                    }else{//休憩外
                        $rest_start = TRUE;
                        $rest_end = FALSE;
                    }
                }else{//まだその勤務内に休憩をしていない場合
                    $rest_start = TRUE;
                    $rest_end = FALSE;
                }
                
            }elseif($attendance->finished_at){//勤務時間外
                $work_start = TRUE;
                $rest_start = FALSE;
                $rest_end = FALSE;
            }
        }else{//新規登録者、勤務中のまま終了せず日付変更時の処理、本日の勤務開始前
            $work_start = TRUE;
            $rest_start = FALSE;
            $rest_end = FALSE;
        }
        $param = ['user' => $user,
            'work_start' => $work_start,
            'work_end' => $work_end,
            //undefined rest_st,end
            'rest_start' => $rest_start,
            'rest_end' => $rest_end,
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
        //↑の分岐をもっとシンプルに 10/30
    }

    public function end()
    {
        $user = Auth::user();
        //毎回Auth::user()入れる？
        $date = new DateTime();
        $dt = new DateTime();
        $dt->format('Y-m-d');
        $attendance = Attendance::where('user_id', $user->id)->where('date', $dt->format('Y-m-d'))->latest()->first(); //$attendanceが取得できなかった場合の分岐を考えるべき？
        //$attendanceない場合の分岐を書く
        $work_start = TRUE;
        $work_end = FALSE;
        $rest_start = FALSE;
        $rest_end = FALSE;
        // $attendance->finished_at = date_format($date , 'H:i:s');
        // Attendance::where('user_id', $user->id)->latest()->first()->update($attendance);
        //↑のやり方ではarray関連のエラーが起きた
        $attendance->update([
            'finished_at' => date_format($date , 'H:i:s')
        ]);
        //model Attendance.phpのfillableにfinished_atを入れなければならない
        $param = ['user' => $user, 
            'work_start' => $work_start,
            'work_end' => $work_end,
            'rest_start' => $rest_start,
            'rest_end' => $rest_end,
        ];
        return view('index',$param);
    }
//↑３つ今週やる 10/16
    public function attendances()
    {
        
        return view('attendances');
    }
}
