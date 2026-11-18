<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Giriş Yap — ArıRota</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
  <style>
    :root{--ink:#203229;--muted:#647268;--paper:#f7f5ee;--card:#fffefa;--line:#dbe0d5;--sage:#e9eee2;--moss:#31533a;--honey:#d88922;--danger:#b95240;--shadow:0 18px 45px rgba(37,52,39,.09)}
    *{box-sizing:border-box}
    body{margin:0;min-height:100vh;color:var(--ink);background:var(--paper);font-family:"DM Sans",sans-serif;display:grid;place-items:center;padding:24px}
    .card{width:min(100%,440px);background:var(--card);border:1px solid var(--line);border-radius:18px;padding:30px;box-shadow:var(--shadow)}
    .brand{display:flex;align-items:center;gap:11px;text-decoration:none;color:var(--ink);margin-bottom:22px}
    .brand-mark{width:35px;height:35px;border-radius:50% 50% 48% 48%;background:var(--honey);display:grid;place-items:center;color:#fff;font-size:18px}
    .brand strong{display:block;font-size:20px;line-height:1}.brand small{display:block;margin-top:4px;color:var(--muted);font-size:10px;letter-spacing:.12em;text-transform:uppercase}
    h1{margin:0 0 6px;font:600 28px/1.1 "Playfair Display",serif}
    p.sub{margin:0 0 22px;color:var(--muted);font-size:14px}
    .field{margin-bottom:14px}
    .field label{display:block;font-size:12px;font-weight:700;color:#59665b;margin-bottom:6px}
    .field input{width:100%;padding:11px 12px;border:1px solid var(--line);border-radius:9px;background:#fff;font:inherit;outline:0}
    .field input:focus{border-color:#86a18a;box-shadow:0 0 0 3px rgba(134,161,138,.15)}
    .check{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted);margin-bottom:18px}
    .primary{width:100%;border:0;color:#fff;background:var(--moss);border-radius:10px;padding:12px 16px;font:inherit;font-size:14px;font-weight:700;cursor:pointer}
    .primary:hover{background:#24442d}
    .err{background:#f7e4df;color:#9c4132;border-radius:9px;padding:10px 12px;font-size:13px;margin-bottom:16px}
    .demo{margin-top:18px;padding:12px;background:var(--sage);border-radius:10px;font-size:12px;color:#4a5a4d}
    .demo-btns{display:flex;gap:8px;margin-top:8px}
    .demo-btn{flex:1;border:1px solid var(--line);background:#fff;color:var(--moss);border-radius:8px;padding:7px 10px;font:inherit;font-size:12px;font-weight:600;cursor:pointer}
    .demo-btn:hover{background:#f2f6ee}
    .foot{margin-top:18px;text-align:center;font-size:13px;color:var(--muted)}
    .foot a{color:var(--moss);font-weight:700;text-decoration:none}
  </style>
</head>
<body>
  <div class="card">
    <a class="brand" href="{{ route('dashboard') }}">
      <span class="brand-mark">♢</span>
      <span><strong>ArıRota</strong><small>Gezginci arıcılık atlası</small></span>
    </a>
    <h1>Tekrar hoş geldiniz</h1>
    <p class="sub">Kovan konumlarınızı ve bölge kotalarını yönetmek için giriş yapın.</p>

    @if ($errors->any())
      <div class="err">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm">
      @csrf
      <div class="field">
        <label for="email">E-posta</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="ornek@arirota.test">
      </div>
      <div class="field">
        <label for="password">Şifre</label>
        <input id="password" type="password" name="password" required placeholder="••••••••">
      </div>
      <label class="check">
        <input type="checkbox" name="remember"> Oturumu açık tut
      </label>
      <button type="submit" class="primary">Giriş yap</button>
    </form>

    <div class="demo">
      <strong>Demo Hesapları (Şifre: <code>password</code>)</strong>
      <div class="demo-btns">
        <button type="button" class="demo-btn" onclick="fillDemo('arici@arirota.test')">Gezgin Arıcı</button>
        <button type="button" class="demo-btn" onclick="fillDemo('admin@arirota.test')">Birlik Yöneticisi</button>
      </div>
    </div>

    <div class="foot">
      Hesabınız yok mu? <a href="{{ route('register') }}">Kayıt olun</a> · <a href="{{ route('dashboard') }}">Haritaya dön</a>
    </div>
  </div>
  <script>
    function fillDemo(email) {
      document.getElementById('email').value = email;
      document.getElementById('password').value = 'password';
    }
  </script>
</body>
</html>
