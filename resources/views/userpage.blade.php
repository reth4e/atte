<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atte</title>
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/users.css') }}">
</head>

<body>
    <header class="header">
        <h2 class="header-title">
            Atte
        </h2>
        <ul class="header-link-list">
            <li class="header-link">
                <a href="/">ホーム</a>
            </li>
            <li class="header-link">
                <a href="/attendance/0">日付一覧</a>
            </li>
            <li class="header-link">
                <a href="/users">ユーザー一覧</a>
            </li>
            <li class="header-link">
                <form action="/logout" method="post" class="form-logout">
                    @csrf
                    <input class="btn-logout" type="submit" value="ログアウト">
                </form>
            </li>
        </ul>
    </header>

    <main>
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
                    @if($attendance->rest_sum != NULL)
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
        
    </main>
    <footer class="footer">
        <small class="footer-logo">Atte,inc.</small>
    </footer>
</body>
</html>