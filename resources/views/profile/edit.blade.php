<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Profilim — ArıRota</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
  <style>
    :root{--ink:#203229;--muted:#647268;--paper:#f7f5ee;--card:#fffefa;--line:#dbe0d5;--sage:#e9eee2;--moss:#31533a;--honey:#d88922;--honey-soft:#faefcf;--danger:#b95240;--shadow:0 18px 45px rgba(37,52,39,.09)}
    *{box-sizing:border-box}
    body{margin:0;color:var(--ink);background:var(--paper);font-family:"DM Sans",sans-serif}
    .topbar{height:74px;padding:0 clamp(18px,4vw,54px);display:flex;align-items:center;justify-content:space-between;gap:16px;border-bottom:1px solid var(--line);background:rgba(255,254,250,.94);position:sticky;top:0;z-index:100;backdrop-filter:blur(14px)}
    .brand{display:flex;align-items:center;gap:11px;text-decoration:none;color:var(--ink)}
    .brand-mark{width:35px;height:35px;border-radius:50% 50% 48% 48%;background:var(--honey);display:grid;place-items:center;color:#fff;font-size:18px}
    .brand strong{display:block;font-size:20px;line-height:1}.brand small{display:block;margin-top:4px;color:var(--muted);font-size:10px;letter-spacing:.12em;text-transform:uppercase}
    .nav-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
    .btn-link{text-decoration:none;color:var(--moss);font-weight:700;font-size:13px;padding:8px 12px;border-radius:9px}
    .btn-link:hover{background:var(--sage)}
    .btn-outline{border:1px solid var(--line);background:#fff;color:var(--ink);border-radius:9px;padding:8px 13px;font:inherit;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none}
    .btn-primary{border:0;color:#fff;background:var(--moss);border-radius:10px;padding:11px 18px;font:inherit;font-size:13px;font-weight:700;cursor:pointer}
    .btn-primary:hover{background:#24442d}
    .btn-danger{border:1px solid #ecc6be;background:#fdf3f1;color:var(--danger);border-radius:8px;padding:6px 10px;font:inherit;font-size:12px;font-weight:600;cursor:pointer}
    main{max-width:1120px;margin:auto;padding:28px clamp(18px,4vw,54px) 48px}
    .head{display:flex;justify-content:space-between;align-items:end;gap:16px;flex-wrap:wrap;margin-bottom:22px}
    .eyebrow{color:#7a8a7b;font:500 11px "DM Mono",monospace;text-transform:uppercase;letter-spacing:.1em}
    .head h1{margin:6px 0 0;font:600 32px/1.1 "Playfair Display",serif}
    .role-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:700}
    .role-badge.admin{background:var(--honey-soft);color:#9b641a}
    .role-badge.beekeeper{background:#e5f1e4;color:#376941}
    .grid{display:grid;grid-template-columns:1.05fr 1fr;gap:18px;align-items:start}
    .card{background:var(--card);border:1px solid var(--line);border-radius:16px;padding:24px;box-shadow:var(--shadow)}
    .card h2{margin:0 0 16px;font:600 22px/1.2 "Playfair Display",serif}
    .field{margin-bottom:14px}
    .field label{display:block;font-size:12px;font-weight:700;color:#59665b;margin-bottom:5px}
    .field input,.field textarea{width:100%;padding:10px 12px;border:1px solid var(--line);border-radius:9px;background:#fff;font:inherit;outline:0}
    .field input:focus,.field textarea:focus{border-color:#86a18a;box-shadow:0 0 0 3px rgba(134,161,138,.15)}
    .two{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    .ok{background:#e5f1e4;color:#2e5b37;border-radius:10px;padding:12px 14px;font-size:13px;margin-bottom:18px}
    .err{background:#f7e4df;color:#9c4132;border-radius:10px;padding:12px 14px;font-size:13px;margin-bottom:18px}
    .hive-item{border:1px solid var(--line);border-radius:12px;padding:14px;background:#fbfbf7;margin-bottom:10px;display:flex;justify-content:space-between;align-items:center;gap:12px}
    .hive-item b{display:block;font-size:14px}
    .hive-item span{color:var(--muted);font-size:12px}
    .empty{color:var(--muted);font-size:13px;padding:18px;background:var(--sage);border-radius:10px;text-align:center}
    @media(max-width:860px){.grid{grid-template-columns:1fr}}
  </style>
</head>
<body>
  <header class="topbar">
    <a class="brand" href="{{ route('dashboard') }}">
      <span class="brand-mark">♢</span>
      <span><strong>ArıRota</strong><small>Profil & Kovanlarım</small></span>
    </a>
    <div class="nav-actions">
      <a class="btn-link" href="{{ route('dashboard') }}">← Haritaya Dön</a>
      @if ($user->isAdmin())
        <a class="btn-outline" href="{{ route('admin.index') }}">Yönetim Paneli</a>
      @endif
      <form method="POST" action="{{ route('logout') }}" style="margin:0">
        @csrf
        <button type="submit" class="btn-outline">Çıkış yap</button>
      </form>
    </div>
  </header>

  <main>
    <div class="head">
      <div>
        <div class="eyebrow">Hesap Yönetimi</div>
        <h1>{{ $user->name }}</h1>
      </div>
      <span class="role-badge {{ $user->isAdmin() ? 'admin' : 'beekeeper' }}">
        {{ $user->isAdmin() ? 'Birlik Yöneticisi' : 'Gezgin Arıcı' }}
      </span>
    </div>

    @if (session('success'))
      <div class="ok">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
      <div class="err">
        @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <div class="grid">
      <section class="card">
        <h2>Profil Bilgileri</h2>
        <form method="POST" action="{{ route('profile.update') }}">
          @csrf
          @method('PUT')
          <div class="field">
            <label for="name">Ad Soyad</label>
            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required>
          </div>
          <div class="field">
            <label for="email">E-posta</label>
            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
          </div>
          <div class="two">
            <div class="field">
              <label for="city">İl</label>
              <input id="city" type="text" name="city" value="{{ old('city', $user->city) }}" placeholder="Manisa">
            </div>
            <div class="field">
              <label for="phone">Telefon</label>
              <input id="phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="05XX XXX XX XX">
            </div>
          </div>
          <div class="field">
            <label for="bio">Kısa Not / Biyografi</label>
            <textarea id="bio" name="bio" rows="3" placeholder="Arıcılık deneyiminiz veya tercih ettiğiniz rotalar...">{{ old('bio', $user->bio) }}</textarea>
          </div>
          <button type="submit" class="btn-primary">Değişiklikleri kaydet</button>
        </form>
      </section>

      <section class="card">
        <h2>Aktif Kovan Kayıtlarım</h2>
        @php($hives = $user->hives()->with('region')->latest()->get())
        @forelse ($hives as $hive)
          <div class="hive-item">
            <div>
              <b>{{ $hive->region?->name ?? 'Bölge' }} ({{ $hive->hive_count }} kovan)</b>
              <span>AKS: {{ $hive->aks_number }} · Durum: {{ $hive->status === 'active' ? 'Aktif' : 'Süresi Doldu' }}</span>
              @if ($hive->expires_at)
                <br><span>Geçerlilik: {{ $hive->expires_at->format('d.m.Y') }}</span>
              @endif
            </div>
            <form method="POST" action="{{ route('hives.destroy', $hive) }}" onsubmit="return confirm('Bu kovan kaydını kaldırmak istediğinize emin misiniz?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn-danger">Kaldır</button>
            </form>
          </div>
        @empty
          <div class="empty">
            Henüz kayıtlı bir kovan konumunuz yok. <a href="{{ route('dashboard') }}" style="color:var(--moss);font-weight:700">Haritadan konum ekleyin</a>.
          </div>
        @endforelse
      </section>
    </div>
  </main>
</body>
</html>
