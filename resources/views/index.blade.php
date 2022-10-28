<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Atte</title>
  <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
  <link rel="stylesheet" href="{{ asset('css/index.css') }}">
</head>

<body>
  <header class="header">
    <h2 class="header-title">
      Atte
    </h2>
    <ul class="header-link-list">
      <li class="header-link">
        <a href="/attendance">ホーム</a>
      </li>
      <li class="header-link">
        <a href="/attendance/attendances">日付一覧</a>
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
      <h3 class="greeting">{{$user->name}}さんお疲れ様です !</h3>
      <div class="buttons">
        <div class="work-button">
          <!-- ここに@ifで分岐、押せないボタンを作る -->
          <form action="/attendance/start" method="get" class="work-start">
            <button class="btn" id="work-start">勤務開始</button>
          </form>
          <form action="/attendance/end" method="get" class="work-end">
            <button class="btn" id="work-end">勤務終了</button>
          </form>
        </div>
        <div class="rest-button">
          <form action="/rest/start" method="get" class="rest-start">
            <button class="btn" id="rest-start">休憩開始</button>
          </form>
          <form action="/rest/end" method="get" class="rest-end">
            <button class="btn" id="rest-end">休憩終了</button>
          </form>
          <!-- ↑のボタンの場合分けをやる 10/24 -->
        </div>
      </div>
    </div>
  </main>

  <footer class="footer">
    <small class="footer-logo">Atte,inc.</small>
  </footer>
</body>
</html>