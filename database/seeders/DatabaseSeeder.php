<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\Flora;
use App\Models\Hive;
use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@arirota.test'],
            [
                'name' => 'Ege Arıcılar Birliği Yöneticisi',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'city' => 'İzmir',
                'phone' => '0232 444 01 01',
                'bio' => 'Ege Bölgesi konaklama kotaları ve saha uyarılarından sorumlu birlik yöneticisi.',
            ]
        );

        $beekeeper = User::updateOrCreate(
            ['email' => 'arici@arirota.test'],
            [
                'name' => 'Caner Kahya',
                'password' => Hash::make('password'),
                'role' => 'beekeeper',
                'city' => 'Manisa',
                'phone' => '0532 555 01 45',
                'bio' => 'Ege ve Akdeniz rotalarında gezginci arıcılık yapan üretici.',
            ]
        );

        $floras = [
            'narenciye' => Flora::updateOrCreate(
                ['name' => 'Narenciye'],
                ['season' => 'ilkbahar', 'icon' => '🌼', 'description' => 'Bahar başlangıcında yoğun nektar ve polen akışı sağlayan narenciye bahçeleri.']
            ),
            'kekik' => Flora::updateOrCreate(
                ['name' => 'Kekik & Yayla Çiçeği'],
                ['season' => 'yaz', 'icon' => '🌿', 'description' => 'Yüksek rakımlı yaylalarda yaz aylarında açan aromatik bitki örtüsü.']
            ),
            'kestane' => Flora::updateOrCreate(
                ['name' => 'Kestane'],
                ['season' => 'yaz', 'icon' => '🌰', 'description' => 'Nemli vadi ve dağ eteklerinde Haziran ayında yoğunlaşan kestane florası.']
            ),
            'cam' => Flora::updateOrCreate(
                ['name' => 'Çam Balı (Basra)'],
                ['season' => 'sonbahar', 'icon' => '🌲', 'description' => 'Kızılçam ormanlarında sonbahar boyunca süren salgı balı sezonu.']
            ),
        ];

        $regionsData = [
            [
                'name' => 'Spil Dağı Milli Parkı',
                'province' => 'Manisa',
                'latitude' => 38.5567,
                'longitude' => 27.4342,
                'elevation' => 1250,
                'capacity' => 450,
                'description' => 'Yüksek rakımlı yayla florası, serin gece rüzgârları ve zengin kekik örtüsü.',
                'flora' => ['kekik'],
                'hives' => 310,
            ],
            [
                'name' => 'Ödemiş Bozdağ Yaylası',
                'province' => 'İzmir',
                'latitude' => 38.3522,
                'longitude' => 28.0811,
                'elevation' => 1150,
                'capacity' => 500,
                'description' => 'Kestane ve yayla çiçeklerinin birleştiği geniş konaklama alanı.',
                'flora' => ['kestane', 'kekik'],
                'hives' => 210,
            ],
            [
                'name' => 'Marmaris Çam Ormanları',
                'province' => 'Muğla',
                'latitude' => 36.8549,
                'longitude' => 28.2708,
                'elevation' => 320,
                'capacity' => 800,
                'description' => 'Sonbahar çam balı sezonunun en yoğun konaklama noktalarından biri.',
                'flora' => ['cam'],
                'hives' => 740,
            ],
            [
                'name' => 'Aydın Köşk Narenciye Ovası',
                'province' => 'Aydın',
                'latitude' => 37.8542,
                'longitude' => 28.0519,
                'elevation' => 85,
                'capacity' => 600,
                'description' => 'İlkbahar erken koloni gelişimi için verimli narenciye koridoru.',
                'flora' => ['narenciye'],
                'hives' => 180,
            ],
            [
                'name' => 'Simav Dağı Yaylası',
                'province' => 'Kütahya',
                'latitude' => 39.0889,
                'longitude' => 28.9772,
                'elevation' => 1420,
                'capacity' => 350,
                'description' => 'Düşük yoğunluklu, temiz hava akışına sahip yaz dönemi rotası.',
                'flora' => ['kekik'],
                'hives' => 90,
            ],
            [
                'name' => 'Seferihisar Mandalina Vadisi',
                'province' => 'İzmir',
                'latitude' => 38.1969,
                'longitude' => 26.8386,
                'elevation' => 60,
                'capacity' => 400,
                'description' => 'Kıyı rüzgârlarına açık, erken bahar mandalina ve portakal bahçeleri.',
                'flora' => ['narenciye'],
                'hives' => 150,
            ],
        ];

        foreach ($regionsData as $index => $item) {
            $region = Region::updateOrCreate(
                ['name' => $item['name'], 'province' => $item['province']],
                [
                    'latitude' => $item['latitude'],
                    'longitude' => $item['longitude'],
                    'elevation' => $item['elevation'],
                    'capacity' => $item['capacity'],
                    'description' => $item['description'],
                    'is_active' => true,
                ]
            );

            $floraIds = collect($item['flora'])->map(fn ($key) => $floras[$key]->id)->all();
            $region->floras()->sync($floraIds);

            if ($region->hives()->count() === 0) {
                Hive::create([
                    'user_id' => $beekeeper->id,
                    'region_id' => $region->id,
                    'aks_number' => 'TR-AKS-2026-00'.(100 + $index),
                    'hive_count' => $item['hives'],
                    'latitude' => $item['latitude'],
                    'longitude' => $item['longitude'],
                    'status' => 'active',
                    'expires_at' => now()->addDays(25),
                ]);
            }
        }

        $marmaris = Region::where('name', 'Marmaris Çam Ormanları')->first();
        if ($marmaris && $marmaris->alerts()->count() === 0) {
            Alert::create([
                'region_id' => $marmaris->id,
                'user_id' => $admin->id,
                'type' => 'capacity',
                'severity' => 'danger',
                'title' => 'Yüksek Kovan Yoğunluğu',
                'message' => 'Bölge kapasitesi %92 doluluğa ulaştı. Yeni kovan yerleştirmeden önce alternatif rotaları inceleyin.',
                'is_active' => true,
            ]);
        }

        $aydin = Region::where('name', 'Aydın Köşk Narenciye Ovası')->first();
        if ($aydin && $aydin->alerts()->count() === 0) {
            Alert::create([
                'region_id' => $aydin->id,
                'user_id' => $admin->id,
                'type' => 'pesticide',
                'severity' => 'warning',
                'title' => 'Zirai İlaçlama Takibi',
                'message' => 'Ovadaki bahçelerde akşam saatlerinde ilaçlama planlanıyor; kovan uçuş saatlerine dikkat edin.',
                'is_active' => true,
            ]);
        }
    }
}
