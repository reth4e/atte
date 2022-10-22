<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
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
        <form action="/logout" method="post">
          @csrf
          <input class="btn btn-logout" type="submit" value="ログアウト">
        </form>
      </li>
    </ul>
  </header>

  <main>
    <h3 class="greeting">{{$user->name}}さんお疲れ様です!</h3>
    <div class="buttons">
      <div class="work-button">
        <form action="/attendance/start" method="get">
          @csrf
          <button class="work-start btn">勤務開始</button>
        </form>
        <form action="/attendance/end" method="get">
          @csrf
          <button class="work-end btn">勤務終了</button>
        </form>
      </div>
      <div class="rest-button">
        <form action="/rest/start" method="get">
          @csrf
          <button class="rest-start btn">休憩開始</button>
        </form>
        <form action="/rest/end" method="get">
          @csrf
          <button class="rest-end btn">休憩終了</button>
        </form>
      </div>
    </div>
  </main>
  <footer class="footer">
    <small>Atte,inc.</small>
  </footer>
  <script></script>
</body>
</html>