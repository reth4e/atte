@extends('layouts.default')
@section('main')
        <div class="wrapper">
            <h3 class="name">{{$user->name}}</h3>
            <table class="users_table">
                <tr>
                    <th>日付</th>
                    <th>勤務開始</th>
                    <th>勤務終了</th>
                    <th>休憩時間</th>
                    <th>勤務時間</th>
                </tr>
            @foreach ($attendances as $attendance)
                <tr>
                    <td>{{$attendance->date}}</td>
                    <td>{{$attendance->started_at}}</td>
                    <td>{{$attendance->finished_at}}</td>
                    @if($attendance->rest_sum !== NULL)
                    <td>{{$attendance->rest_sum}}</td>
                    @else
                    <td>00:00:00</td>
                    @endif
                    <td>{{$attendance->work_sum}}</td>
                </tr>
            @endforeach
            </table>
        {{ $attendances->links('pagination::bootstrap-4') }}
        </div>
@endsection