<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DateTime;

class AttendanceController extends Controller
{
    //勤怠管理ページ
    public function index()
    {
        $user = Auth::user();
        $dt = new Carbon();
        // dd($dt->addDay(1));
        // $a = new Carbon(NULL);
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
                
            }else{//勤務時間外
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

//勤務開始処理
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
    }

    //勤務終了処理
    public function end()
    {
        $user = Auth::user();
        //毎回Auth::user()入れる？
        $date = new Carbon();
        $dt = new Carbon();
        $attendance = Attendance::where('user_id', $user->id)->where('date', $dt->format('Y-m-d'))->latest()->first(); 
        
        $work_start = TRUE;
        $work_end = FALSE;
        $rest_start = FALSE;
        $rest_end = FALSE;
        $param = ['user' => $user, 
            'work_start' => $work_start,
            'work_end' => $work_end,
            'rest_start' => $rest_start,
            'rest_end' => $rest_end,
        ];

        //$attendanceない場合の分岐を書く(日跨ぎ時の処理)

        if(!$attendance){
            return view('index',$param);
        }
        
        // $attendance->finished_at = date_format($date , 'H:i:s');
        // Attendance::where('user_id', $user->id)->latest()->first()->update($attendance);
        //↑のやり方ではarray関連のエラーが起きた
        $attendance->update([
            'finished_at' => date_format($date , 'H:i:s')
        ]);
        //model Attendance.phpのfillableにfinished_atを入れなければならない
        
        return view('index',$param);
    }

//日付一覧ページ
    public function attendances(Request $request)
    {
        $num = $request->num;
        $dt = new Carbon();
        if ($num > 0){
            $date = $dt->addDays($num);
        } elseif ($num === 0){
            $date = $dt;
        } else {
            $date = $dt->subDays(-$num);
        }
        // $dt = new Carbon();
        $attendances = Attendance::where('date', $date->format('Y-m-d'))->paginate(5);
        
        foreach($attendances as $attendance){
            
            
            
            $attendance_start = new Carbon($attendance->started_at);
            $attendance_finish = new Carbon($attendance->finished_at);
            
            
            //日跨ぎ処理 numによって分岐
            if($attendance->finished_at === NULL && $num < 0){
                    $finish_datetime = $attendance_start->year.'-'.$attendance_start->month.'-'.$attendance_start->day.' 23:59:59';
                    $attendance_finish = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $finish_datetime,
                    );
                    $attendance->update([
                        'finished_at' => date_format($attendance_finish , 'H:i:s')
                    ]);
                
            }
            
            
            $rest_total = 0;
            $rests = Rest::where('attendance_id',$attendance->id)->get();
            foreach($rests as $rest){
                $rest_start = new Carbon($rest->started_at);
                $rest_finish = new Carbon($rest->finished_at);

                //日跨ぎ時の処理
                if($rest->finished_at === NULL && $num < 0){
                    $finish_resttime = $rest_start->year.'-'.$rest_start->month.'-'.$rest_start->day.' 23:59:59';
                    $rest_finish = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $finish_resttime,
                    );
                    $rest->update([
                        'finished_at' => date_format($rest_finish , 'H:i:s')
                    ]);
                }
                
                $rest_diff = $rest_start->diffInSeconds($rest_finish);
                $rest_total = $rest_total + $rest_diff; //休憩時間の合計算出(int)
            }
            //ここでtotalを時間に変換が必要
            $rest_hours = (int)($rest_total / 3600);
            $rest_minutes = (int)($rest_total % 3600 / 60);
            $rest_seconds = (int)($rest_total % 60);
            //ここでstrに変換？
            //時、分、秒のそれぞれを場合分け、2桁ならそのまま変換、１桁なら０を前に着けて変換
            if ($rest_hours < 10) {
                $rest_hours_s = '0'.(string)$rest_hours;
            } else {
                $rest_hours_s = (string)$rest_hours;
            }

            if ($rest_minutes < 10) {
                $rest_minutes_s = '0'.(string)$rest_minutes;
            } else{
                $rest_minutes_s = (string)$rest_minutes;
            }

            if($rest_seconds < 10) {
                $rest_seconds_s = '0'.(string)$rest_seconds;
            } else {
                $rest_seconds_s = (string)$rest_seconds;
            }

