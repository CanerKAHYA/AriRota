<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MapDashboardController extends Controller
{
    /**
     * Ege Bölgesi arıcılık lokasyon verisi (Zero-config demo data).
     * Flora, kapasite ve mevsim bilgileri statik; hava durumu Open-Meteo'dan canlı çekilir.
     */
    private function getBaseLocations(): array
    {
        return [
            [
                'id' => 1,
                'baslik' => 'Muğla - Köyceğiz / Marmaris',
                'lat' => 36.9646,
                'lng' => 28.6813,
                'flora' => 'Çam Balı Florası',
                'flora_icon' => '🌲',
                'mevsim' => 'sonbahar',
                'mevsim_label' => '🍂 Sonbahar',
                'rakım' => 450,
                'kapasite_mevcut' => 280,
                'kapasite_max' => 500,
                'onayli_arici' => 11,
                'ilaclama_riski' => false,
                // Fallback hava durumu (API çağrısı başarısız olursa)
                'sicaklik' => 22,
                'nem' => 72,
                'ruzgar' => 8,
            ],
            [
                'id' => 2,
                'baslik' => 'Manisa - Spil Dağı Etekleri',
                'lat' => 38.5870,
                'lng' => 27.5990,
                'flora' => 'Kestane & Kekik Florası',
                'flora_icon' => '🌰',
                'mevsim' => 'yaz',
                'mevsim_label' => '☀️ Yaz',
                'rakım' => 950,
                'kapasite_mevcut' => 430,
                'kapasite_max' => 500,
                'onayli_arici' => 19,
                'ilaclama_riski' => false,
                'sicaklik' => 28,
                'nem' => 55,
                'ruzgar' => 14,
            ],
            [
                'id' => 3,
                'baslik' => 'İzmir - Kemalpaşa / Selçuk',
                'lat' => 38.1580,
                'lng' => 27.4220,
                'flora' => 'Narenciye & Bahar Florası',
                'flora_icon' => '🌸',
                'mevsim' => 'ilkbahar',
                'mevsim_label' => '🌸 İlkbahar',
                'rakım' => 180,
                'kapasite_mevcut' => 320,
                'kapasite_max' => 500,
                'onayli_arici' => 14,
                'ilaclama_riski' => false,
                'sicaklik' => 24,
                'nem' => 65,
                'ruzgar' => 12,
            ],
            [
                'id' => 4,
                'baslik' => 'Aydın - Bozdoğan / Yaylalar',
                'lat' => 37.6652,
                'lng' => 28.3125,
                'flora' => 'Püren & Çiçek Balı Florası',
                'flora_icon' => '🌼',
                'mevsim' => 'yaz',
                'mevsim_label' => '☀️ Yaz',
                'rakım' => 720,
                'kapasite_mevcut' => 460,
                'kapasite_max' => 500,
                'onayli_arici' => 22,
                'ilaclama_riski' => false,
                'sicaklik' => 31,
                'nem' => 42,
                'ruzgar' => 42,
            ],
            [
                'id' => 5,
                'baslik' => 'Denizli - Çivril / Bekilli',
                'lat' => 38.2980,
                'lng' => 29.7340,
                'flora' => 'Lavanta & Ayçiçeği Florası',
                'flora_icon' => '💜',
                'mevsim' => 'yaz',
                'mevsim_label' => '☀️ Yaz',
                'rakım' => 830,
                'kapasite_mevcut' => 210,
                'kapasite_max' => 500,
                'onayli_arici' => 8,
                'ilaclama_riski' => true,
                'sicaklik' => 26,
                'nem' => 48,
                'ruzgar' => 10,
            ],
        ];
    }

    /**
     * Open-Meteo API'den canlı hava durumu verisi çek.
     * Sonuçlar 15 dakika cache'lenir (API yükünü azaltmak için).
     */
    private function fetchWeatherForLocations(array $locations): array
    {
        // Tüm lokasyonlar için tek batch istek hazırla (Open-Meteo çoklu lat/lng destekler)
        $lats = implode(',', array_column($locations, 'lat'));
        $lngs = implode(',', array_column($locations, 'lng'));

        $cacheKey = 'open_meteo_weather_' . md5($lats . $lngs);

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($locations, $lats, $lngs) {
            try {
                $response = Http::timeout(8)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $lats,
                    'longitude' => $lngs,
                    'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code',
                    'timezone' => 'Europe/Istanbul',
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    // Open-Meteo çoklu koordinat için dizi döndürür
                    // Tekli ise tek obje döndürür — normalize edelim
                    if (isset($data['latitude'])) {
                        // Tekli istek veya single array
                        $data = [$data];
                    }

                    $weatherMap = [];
                    foreach ($data as $i => $entry) {
                        if (isset($entry['current'])) {
                            $current = $entry['current'];
                            $weatherMap[$i] = [
                                'sicaklik' => round($current['temperature_2m'] ?? 0),
                                'nem' => round($current['relative_humidity_2m'] ?? 0),
                                'ruzgar' => round($current['wind_speed_10m'] ?? 0),
                                'weather_code' => $current['weather_code'] ?? 0,
                                'api_zaman' => $current['time'] ?? now()->toIso8601String(),
                                'canli' => true,
                            ];
                        }
                    }

                    return $weatherMap;
                }
            } catch (\Exception $e) {
                // API hatası — fallback verileri kullanılacak
                report($e);
            }

            return []; // Boş dön = fallback kullanılır
        });
    }

    /**
     * Canlı hava durumuna göre durum/renk/fırtına hesapla.
     */
    private function computeStatus(array $location): array
    {
        $sicaklik = $location['sicaklik'];
        $nem = $location['nem'];
        $ruzgar = $location['ruzgar'];
        $weatherCode = $location['weather_code'] ?? 0;
        $kapasiteOran = $location['kapasite_mevcut'] / $location['kapasite_max'];

        // Fırtına tespiti: rüzgar > 30 km/s VEYA weather_code 95+ (thunderstorm)
        $firtina = $ruzgar > 30 || $weatherCode >= 95;

        // Durum hesaplama
        if ($firtina || $kapasiteOran >= 0.85 || $location['ilaclama_riski']) {
            $durum = 'riskli';
            $renk = 'kirmizi';

            // Neden oluştur
            $nedenler = [];
            if ($ruzgar > 30) $nedenler[] = "Şiddetli rüzgar ({$ruzgar} km/s)";
            if ($weatherCode >= 95) $nedenler[] = 'Fırtına uyarısı aktif';
            if ($kapasiteOran >= 0.85) $nedenler[] = 'Aşırı kovan yığılması (%' . round($kapasiteOran * 100) . ')';
            if ($location['ilaclama_riski']) $nedenler[] = 'Zirai ilaçlama riski';
            $durum_neden = implode(', ', $nedenler) . '.';
        } elseif ($kapasiteOran >= 0.60 || $sicaklik > 35 || $sicaklik < 10 || $nem > 85) {
            $durum = 'uyari';
            $renk = 'sari';

            $nedenler = [];
            if ($kapasiteOran >= 0.60) $nedenler[] = 'Kapasite %' . round($kapasiteOran * 100) . ' dolu';
            if ($sicaklik > 35) $nedenler[] = "Yüksek sıcaklık ({$sicaklik}°C)";
            if ($sicaklik < 10) $nedenler[] = "Düşük sıcaklık ({$sicaklik}°C)";
            if ($nem > 85) $nedenler[] = "Yüksek nem (%{$nem})";
            $durum_neden = implode(', ', $nedenler) . '. Dikkatli olunmalı.';
        } else {
            $durum = 'uygun';
            $renk = 'yesil';
            $durum_neden = 'Nektar akımı uygun, hava koşulları ideal.';
        }

        return [
            'durum' => $durum,
            'durum_neden' => $durum_neden,
            'firtina' => $firtina,
            'renk' => $renk,
        ];
    }

    /**
     * Tüm lokasyonları canlı hava durumuyla birlikte döndür.
     */
    private function getLocationsWithWeather(): array
    {
        $locations = $this->getBaseLocations();
        $weatherData = $this->fetchWeatherForLocations($locations);

        foreach ($locations as $i => &$loc) {
            // Canlı veri varsa üzerine yaz, yoksa fallback'i koru
            if (isset($weatherData[$i])) {
                $loc['sicaklik'] = $weatherData[$i]['sicaklik'];
                $loc['nem'] = $weatherData[$i]['nem'];
                $loc['ruzgar'] = $weatherData[$i]['ruzgar'];
                $loc['weather_code'] = $weatherData[$i]['weather_code'];
                $loc['api_zaman'] = $weatherData[$i]['api_zaman'];
                $loc['canli'] = true;
            } else {
                $loc['weather_code'] = 0;
                $loc['api_zaman'] = null;
                $loc['canli'] = false;
            }

            // Durum hesapla (canlı veya fallback verisine göre)
            $status = $this->computeStatus($loc);
            $loc = array_merge($loc, $status);
        }

        return $locations;
    }

    /**
     * Ana dashboard sayfasını göster.
     */
    public function index()
    {
        $locations = $this->getLocationsWithWeather();
        return view('dashboard', compact('locations'));
    }

    /**
     * Tüm lokasyon verisini JSON olarak döndür (API).
     */
    public function apiLocations()
    {
        return response()->json($this->getLocationsWithWeather());
    }

    /**
     * Belirli bir lokasyonun detayını döndür (API).
     */
    public function apiLocation(int $id)
    {
        $locations = collect($this->getLocationsWithWeather());
        $location = $locations->firstWhere('id', $id);

        if (!$location) {
            return response()->json(['error' => 'Lokasyon bulunamadı.'], 404);
        }

        return response()->json($location);
    }

    /**
     * Belirli koordinatlar için canlı hava durumu (Open-Meteo proxy).
     * Frontend'in doğrudan da çağırabileceği bir endpoint.
     */
    public function apiWeather(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $lat = $request->query('lat');
        $lng = $request->query('lng');

        $cacheKey = 'weather_single_' . md5("{$lat}_{$lng}");

        $weather = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($lat, $lng) {
            try {
                $response = Http::timeout(8)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code',
                    'timezone' => 'Europe/Istanbul',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $current = $data['current'] ?? [];
                    return [
                        'sicaklik' => round($current['temperature_2m'] ?? 0),
                        'nem' => round($current['relative_humidity_2m'] ?? 0),
                        'ruzgar' => round($current['wind_speed_10m'] ?? 0),
                        'weather_code' => $current['weather_code'] ?? 0,
                        'api_zaman' => $current['time'] ?? null,
                        'canli' => true,
                        'kaynak' => 'Open-Meteo',
                    ];
                }
            } catch (\Exception $e) {
                report($e);
            }

            return ['canli' => false, 'kaynak' => 'fallback'];
        });

        return response()->json($weather);
    }

    /**
     * Yeni kovan konumu ekle (simülasyon).
     */
    public function apiAddHive(Request $request)
    {
        $request->validate([
            'aks_no' => 'required|string|min:5',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'kovan_sayisi' => 'required|integer|min:10|max:500',
        ]);

        // Demo: Gerçek DB'ye kaydetmek yerine başarı mesajı dön
        return response()->json([
            'success' => true,
            'mesaj' => 'Kovan konumu başarıyla eklendi. 30 gün sonra otomatik askıya alınacaktır.',
            'yeni_pin' => [
                'id' => rand(100, 999),
                'lat' => $request->lat,
                'lng' => $request->lng,
                'baslik' => 'Yeni Kovan Noktası',
                'kovan_sayisi' => $request->kovan_sayisi,
                'aks_no' => $request->aks_no,
                'durum' => 'uygun',
                'renk' => 'yesil',
            ],
        ]);
    }

    /**
     * Bölge ihbar bildirimi (simülasyon).
     */
    public function apiReport(Request $request)
    {
        $request->validate([
            'lokasyon_id' => 'required|integer',
            'sorun' => 'required|string',
        ]);

        return response()->json([
            'success' => true,
            'mesaj' => 'İhbarınız alındı. Moderasyon ekibi 24 saat içinde inceleyecektir.',
        ]);
    }
}
