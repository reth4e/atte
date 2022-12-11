@extends('layouts.default')
@section('main')
    <div class="wrapper">
      <h3 class="greeting">{{$user->name}}さんお疲れ様です !</h3>
      <div class="buttons">
        <div class="work-button">
          <!-- ここにifで分岐、押せないボタンを作る -->
          @if($work_start)
            <form action="/attendance/start" method="get" class="work-start">
              <button class="btn" id="work-start">勤務開始</button>
            </form>
          @else
            <div class="work-start no_push">
              <p>勤務開始</p>
            </div>
          @endif

          @if($work_end)
            <form action="/attendance/end" method="get" class="work-end">
              <button class="btn" id="work-end">勤務終了</button>
            </form>
          @else
            <div class="work-end no_push">
              <p>勤務終了</p>
            </div>
          @endif
        </div>
        
        <div class="rest-button">
          @if($rest_start)
            <form action="/rest/start" method="get" class="rest-start">
              <button class="btn" id="rest-start">休憩開始</button>
            </form>
          @else
            <div class="rest-start no_push">
              <p>休憩開始</p>
            </div>
          @endif

          @if($rest_end)
            <form action="/rest/end" method="get" class="rest-end">
              <button class="btn" id="rest-end">休憩終了</button>
            </form>
          @else
            <div class="rest-end no_push">
              <p>休憩終了</p>
            </div>
          @endif
        </div>
      </div>
    </div>
@endsection