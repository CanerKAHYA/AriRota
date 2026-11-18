<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kayıt Ol — ArıRota</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
  <style>
    :root{--ink:#203229;--muted:#647268;--paper:#f7f5ee;--card:#fffefa;--line:#dbe0d5;--sage:#e9eee2;--moss:#31533a;--honey:#d88922;--danger:#b95240;--shadow:0 18px 45px rgba(37,52,39,.09)}
    *{box-sizing:border-box}
    body{margin:0;min-height:100vh;color:var(--ink);background:var(--paper);font-family:"DM Sans",sans-serif;display:grid;place-items:center;padding:24px}
    .card{width:min(100%,480px);background:var(--card);border:1px solid var(--line);border-radius:18px;padding:30px;box-shadow:var(--shadow)}
    .brand{display:flex;align-items:center;gap:11px;text-decoration:none;color:var(--ink);margin-bottom:20px}
    .brand-mark{width:35px;height:35px;border-radius:50% 50% 48% 48%;background:var(--honey);display:grid;place-items:center;color:#fff;font-size:18px}
    .brand strong{display:block;font-size:20px;line-height:1}.brand small{display:block;margin-top:4px;color:var(--muted);font-size:10px;letter-spacing:.12em;text-transform:uppercase}
    h1{margin:0 0 6px;font:600 28px/1.1 "Playfair Display",serif}
    p.sub{margin:0 0 20px;color:var(--muted);font-size:14px}
    .field{margin-bottom:13px}
    .field label{display:block;font-size:12px;font-weight:700;color:#59665b;margin-bottom:5px}
    .field input,.field select{width:100%;padding:10px 12px;border:1px solid var(--line);border-radius:9px;background:#fff;font:inherit;outline:0}
    .field input:focus,.field select:focus{border-color:#86a18a;box-shadow:0 0 0 3px rgba(134,161,138,.15)}
    .two{display:grid;grid-template-columns:1fr 1fr;gap:10px}
    .primary{width:100%;border:0;color:#fff;background:var(--moss);border-radius:10px;padding:12px 16px;font:inherit;font-size:14px;font-weight:700;cursor:pointer;margin-top:6px}
    .primary:hover{background:#24442d}
    .err{background:#f7e4df;color:#9c4132;border-radius:9px;padding:10px 12px;font-size:13px;margin-bottom:16px}
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
    <h1>Yeni hesap oluştur</h1>
    <p class="sub">Rolünüzü seçerek ArıRota ağına katılın.</p>

    @if ($errors->any())
      <div class="err">
        @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
      @csrf
      <div class="field">
        <label for="name">Ad Soyad</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required placeholder="Caner Kahya">
      </div>
      <div class="field">
        <label for="email">E-posta</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="caner@arirota.test">
      </div>
      <div class="field">
        <label for="role">Kullanıcı Rolü</label>
        <select id="role" name="role">
          <option value="beekeeper" @selected(old('role') === 'beekeeper')>Gezgin Arıcı</option>
          <option value="admin" @selected(old('role') === 'admin')>Birlik Yöneticisi</option>
        </select>
      </div>
      <div class="two">
        <div class="field">
          <label for="city">İl</label>
          <input id="city" type="text" name="city" value="{{ old('city') }}" placeholder="Manisa">
        </div>
        <div class="field">
          <label for="phone">Telefon</label>
          <input id="phone" type="text" name="phone" value="{{ old('phone') }}" placeholder="05XX XXX XX XX">
        </div>
      </div>
      <div class="two">
        <div class="field">
          <label for="password">Şifre (en az 8 karakter)</label>
          <input id="password" type="password" name="password" required>
        </div>
        <div class="field">
          <label for="password_confirmation">Şifre Tekrar</label>
          <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>
      </div>
      <button type="submit" class="primary">Kayıt ol</button>
    </form>

    <div class="foot">
      Zaten hesabınız var mı? <a href="{{ route('login') }}">Giriş yapın</a> · <a href="{{ route('dashboard') }}">Haritaya dön</a>
    </div>
  </div>
</body>
</html>
