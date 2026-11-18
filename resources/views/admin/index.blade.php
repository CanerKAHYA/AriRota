<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Birlik Yönetim Paneli — ArıRota</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
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
    .btn-outline{border:1px solid var(--line);background:#fff;color:var(--ink);border-radius:9px;padding:8px 13px;font:inherit;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px}
    .btn-primary{border:0;color:#fff;background:var(--moss);border-radius:10px;padding:10px 16px;font:inherit;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px}
    .btn-primary:hover{background:#24442d}
    main{max-width:1360px;margin:auto;padding:28px clamp(18px,4vw,54px) 52px}
    .head{display:flex;justify-content:space-between;align-items:end;gap:16px;flex-wrap:wrap;margin-bottom:22px}
    .eyebrow{color:#7a8a7b;font:500 11px "DM Mono",monospace;text-transform:uppercase;letter-spacing:.1em}
    .head h1{margin:6px 0 0;font:600 32px/1.1 "Playfair Display",serif}
    .kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px}
    .kpi{background:var(--card);border:1px solid var(--line);border-radius:15px;padding:18px}
    .kpi small{color:var(--muted);font-size:12px;display:block}
    .kpi b{margin-top:8px;display:block;font:600 26px/1 "Playfair Display",serif}
    .grid-2{display:grid;grid-template-columns:1.35fr 1fr;gap:18px;margin-bottom:20px}
    .card{background:var(--card);border:1px solid var(--line);border-radius:16px;padding:22px;box-shadow:var(--shadow);margin-bottom:18px}
    .card h2{margin:0 0 14px;font:600 21px/1.2 "Playfair Display",serif}
    .ok{background:#e5f1e4;color:#2e5b37;border-radius:10px;padding:12px 14px;font-size:13px;margin-bottom:16px}
    .err{background:#f7e4df;color:#9c4132;border-radius:10px;padding:12px 14px;font-size:13px;margin-bottom:16px}
    table{width:100%;border-collapse:collapse;font-size:13px}
    th,td{text-align:left;padding:10px 8px;border-bottom:1px solid var(--line);vertical-align:middle}
    th{color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.06em}
    .badge{display:inline-flex;padding:3px 9px;border-radius:999px;font-size:11px;font-weight:700}
    .badge.ok{background:#e5f1e4;color:#376941;margin:0}
    .badge.warn{background:var(--honey-soft);color:#9b641a}
    .badge.danger{background:#f7e4df;color:#9c4132}
    .field{margin-bottom:12px}
    .field label{display:block;font-size:12px;font-weight:700;color:#59665b;margin-bottom:4px}
    .field input,.field select,.field textarea{width:100%;padding:9px 11px;border:1px solid var(--line);border-radius:8px;background:#fff;font:inherit;font-size:13px;outline:0}
    .two{display:grid;grid-template-columns:1fr 1fr;gap:10px}
    .inline-form{display:flex;gap:6px;align-items:center}
    .inline-form input[type="number"]{width:84px;padding:6px 8px;border:1px solid var(--line);border-radius:7px;font:inherit;font-size:12px}
    .inline-form button{padding:6px 10px;font-size:12px}
    @media(max-width:1020px){.kpis{grid-template-columns:1fr 1fr}.grid-2{grid-template-columns:1fr}}
    @media print{.topbar,.no-print{display:none!important}body{background:#fff}.card{box-shadow:none}}
  </style>
</head>
<body>
  <header class="topbar">
    <a class="brand" href="{{ route('dashboard') }}">
      <span class="brand-mark">♢</span>
      <span><strong>ArıRota</strong><small>Birlik Yönetim Paneli</small></span>
    </a>
    <div class="nav-actions">
      <a class="btn-link" href="{{ route('dashboard') }}">← Haritaya Dön</a>
      <a class="btn-outline" href="{{ route('profile.edit') }}">Profilim</a>
      <a class="btn-outline" href="{{ route('admin.regions.export') }}">CSV İndir</a>
      <button type="button" class="btn-outline" onclick="window.print()">Yazdır</button>
      <form method="POST" action="{{ route('logout') }}" style="margin:0">
        @csrf
        <button type="submit" class="btn-outline">Çıkış</button>
      </form>
    </div>
  </header>

  <main>
    <div class="head">
      <div>
        <div class="eyebrow">Birlik Yöneticisi · Karar Destek & Kota</div>
        <h1>Bölgesel Yoğunluk ve Uyarı Yönetimi</h1>
      </div>
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

    @php($totalCapacity = (int) $regions->sum('capacity'))
    @php($activeAlerts = $regions->sum(fn ($r) => $r->alerts->count()))
    <section class="kpis">
      <div class="kpi"><small>Aktif Bölge Sayısı</small><b>{{ $regions->count() }}</b></div>
      <div class="kpi"><small>Toplam Aktif Kovan</small><b>{{ $hiveTotal }}</b></div>
      <div class="kpi"><small>Toplam Kota Kapasitesi</small><b>{{ $totalCapacity }}</b></div>
      <div class="kpi"><small>Aktif Saha Uyarıları</small><b>{{ $activeAlerts }}</b></div>
    </section>

    <div class="grid-2">
      <section class="card">
        <h2>Bölge Doluluk ve Kapasite Grafiği</h2>
        <canvas id="occupancyChart" height="160"></canvas>
      </section>

      <section class="card no-print">
        <h2>İklim / İlaçlama Uyarısı Yayınla</h2>
        <form method="POST" action="{{ route('admin.alerts.store') }}">
          @csrf
          <div class="field">
            <label for="alert_region">Bölge</label>
            <select id="alert_region" name="region_id" required>
              @foreach ($regions as $region)
                <option value="{{ $region->id }}">{{ $region->name }} ({{ $region->province }})</option>
              @endforeach
            </select>
          </div>
          <div class="two">
            <div class="field">
              <label for="alert_type">Uyarı Türü</label>
              <select id="alert_type" name="type" required>
                <option value="pesticide">Zirai İlaçlama</option>
                <option value="weather">Hava Koşulu</option>
                <option value="capacity">Kapasite / Yoğunluk</option>
                <option value="other">Diğer</option>
              </select>
            </div>
            <div class="field">
              <label for="alert_severity">Önem Derecesi</label>
              <select id="alert_severity" name="severity" required>
                <option value="warning">İzleme (Sarı)</option>
                <option value="danger">Riskli (Kırmızı)</option>
                <option value="info">Bilgi</option>
              </select>
            </div>
          </div>
          <div class="field">
            <label for="alert_title">Başlık</label>
            <input id="alert_title" type="text" name="title" required placeholder="Örn: Akşam saatlerinde ilaçlama riski">
          </div>
          <div class="field">
            <label for="alert_msg">Açıklama</label>
            <textarea id="alert_msg" name="message" rows="2" required placeholder="Arıcılara iletilecek kısa karar destek notu..."></textarea>
          </div>
          <button type="submit" class="btn-primary">Uyarıyı yayınla</button>
        </form>
      </section>
    </div>

    <section class="card">
      <h2>Bölge ve Kota Listesi</h2>
      <div style="overflow:auto">
        <table>
          <thead>
            <tr>
              <th>Bölge</th>
              <th>İl</th>
              <th>Flora</th>
              <th>Dolu / Kota</th>
              <th>Durum</th>
              <th class="no-print">Hızlı Kota Güncelle</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($regions as $region)
              @php($occ = (int) $region->occupied)
              @php($pct = $region->capacity > 0 ? round(($occ / $region->capacity) * 100) : 100)
              <tr>
                <td><strong>{{ $region->name }}</strong><br><small style="color:var(--muted)">{{ $region->elevation }} m</small></td>
                <td>{{ $region->province }}</td>
                <td>{{ $region->floras->pluck('name')->join(', ') ?: '—' }}</td>
                <td>{{ $occ }} / {{ $region->capacity }} (%{{ $pct }})</td>
                <td>
                  @if ($pct >= 90 || $region->alerts->contains('severity', 'danger'))
                    <span class="badge danger">Riskli</span>
                  @elseif ($pct >= 65 || $region->alerts->isNotEmpty())
                    <span class="badge warn">İzleme</span>
                  @else
                    <span class="badge ok">Uygun</span>
                  @endif
                </td>
                <td class="no-print">
                  <form method="POST" action="{{ route('admin.regions.update', $region) }}" class="inline-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="name" value="{{ $region->name }}">
                    <input type="hidden" name="province" value="{{ $region->province }}">
                    <input type="hidden" name="latitude" value="{{ $region->latitude }}">
                    <input type="hidden" name="longitude" value="{{ $region->longitude }}">
                    <input type="hidden" name="elevation" value="{{ $region->elevation }}">
                    <input type="hidden" name="description" value="{{ $region->description }}">
                    <input type="hidden" name="is_active" value="{{ $region->is_active ? 1 : 0 }}">
                    <input type="number" name="capacity" value="{{ $region->capacity }}" min="{{ $occ }}" max="10000">
                    <button type="submit" class="btn-outline">Güncelle</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </section>

    <div class="grid-2">
      <section class="card no-print">
        <h2>Yeni Konaklama Bölgesi Ekle</h2>
        <form method="POST" action="{{ route('admin.regions.store') }}">
          @csrf
          <div class="two">
            <div class="field">
              <label>Bölge Adı</label>
              <input type="text" name="name" required placeholder="Örn: Kazdağları Güney Yamacı">
            </div>
            <div class="field">
              <label>İl</label>
              <input type="text" name="province" required placeholder="Balıkesir">
            </div>
          </div>
          <div class="two">
            <div class="field">
              <label>Enlem (35-43)</label>
              <input type="number" step="0.0001" name="latitude" required placeholder="39.6500">
            </div>
            <div class="field">
              <label>Boylam (25-33)</label>
              <input type="number" step="0.0001" name="longitude" required placeholder="26.8500">
            </div>
          </div>
          <div class="two">
            <div class="field">
              <label>Rakım (m)</label>
              <input type="number" name="elevation" required value="650">
            </div>
            <div class="field">
              <label>Maksimum Kovan Kotası</label>
              <input type="number" name="capacity" required value="350">
            </div>
          </div>
          <div class="field">
            <label>Açıklama</label>
            <input type="text" name="description" placeholder="Bölge hakkında kısa not">
          </div>
          <input type="hidden" name="is_active" value="1">
          <button type="submit" class="btn-primary">Bölgeyi ekle</button>
        </form>
      </section>

      <section class="card">
        <h2>Kayıtlı Kullanıcılar</h2>
        <table>
          <thead>
            <tr>
              <th>Ad Soyad</th>
              <th>E-posta</th>
              <th>İl</th>
              <th>Rol</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($users as $u)
              <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->city ?? '—' }}</td>
                <td>
                  <span class="badge {{ $u->isAdmin() ? 'warn' : 'ok' }}">
                    {{ $u->isAdmin() ? 'Birlik Yöneticisi' : 'Gezgin Arıcı' }}
                  </span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </section>
    </div>
  </main>

  <script>
    const ctx = document.getElementById('occupancyChart');
    if (ctx) {
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: @json($regions->pluck('name')),
          datasets: [
            {
              label: 'Dolu Kovan',
              data: @json($regions->map(fn ($r) => (int) $r->occupied)),
              backgroundColor: '#d88922',
              borderRadius: 6
            },
            {
              label: 'Toplam Kota',
              data: @json($regions->pluck('capacity')),
              backgroundColor: '#31533a',
              borderRadius: 6
            }
          ]
        },
        options: {
          responsive: true,
          plugins: { legend: { position: 'bottom' } },
          scales: { y: { beginAtZero: true } }
        }
      });
    }
  </script>
</body>
</html>
