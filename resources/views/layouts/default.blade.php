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
    @yield('main')
    
  </main>

  <footer class="footer">
    <small class="footer-logo">Atte,inc.</small>
  </footer>

  <script>
    //勤務、休憩開始ボタンの多重クリック防止処理
    const start = document.getElementById('start');
    

    // クリック時にdisabledにすると勤務開始が出来ない
    start.addEventListener('click', () => {
      alert("開始処理中です。お待ちください。");
    });
  </script>
</body>
</html>