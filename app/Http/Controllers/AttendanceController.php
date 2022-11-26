<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DateTime;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $dt = new Carbon();
        // dd($dt->addDay(1));
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
        $date = new Carbon();
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
        $date = new Carbon();
        $dt = new Carbon();
        $attendance = Attendance::where('user_id', $user->id)->where('date', $dt->format('Y-m-d'))->latest()->first(); //$attendanceが取得できなかった場合の分岐を考えるべき？
        
        $work_start = TRUE;
        $work_end = FALSE;
        $rest_start = FALSE;
        $rest_end = FALSE;
        //$attendanceない場合の分岐を書く
        
        
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
        $dt = new Carbon();
        $attendances = Attendance::where('date', $dt->format('Y-m-d'))->get();
        
        foreach($attendances as $attendance){
            $rest_total = 0;
            $rests = Rest::where('attendance_id',$attendance->id)->get();
            foreach($rests as $rest){
                $rest_start = new Carbon($rest->started_at);
                $rest_finish = new Carbon($rest->finished_at);
                $rest_diff = $rest_start->diffInSeconds($rest_finish);
                $rest_total = $rest_total + $rest_diff; //休憩時間の合計算出(int)
            }
            //ここでtotalを時間に変換が必要
            $rest_hours = (int)($rest_total / 3600);
            $rest_minutes = (int)($rest_total / 60);
            $rest_seconds = (int)($rest_total % 60);
            //ここでstrに変換？
            //時、分、秒のそれぞれを場合分け、2桁ならそのまま変換、１桁なら０を前に着けて変換
            if($rest_hours < 10) {
                $rest_hours_s = '0'.(string)$rest_hours;
            } else{
                $rest_hours_s = (string)$rest_hours;
            }

            if($rest_minutes < 10) {
                $rest_minutes_s = '0'.(string)$rest_minutes;
            } else{
                $rest_minutes_s = (string)$rest_minutes;
            }

            if($rest_seconds < 10) {
                $rest_seconds_s = '0'.(string)$rest_seconds;
            } else{
                $rest_seconds_s = (string)$rest_seconds;
            }

            //strからtimeに
            $attendance->rest_sum = date('H:i:s', strtotime($rest_hours_s.$rest_minutes_s.$rest_seconds_s));
            //$attendance->rest_sum = $total;
            
            //ここで勤務時間を開始時間、終了時間、休憩時間から算出
            $attendance_start = new Carbon($attendance->started_at);
            $attendance_finish = new Carbon($attendance->finished_at);
            $work_diff = $attendance_start->diffInSeconds($attendance_finish);
            $work_total = $work_diff - $rest_total;

            //work_totalの変換
            $work_hours = (int)($work_total / 3600);
            $work_minutes = (int)($work_total / 60);
            $work_seconds = (int)($work_total % 60);

            if($work_hours < 10) {
                $work_hours_s = '0'.(string)$work_hours;
            } else{
                $work_hours_s = (string)$work_hours;
            }

            if($work_minutes < 10) {
                $work_minutes_s = '0'.(string)$work_minutes;
            } else{
                $work_minutes_s = (string)$work_minutes;
            }

            if($work_seconds < 10) {
                $work_seconds_s = '0'.(string)$work_seconds;
            } else{
                $work_seconds_s = (string)$work_seconds;
            }

            $attendance->work_sum = date('H:i:s', strtotime($work_hours_s.$work_minutes_s.$work_seconds_s));
        }
        //dd($attendances);
        
        $param = [
            'attendances' => $attendances,
            'dt' => $dt->format('Y-m-d'),
        ];
        return view('attendances',$param);
    }
}
