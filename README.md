# ArıRota: Akıllı Kovan Konumlandırma ve Karar Destek Platformu

Ege Bölgesi'ndeki gezginci arıcılar ve Arı Yetiştiricileri Birlikleri için geliştirilmiş; konaklama kotalarını, mevsimsel flora takvimini, anlık hava durumunu ve zirai ilaçlama/yoğunluk uyarılarını tek bir interaktif harita üzerinde birleştiren web tabanlı karar destek platformu.

- **GitHub Repo:** [https://github.com/CanerKAHYA/AriRota](https://github.com/CanerKAHYA/AriRota)
- **Canlı Yayın (Render / Docker):** [https://arirota.onrender.com/](https://arirota.onrender.com/)
- **Teknoloji Yığını:** Laravel 12 (PHP 8.4), SQLite, Leaflet.js, Alpine.js, Chart.js, Docker

---

## Proje Ekibi

| Öğrenci No | Ad Soyad | GitHub | Rol / Sorumluluk |
| --- | --- | --- | --- |
| 2516013038 | **Caner Kahya** | [@CanerKAHYA](https://github.com/CanerKAHYA) | Ö1 — M1 (Kovan Yönetimi), M3 (İnteraktif Harita ve Flora İzleme) |
| 2516013025 | **Muhammet Yusuf Coşkun** | [@yusufcosk](https://github.com/yusufcosk) | Ö2 — M2 (Bölge ve Kota Yönetimi), M4 (İklim, İlaçlama Uyarı ve Karar Destek) |

---

## Modül ve Sorumluluk Tablosu

| Kod / Modül Adı | İşlemler ve İş Kuralı | Dosya / Görünüm | Sorumlu |
| --- | --- | --- | --- |
| **M1 / Kovan Yönetimi** | Aktif kovan konumu ekleme, listeleme ve silme (CRUD). **İş kuralı:** Bölge maksimum kotasını aşan veya 0 ve altında girilen kovan kaydı reddedilir. | `HiveController.php` & `profile/edit.blade.php` | Ö1 (Caner Kahya) |
| **M2 / Bölge ve Kota Yönetimi** | Konaklama bölgelerini ve mevsimsel flora eşleşmelerini ekleme, kota güncelleme ve doluluk raporu oluşturma. **İş kuralı:** Kota 0'dan küçük olamaz ve mevcut aktif kovan sayısının altına düşürülemez. | `AdminController.php` & `admin/index.blade.php` | Ö2 (Muhammet Yusuf Coşkun) |
| **M3 / İnteraktif Harita ve Flora İzleme** | Bölge doluluk oranlarını, kovan yoğunluğunu ve bitki türlerini Leaflet.js haritasında filtreleme ve listeleme. **İş kuralı:** Yalnızca aktif ve 30 günlük geçerlilik süresi dolmamış kayıtlar yoğunluk hesabına dahil edilir. | `MapDashboardController.php` & `dashboard.blade.php` | Ö1 (Caner Kahya) |
| **M4 / İklim, İlaçlama Uyarı ve Karar Destek** | Bölge hava durumu ve pestisit bildirimlerini kaydetme/okuma, Chart.js ile yoğunluk grafiği üretme, Yeşil/Sarı/Kırmızı uygunluk kartı ve CSV/yazdırma raporu. **İş kuralı:** Eşik dışı yoğunluk veya aktif tehlikeli uyarı varsa bölge otomatik Riskli (Kırmızı) işaretlenir. | `AdminController.php` & `admin/index.blade.php` | Ö2 (Muhammet Yusuf Coşkun) |

---

## Veritabanı Mimarisi (6 İlişkili Tablo)

1. **`users`**: Gezgin Arıcı (`beekeeper`) ve Birlik Yöneticisi (`admin`) kullanıcı hesapları ve profil bilgileri.
2. **`regions`**: Ege Bölgesi konaklama noktaları, koordinatlar, rakım ve maksimum kovan kotaları.
3. **`floras`**: Mevsimsel bitki türleri (`ilkbahar`, `yaz`, `sonbahar`) ve ikon/açıklama bilgileri.
4. **`flora_region`**: Bölge ve flora türleri arasındaki `N:M` ara tablosu.
5. **`hives`**: Arıcıların oluşturduğu 30 gün geçerli aktif kovan konaklama bildirimleri (`aks_number`, `hive_count`, `expires_at`).
6. **`alerts`**: Bölgesel zirai ilaçlama, hava durumu ve kapasite uyarıları.

---

## Kurulum ve Çalıştırma

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve --port=8001
```

### Demo Hesapları

- **Gezgin Arıcı:** `arici@arirota.test` / Şifre: `password`
- **Birlik Yöneticisi (Admin):** `admin@arirota.test` / Şifre: `password`
