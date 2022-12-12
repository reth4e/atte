@extends('layouts.default')

@section('main')
    <div class="wrapper_attendances">
      <div class="flex">
        <div>
          <a href="{!! '/attendance/' . ($num - 1) !!}" class="date_link"><</a>
        </div>
        <h3 class="date">{{$dt}}</h3>
        <div>
          <a href="{!! '/attendance/' . ($num + 1) !!}" class="date_link">></a>
        </div>
      </div>
      <!-- ここでattendances->dateとするとエラー Property[date] does not exist on the Eloquent builder instance. -->
      <table class="attendance_table">
        <tr>
          <th>名前</th>
          <th>勤務開始</th>
          <th>勤務終了</th>
          <th>休憩時間</th>
          <th>勤務時間</th>
        </tr>
        @foreach ($attendances as $attendance)
          <tr>
            <td>{{$attendance->user->name}}</td>
            <td>{{$attendance->started_at}}</td>
            @if($attendance->finished_at != NULL)
            <td>{{$attendance->finished_at}}</td>
            <!-- どうしてもfinished_atに時間を入れられない場合の処置 -->
            @else
            <td>23:59:59</td>
            @endif
            @if($attendance->rest_sum != NULL)
            <td>{{$attendance->rest_sum}}</td>
            @else
            <td>00:00:00</td>
            @endif
            <!-- ここにも処置が必要 -->
            <td>{{$attendance->work_sum}}</td>
          </tr>
        @endforeach
      </table>
      {{ $attendances->links('pagination::bootstrap-4') }}
    </div>
    
@endsection