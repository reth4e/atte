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
    //勤怠管理ページ　indexはボタン状態の保持に使う
    public function index()
    {
        $user = Auth::user();
        $dt = new Carbon();
        //$attendanceはログインユーザーとIDが一致していて、操作日と同じ日の最新のレコードを取得する
        $attendance = Attendance::where('user_id', $user->id)->where('date', $dt->format('Y-m-d'))->latest()->first(); 

        //$work_start,$work_end,$rest_start,$rest_endは勤務状態を保持するための変数　view内でそれぞれの変数に対応するボタンが押せるかどうかの判定に使われる
        $work_start = FALSE;
        $work_end = FALSE;

        //勤務状態の判定
        if($attendance){//すでに本日勤務開始している
            $rest = Rest::where('attendance_id', $attendance->id)->latest()->first();
            
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
            'rest_start' => $rest_start,
            'rest_end' => $rest_end,
        ];
        return view('index',$param);
    }


//勤務開始処理
    public function start()
    {
        $user = Auth::user();
        $date = new Carbon();
        $attendance = new Attendance;
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
        $date = new Carbon();
        $attendance = Attendance::where('user_id', $user->id)->where('date', $date->format('Y-m-d'))->latest()->first(); 
        
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

        //$attendanceがない場合は日跨ぎ時の処理　アップデートせずにホームに戻る

        if(!$attendance){
            return view('index',$param);
        }
        
        
        $attendance->update([
            'finished_at' => date_format($date , 'H:i:s')
        ]);
        
        return view('index',$param);
    }

//日付一覧ページ
    public function attendances(Request $request)
    {
        $num = $request->num;
        $dt = new Carbon();
        //$numをパスパラメータとして渡し、その値によって閲覧ページの日付を変化させる $numが0より大きいときは閲覧日より後、0のときは閲覧日、0より小さいときは閲覧日より前の日のデータを閲覧する
        if ($num > 0){
            $date = $dt->addDays($num);
        } elseif ($num === 0){
            $date = $dt;
        } else {
            $date = $dt->subDays(-$num);
        }
        
        $attendances = Attendance::where('date', $date->format('Y-m-d'))->paginate(5);
        
        foreach($attendances as $attendance){
            //$attendance_start,$attendance_finishにはそれぞれ勤務開始、勤務終了時の時間を入れる　これらは勤務時間の合計の計算に用いる
            $attendance_start = new Carbon($attendance->started_at);
            $attendance_finish = new Carbon($attendance->finished_at);
            
            //日跨ぎ処理 レコードが閲覧日の前日以前で、勤務終了ボタンが押されずに日を跨いだ場合　
            if($attendance->finished_at === NULL && $num < 0){
                //$finish_datetimeに勤務開始した日の終わりの時間をstringの形で入れる
                
                $finish_datetime = $attendance_start->year.'-'.$attendance_start->month.'-'.$attendance_start->day.' 23:59:59';
                //$attendance_finishにdatetimeの形に変換した$finish_datetimeを代入する　その後$attendance->finished_atに$attendance_finishの値を時：分：秒の形で入れる
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

                //日跨ぎ時の処理　レコードが閲覧日の前日以前で、休憩終了が押されずに日を跨いだ場合
                if($rest->finished_at === NULL && $num < 0){
                    //$attendanceの場合と同様に、$restにもfinished_atのカラムに休憩開始時の日付の終わりの時間を入れる
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
                $rest_total = $rest_total + $rest_diff; //休憩時間(秒)の合計算出(int)
            }

            //rest_totalを時、分、秒に分割変換
            $rest_hours = (int)($rest_total / 3600);
            $rest_minutes = (int)($rest_total % 3600 / 60);
            $rest_seconds = (int)($rest_total % 60);
            //ここでstringに変換
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

            //strからtimeに $attendance->rest_sumが休憩時間の合計を表す
            $attendance->rest_sum = date('H:i:s', strtotime($rest_hours_s.$rest_minutes_s.$rest_seconds_s));

            //ここで勤務時間(秒)を開始時間、終了時間、休憩時間から算出
            $work_diff = $attendance_start->diffInSeconds($attendance_finish);
            $work_total = $work_diff - $rest_total;
            
            //work_totalを時、分、秒に分割変換
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

            //$attendance->work_sumが勤務時間の合計を表す
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
            $attendance_start = new Carbon($attendance->started_at);
            $attendance_finish = new Carbon($attendance->finished_at);

            //日跨ぎ処理 startの日付と今日の日付との比較で分岐
            if($attendance->finished_at === NULL && $attendance->date < date_format($dt , 'Y-m-d')){
                $finish_datetime = $attendance_start->year.'-'.$attendance_start->month.'-'.$attendance_start->day.' 23:59:59';
                $attendance_finish = Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $finish_datetime,
                );
                
                //閲覧日以前のfinished_atがNULLのときupdate
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
                    
                    //閲覧日以前のfinished_atがNULLのときupdate
                    $rest->update([
                            'finished_at' => date_format($rest_finish , 'H:i:s')
                        ]);
            }

                $rest_diff = $rest_start->diffInSeconds($rest_finish);
                $rest_total = $rest_total + $rest_diff; //休憩時間(秒)の合計算出(int)
            }

            //$rest_totalの時、分、秒への分割、変換
            $rest_hours = (int)($rest_total / 3600);
            $rest_minutes = (int)($rest_total % 3600 / 60);
            $rest_seconds = (int)($rest_total % 60);
            //ここでstrに変換
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