            //strからtimeに
            $attendance->rest_sum = date('H:i:s', strtotime($rest_hours_s.$rest_minutes_s.$rest_seconds_s));

            //ここで勤務時間を開始時間、終了時間、休憩時間から算出
            $work_diff = $attendance_start->diffInSeconds($attendance_finish);
            $work_total = $work_diff - $rest_total;

            

            //work_totalの変換 要書き換え
            $work_hours = (int)($work_total / 3600);
            $work_minutes = (int)($work_total % 3600 / 60);
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
            'dt' => $date->format('Y-m-d'),
            'num' => $num,
        ];
        return view('attendances',$param);
    }

    //ユーザー一覧ページ
    public function users()
    {
        $users = User::paginate(5);
        $param = [
            'users' => $users,
        ];
        return view('users',$param);
    }

    //ユーザーページ
    public function userPage(Request $request)
    {
        $id = $request->id;
        $user = User::where('id', $id)->first();
        $attendances = Attendance::where('user_id', $id)->paginate(5);
        $dt = new Carbon();

        foreach($attendances as $attendance){
            //日跨ぎ処理 startの日付と今日の日付との比較で分岐
            $attendance_start = new Carbon($attendance->started_at);
            $attendance_finish = new Carbon($attendance->finished_at);

            //要検証
            if($attendance->finished_at === NULL && $attendance->date < date_format($dt , 'Y-m-d')){
                $finish_datetime = $attendance_start->year.'-'.$attendance_start->month.'-'.$attendance_start->day.' 23:59:59';
                $attendance_finish = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $finish_datetime,
                );
                
                //今日以前のfinished_atがNULLのときupdate
                $attendance->update([
                        'finished_at' => date_format($attendance_finish , 'H:i:s')
                    ]);
            }

            $rest_total = 0;
            $rests = Rest::where('attendance_id',$attendance->id)->get();
            foreach($rests as $rest){
                $rest_start = new Carbon($rest->started_at);
                $rest_finish = new Carbon($rest->finished_at);

                if($rest->finished_at === NULL && $attendance->date < date_format($dt , 'Y-m-d')){
                    $finish_resttime = $rest_start->year.'-'.$rest_start->month.'-'.$rest_start->day.' 23:59:59';
                    $rest_finish = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $finish_resttime,
                    );
                    
                    //今日以前のfinished_atがNULLのときupdate
                    $rest->update([
                            'finished_at' => date_format($rest_finish , 'H:i:s')
                        ]);
            }

                $rest_diff = $rest_start->diffInSeconds($rest_finish);
                $rest_total = $rest_total + $rest_diff; //休憩時間の合計算出(int)
            }
            //ここでtotalを時間に変換が必要
            $rest_hours = (int)($rest_total / 3600);
            $rest_minutes = (int)($rest_total % 3600 / 60);
            $rest_seconds = (int)($rest_total % 60);
            //ここでstrに変換？
            //時、分、秒のそれぞれを場合分け、2桁ならそのまま変換、１桁なら０を前に着けて変換
            if ($rest_hours < 10) {
                $rest_hours_s = '0'.(string)$rest_hours;
            } else {
                $rest_hours_s = (string)$rest_hours;
            }

            if ($rest_minutes < 10) {
                $rest_minutes_s = '0'.(string)$rest_minutes;
            } else{
                $rest_minutes_s = (string)$rest_minutes;
            }

            if($rest_seconds < 10) {
                $rest_seconds_s = '0'.(string)$rest_seconds;
            } else {
                $rest_seconds_s = (string)$rest_seconds;
            }

            //strからtimeに
            $attendance->rest_sum = date('H:i:s', strtotime($rest_hours_s.$rest_minutes_s.$rest_seconds_s));
            //$attendance->rest_sum = $total;
            
            //ここで勤務時間を開始時間、終了時間、休憩時間から算出
            
            $work_diff = $attendance_start->diffInSeconds($attendance_finish);
            $work_total = $work_diff - $rest_total;

            //work_totalの変換
            $work_hours = (int)($work_total / 3600);
            $work_minutes = (int)($work_total % 3600 / 60);
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
        
        $param = [
            'user' => $user,
            'attendances' => $attendances,
            'id' => $id,
        ];
        return view('userPage',$param);
    }
}
